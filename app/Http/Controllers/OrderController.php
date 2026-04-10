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
    function __construct()
    {
        $this->middleware('permission:order.index|order.create|order.pos', ['only' => ['index', 'show']]);
        $this->middleware('permission:order.pos', ['only' => ['create', 'store']]);
        $this->middleware('permission:order.create', ['only' => ['create', 'store']]);
    }

    public function pos()
    {
        $categories = Category::where('status', 'active')->whereNull('parent_id')->get();
        $products = Product::where('stock', '>', 0)
            ->where('status', 'active')
            ->whereHas('category', function ($query) {
                $query->where('status', 'active');
            })
            ->with('category')
            ->get();

        return view('dashboard.orders.pos', compact('products', 'categories'));
    }

    // Proses Simpan Transaksi (Checkout)
    public function store(Request $request)
    {
        // Pastikan paid_amount dibersihkan dari titik jika masih ada
        $paidAmount = (int) str_replace('.', '', $request->paid_amount);

        $request->merge(['paid_amount' => $paidAmount]); // Paksa jadi angka murni

        $request->validate([
            'cart' => 'required|array',
            'payment_method' => 'required|in:cash,transfer',
            'paid_amount' => 'required|numeric|min:' . $request->total_amount,
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan ke Tabel Orders
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => Order::generateInvoiceNumber(),
                'total_amount' => $request->total_amount,
                'status' => 'completed',
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
            // Sesuaikan dengan migration kita tadi:
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method, // tadi kodenya 'method'
                'paid_amount' => $request->paid_amount ?? $request->total_amount,
                'change_amount' => ($request->paid_amount ?? $request->total_amount) - $request->total_amount, // tadi 'change'
                'payment_status' => 'paid',
                'approved_at' => now(),
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
            // Ambil data dengan user agar kolom user.name tidak error
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
                    return '<a href="' . route('dashboard.orders.show', $row->id) . '" class="btn btn-sm btn-info text-white"><i class="fa fa-eye"></i> Detail</a>';
                })
                ->rawColumns(['action']) // WAJIB: Agar HTML tombol muncul
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
