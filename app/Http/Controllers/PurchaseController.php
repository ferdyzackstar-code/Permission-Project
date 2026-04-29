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
    // =========================================================
    // INDEX - Riwayat Semua Pembelian
    // =========================================================
    public function index(Request $request)
    {
        $purchases = Purchase::with('supplier')->latest()->get();
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::all();
        $totalProducts = Product::count();
        $pendingCount = Purchase::where('status', 'pending')->count();
        $completedCount = Purchase::where('status', 'completed')->count();
        $cancelledCount = Purchase::where('status', 'cancelled')->count();

        return view('dashboard.purchases.index', compact('purchases', 'suppliers', 'products', 'totalProducts', 'pendingCount', 'completedCount', 'cancelledCount'));
    }

    // =========================================================
    // STORE - Buat Pesanan Baru (Status Otomatis: pending)
    // =========================================================
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric|min:1',
            'price' => 'required|array|min:1',
        ]);

        // Bersihkan format harga rupiah → angka
        $cleanPrices = array_map(fn($p) => (float) preg_replace('/[^0-9]/', '', $p), $request->price);

        DB::beginTransaction();
        try {
            // Generate PO Number otomatis berdasarkan tanggal
            $datePrefix = Carbon::parse($request->purchase_date)->format('Ymd');
            $last = Purchase::whereDate('purchase_date', $request->purchase_date)->latest('id')->first();
            $seq = $last ? (int) substr($last->purchase_number, -4) + 1 : 1;
            $poNumber = 'PO-' . $datePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // Hitung grand total
            $totalAmount = 0;
            foreach ($request->product_id as $i => $pid) {
                $totalAmount += $request->quantity[$i] * $cleanPrices[$i];
            }

            // Simpan header purchase — STATUS SELALU PENDING
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'purchase_number' => $poNumber,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            // Simpan detail items — STOK BELUM BERTAMBAH
            foreach ($request->product_id as $i => $pid) {
                $qty = $request->quantity[$i];
                $price = $cleanPrices[$i];
                $subtotal = $qty * $price;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat! Silakan konfirmasi di halaman Konfirmasi Pembelian.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // =========================================================
    // UPDATE - Edit Pesanan (HANYA boleh jika status: pending)
    // =========================================================
    public function update(Request $request, $id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        // Guard: completed & cancelled tidak boleh diedit
        if (in_array($purchase->status, ['completed', 'cancelled'])) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Pesanan dengan status ' . strtoupper($purchase->status) . ' tidak dapat diedit!',
                ],
                403,
            );
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric|min:1',
            'price' => 'required|array|min:1',
        ]);

        $cleanPrices = array_map(fn($p) => (float) preg_replace('/[^0-9]/', '', $p), $request->price);

        DB::beginTransaction();
        try {
            // Hapus items lama
            $purchase->items()->delete();

            // Hitung total baru & simpan items baru
            $totalAmount = 0;
            foreach ($request->product_id as $i => $pid) {
                $qty = $request->quantity[$i];
                $price = $cleanPrices[$i];
                $subtotal = $qty * $price;
                $totalAmount += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update header — STATUS TETAP PENDING, tidak diubah
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memperbarui pesanan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // =========================================================
    // SHOW - Detail Pesanan (JSON response untuk modal)
    // =========================================================
    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'items.product'])->findOrFail($id);
        return response()->json($purchase);
    }

    // =========================================================
    // CONFIRMATION PAGE - Daftar pesanan status pending saja
    // =========================================================
    public function confirmation()
    {
        $pendingPurchases = Purchase::with('supplier')->where('status', 'pending')->latest()->get();

        return view('dashboard.purchases.confirmation', compact('pendingPurchases'));
    }

    // =========================================================
    // APPROVE - pending → completed + stok bertambah
    // =========================================================
    public function approve($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        // Guard: hanya pending yang bisa diapprove
        if ($purchase->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Hanya pesanan berstatus Pending yang dapat disetujui!',
                ],
                403,
            );
        }

        DB::beginTransaction();
        try {
            // Update status pending → received
            $purchase->update(['status' => 'received']);

            // Increment stok tiap item — hanya saat received
            foreach ($purchase->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan disetujui! Stok produk berhasil ditambahkan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menyetujui pesanan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // =========================================================
    // CANCEL - pending → cancelled (stok tidak berubah sama sekali)
    // =========================================================
    public function cancel($id)
    {
        $purchase = Purchase::findOrFail($id);

        // Guard: hanya pending yang bisa dibatalkan
        if ($purchase->status !== 'pending') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Hanya pesanan berstatus Pending yang dapat dibatalkan!',
                ],
                403,
            );
        }

        DB::beginTransaction();
        try {
            // Update status pending → cancelled, stok TIDAK berubah
            $purchase->update(['status' => 'cancelled']);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan. Stok produk tidak berubah.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal membatalkan pesanan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
