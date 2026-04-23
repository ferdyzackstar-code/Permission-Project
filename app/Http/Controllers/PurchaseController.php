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
        $products = Product::all(); // Untuk dropdown di modal

        return view('dashboard.purchases.index', compact('purchases', 'suppliers', 'products'));
    }

    public function store(Request $request)
    {
        // Bersihkan format harga dari "Rp. 1.000.000" menjadi "1000000"
        $cleanPrices = array_map(function ($price) {
            return (float) preg_replace('/[^0-9]/', '', $price);
        }, $request->price);

        DB::beginTransaction();
        try {
            // 1. Generate PO Number
            $datePrefix = Carbon::parse($request->purchase_date)->format('Ymd');
            $last = Purchase::whereDate('purchase_date', $request->purchase_date)->latest('id')->first();
            $seq = $last ? intval(substr($last->purchase_number, -4)) + 1 : 1;
            $poNumber = 'PO-' . $datePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // 2. Hitung Grand Total dari Array
            $totalAmount = 0;
            for ($i = 0; $i < count($request->product_id); $i++) {
                $totalAmount += $request->quantity[$i] * $cleanPrices[$i];
            }

            // 3. Simpan Header (Purchases)
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'purchase_number' => $poNumber,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => 'received', // Asumsi barang langsung masuk stok
            ]);

            // 4. Simpan Detail (Purchase Items) & Update Stok
            for ($i = 0; $i < count($request->product_id); $i++) {
                $qty = $request->quantity[$i];
                $price = $cleanPrices[$i];
                $subtotal = $qty * $price;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $request->product_id[$i],
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Update Stok Produk
                Product::where('id', $request->product_id[$i])->increment('stock', $qty);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pembelian berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        // Load relasi penuh untuk ditampilkan di modal detail
        $purchase = Purchase::with(['supplier', 'items.product'])->findOrFail($id);
        return response()->json($purchase);
    }

    public function destroy($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Kembalikan stok produk sebelum menghapus item
            foreach ($purchase->items as $item) {
                Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
            }

            // Hapus items lalu hapus purchase (atau biarkan foreign key cascade yang bekerja jika sudah diset di database)
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
