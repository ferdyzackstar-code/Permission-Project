<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class OrderController extends Controller
{
    // Tampilan Halaman Kasir (POS)
    public function pos()
    {
        $products = Product::where('stock', '>', 0)->get();
        $categories = Category::whereNull('parent_id')->get();

        return view('dashboard.orders.pos', compact('products', 'categories'));
    }

    // Proses Simpan Transaksi (Checkout)
    public function store(Request $request)
    {
        // Validasi input dasar
        $request->validate([
            'cart' => 'required|array',
            'payment_method' => 'required|in:cash,transfer',
            'paid_amount' => 'required_if:payment_method,cash',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan ke Tabel Orders
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => Order::generateInvoiceNumber(),
                'total_amount' => $request->total_amount,
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            // 2. Loop Cart untuk Simpan ke OrderItems & Update Stok
            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);

                // Cek apakah stok mencukupi lagi (Double Check)
                if ($product->stock < $item['qty']) {
                    throw new Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                ]);

                // Potong Stok Produk
                $product->decrement('stock', $item['qty']);
            }

            // 3. Simpan ke Tabel Payments
            Payment::create([
                'order_id' => $order->id,
                'method' => $request->payment_method,
                'paid_amount' => $request->paid_amount ?? $request->total_amount,
                'change' => ($request->paid_amount ?? $request->total_amount) - $request->total_amount,
                'paid_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'order_id' => $order->id,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal transaksi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // List Riwayat Transaksi (Untuk DataTables)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with('user')->latest()->get();
            return datatables()
                ->of($orders)
                ->addIndexColumn()
                ->editColumn('total_amount', function ($row) {
                    return 'Rp ' . number_format($row->total_amount, 0, ',', '.');
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('dashboard.orders.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Detail</a>';
                })
                ->make(true);
        }
        return view('dashboard.orders.index');
    }

    // Detail Order & Struk
    public function show($id)
    {
        $order = Order::with(['items.product', 'payment', 'user'])->findOrFail($id);
        return view('dashboard.orders.show', compact('order'));
    }
}
