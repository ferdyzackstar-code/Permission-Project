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
            $orderStatus = $request->payment_method === 'cash' ? 'completed' : 'pending';
            $paymentStatus = $request->payment_method === 'cash' ? 'paid' : 'pending';
            // 1. Simpan ke Tabel Orders
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => Order::generateInvoiceNumber(),
                'total_amount' => $request->total_amount,
                'status' => $orderStatus,
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
                'payment_method' => $request->payment_method,
                'paid_amount' => $request->payment_method === 'cash' ? $request->paid_amount : $request->total_amount,
                'change_amount' => $request->payment_method === 'cash' ? $request->paid_amount - $request->total_amount : 0,
                'payment_status' => $paymentStatus,
                'approved_at' => $request->payment_method === 'cash' ? now() : null,
                'approved_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'receipt_url' => route('dashboard.orders.receipt', $order->id),
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
            $orders = Order::with(['user', 'payment'])
                ->latest()
                ->get();
            return datatables()
                ->of($orders)
                ->addIndexColumn()
                ->editColumn('total_amount', function ($row) {
                    return 'Rp ' . number_format($row->total_amount, 0, ',', '.');
                })
                ->addColumn('payment_method', function ($row) {
                    $method = $row->payment ? $row->payment->payment_method : '';

                    if ($method == 'cash') {
                        return '<span class="badge bg-success text-white"><i class="fa-solid fa-money-bill-wave me-1"></i> Cash</span>';
                    } elseif ($method == 'transfer') {
                        return '<span class="badge bg-info text-white"><i class="fa-solid fa-wallet me-1"></i> Transfer</span>';
                    } else {
                        return '-';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'completed') {
                        return '<span class="badge bg-primary text-white">Completed</span>';
                    } elseif ($row->status == 'pending') {
                        return '<span class="badge bg-warning text-white">Pending</span>';
                    } else {
                        return '<span class="badge bg-danger text-white">Cancelled</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-info text-white btn-detail mx-2" data-id="' . $row->id . '"><i class="fa fa-print me-1"></i> Struk</button>';

                    return $btn;
                })
                ->rawColumns(['status', 'action', 'payment_method'])
                ->make(true);
        }
        return view('dashboard.orders.index');
    }

    // Detail Order & Struk
    public function receipt($id)
    {
        $order = Order::with(['items.product', 'payment', 'user'])->findOrFail($id);
        return view('dashboard.orders.receipt', compact('order'));
    }

    public function confirmPayment(Request $request, Order $order)
    {
        DB::beginTransaction();
        try {
            // Update status order
            $order->update(['status' => 'completed']);

            // Update status payment
            $order->payment()->update([
                'payment_status' => 'paid',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran Transfer berhasil disetujui!',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menyetujui pembayaran: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function confirmation(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with('user')->where('status', 'pending')->latest()->get();
            return datatables()
                ->of($orders)
                ->addIndexColumn()
                ->editColumn('total_amount', fn($row) => 'Rp ' . number_format($row->total_amount, 0, ',', '.'))
                ->addColumn('action', function ($row) {
                    // Tambahkan tombol Batalkan di sini    
                    return '
                        <button class="btn btn-sm btn-success btn-approve mx-1" data-id="' .
                        $row->id .
                        '">
                            <i class="far fa-check-circle"></i> Appr  ove
                        </button>
                        <button class="btn btn-sm btn-danger btn-cancel mr-1" data-id="' .
                        $row->id .
                        '">
                            <i class="far fa-times-circle"></i> Batalkan
                        </button>
                        <a href="' .
                        route('dashboard.orders.receipt', $row->id) .
                        '" class="btn btn-sm btn-info text-white">
            <i class="fa fa-print me-1"></i> Struk
        </a>';
                })
                ->make(true);
        }
        return view('dashboard.orders.confirmation');
    }

    // FUNGSI BARU UNTUK MEMBATALKAN DAN MENGEMBALIKAN STOK
    public function cancel($id)
    {
        // Gunakan $id
        DB::beginTransaction();
        try {
            // Cari order berdasarkan ID
            $order = Order::with('items.product')->findOrFail($id);

            // 1. Ubah status jadi cancelled
            $order->update(['status' => 'cancelled']);

            if ($order->payment) {
                $order->payment->update(['payment_status' => 'failed']);
            }

            // 2. Kembalikan stok produk
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->qty);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi dibatalkan & stok dikembalikan!',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal membatalkan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function approve(Order $order)
    {
        $order->update(['status' => 'completed']);
        if ($order->payment) {
            $order->payment->update(['payment_status' => 'paid']);
        }
        return response()->json(['success' => true]);
    }
}
