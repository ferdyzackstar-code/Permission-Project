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
        $purchases = Purchase::with('supplier')->latest()->get();
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('dashboard.purchases.index', compact('purchases', 'suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,received',
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
            $datePrefix = Carbon::parse($request->purchase_date)->format('Ymd');
            $last = Purchase::whereDate('purchase_date', $request->purchase_date)->latest('id')->first();
            $seq = $last ? intval(substr($last->purchase_number, -4)) + 1 : 1;
            $poNumber = 'PO-' . $datePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            for ($i = 0; $i < count($request->product_id); $i++) {
                $totalAmount += $request->quantity[$i] * $cleanPrices[$i];
            }

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'purchase_number' => $poNumber,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => $request->status, 
            ]);

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

                if ($request->status === 'received') {
                    Product::where('id', $productId)->increment('stock', $qty);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pembelian berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,received',
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
            if ($purchase->status === 'received') {
                foreach ($purchase->items as $oldItem) {
                    Product::where('id', $oldItem->product_id)->decrement('stock', $oldItem->quantity);
                }
            }

            $purchase->items()->delete();

            $totalAmount = 0;
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

                if ($request->status === 'received') {
                    Product::where('id', $productId)->increment('stock', $qty);
                }
            }

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pembelian berhasil diperbarui!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'items.product'])->findOrFail($id);
        return response()->json($purchase);
    }

    public function destroy($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            if ($purchase->status === 'received') {
                foreach ($purchase->items as $item) {
                    Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
                }
            }

            $purchase->items()->delete();
            $purchase->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data'], 500);
        }
    }
}
