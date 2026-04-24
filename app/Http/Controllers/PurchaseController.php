<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier')
            ->whereIn('status', ['received', 'cancelled'])
            ->latest()
            ->get();

        $suppliers = Supplier::all();
        $products = Product::all();

        // Info Cards Data
        $totalProducts = Product::count();
        $pendingPurchases = Purchase::where('status', 'pending')->count();
        $receivedPurchases = Purchase::where('status', 'received')->count();
        $cancelledPurchases = Purchase::where('status', 'cancelled')->count();

        return view('dashboard.purchases.index', compact('purchases', 'suppliers', 'products', 'totalProducts', 'pendingPurchases', 'receivedPurchases', 'cancelledPurchases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:1',
            'price' => 'required|array',
        ]);

        $cleanPrices = array_map(function ($price) {
            return (float) preg_replace('/[^0-9]/', '', $price);
        }, $request->price);

        DB::beginTransaction();
        try {
            // Generate PO Number
            $datePrefix = Carbon::parse($request->purchase_date)->format('Ymd');
            $last = Purchase::whereDate('purchase_date', $request->purchase_date)->latest('id')->first();
            $seq = $last ? intval(substr($last->purchase_number, -4)) + 1 : 1;
            $poNumber = 'PO-' . $datePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // Calculate Grand Total
            $totalAmount = 0;
            for ($i = 0; $i < count($request->product_id); $i++) {
                $totalAmount += $request->quantity[$i] * $cleanPrices[$i];
            }

            // Create Purchase Header - Status always 'pending' on create
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'purchase_number' => $poNumber,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => 'pending', // Always pending on create
            ]);

            // Create Purchase Items - Stock NOT incremented yet
            for ($i = 0; $i < count($request->product_id); $i++) {
                $qty = $request->quantity[$i];
                $price = $cleanPrices[$i];
                $subtotal = $qty * $price;
                $productId = $request->product_id[$i];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan pembelian berhasil dibuat! Menunggu konfirmasi.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        // Only allow editing if status is 'pending'
        if ($purchase->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Hanya pesanan dengan status Pending yang dapat diedit!',
                ],
                403,
            );
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'product_id' => 'required|array',
            'quantity' => 'required|array',
            'price' => 'required|array',
        ]);

        $cleanPrices = array_map(function ($price) {
            return (float) preg_replace('/[^0-9]/', '', $price);
        }, $request->price);

        DB::beginTransaction();
        try {
            // Delete old items
            $purchase->items()->delete();

            $totalAmount = 0;
            // Create new items
            for ($i = 0; $i < count($request->product_id); $i++) {
                $qty = $request->quantity[$i];
                $price = $cleanPrices[$i];
                $subtotal = $qty * $price;
                $productId = $request->product_id[$i];
                $totalAmount += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update purchase header
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan pembelian berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal update: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'items.product'])->findOrFail($id);
        return response()->json($purchase);
    }

    // NEW: Confirmation Page
    public function confirmation()
    {
        $pendingPurchases = Purchase::with('supplier')->where('status', 'pending')->latest()->get();

        return view('dashboard.purchases.confirmation', compact('pendingPurchases'));
    }

    // NEW: Approve Purchase
    public function approve($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        if ($purchase->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Hanya pesanan Pending yang dapat disetujui!',
                ],
                403,
            );
        }

        DB::beginTransaction();
        try {
            // Update status to received
            $purchase->update(['status' => 'received']);

            // Increment stock for all items
            foreach ($purchase->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil disetujui! Stok produk telah ditambahkan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menyetujui: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // NEW: Cancel Purchase
    public function cancel($id)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Hanya pesanan Pending yang dapat dibatalkan!',
                ],
                403,
            );
        }

        DB::beginTransaction();
        try {
            // Update status to cancelled - Stock is NOT incremented
            $purchase->update(['status' => 'cancelled']);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal membatalkan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
