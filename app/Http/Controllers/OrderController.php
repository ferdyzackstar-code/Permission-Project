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
    public function __construct()
    {
        $this->middleware('permission:order.history')->only(['index']);
        $this->middleware('permission:order.pos')->only(['pos', 'store']);
        $this->middleware('permission:order.confirm')->only(['confirmation', 'confirmPayment', 'approve', 'cancel']);
        $this->middleware('permission:order.receipt')->only(['receipt']);
    }

    // ── POS ───────────────────────────────────────────────────────
    public function pos()
    {
        $categories = Category::where('status', 'active')->whereNull('parent_id')->get();
        $products = Product::where('stock', '>', 0)->where('status', 'active')->whereHas('category', fn($q) => $q->where('status', 'active'))->with('category')->get();

        return view('dashboard.orders.pos', compact('products', 'categories'));
    }

    // ── STORE (Checkout) ──────────────────────────────────────────
    public function store(Request $request)
    {
        $paidAmount = (int) str_replace('.', '', $request->paid_amount);
        $request->merge(['paid_amount' => $paidAmount]);

        $request->validate([
            'cart' => 'required|array',
            'payment_method' => 'required|in:cash,transfer',
            'paid_amount' => 'required|numeric|min:' . $request->total_amount,
        ]);

        DB::beginTransaction();
        try {
            $isCash = $request->payment_method === 'cash';
            $orderStatus = $isCash ? 'completed' : 'pending';
            $paymentStatus = $isCash ? 'paid' : 'pending';

            // 1. Buat Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => Order::generateInvoiceNumber(),
                'total_amount' => $request->total_amount,
                'status' => $orderStatus,
            ]);

            // 2. Loop Cart → simpan order items
            foreach ($request->cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']);

                if (!$product) {
                    throw new Exception('Produk tidak ditemukan.');
                }

                if ($isCash && $product->stock < $item['qty']) {
                    throw new Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],  
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                ]);

                if ($isCash) {
                    $product->decrement('stock', $item['qty']);
                }
            }

            // 3. Simpan Payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'paid_amount' => $isCash ? $request->paid_amount : $request->total_amount,
                'change_amount' => $isCash ? $request->paid_amount - $request->total_amount : 0,
                'payment_status' => $paymentStatus,
                'approved_at' => $isCash ? now() : null,
                'approved_by' => $isCash ? Auth::id() : null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'receipt_url' => route('dashboard.orders.receipt', $order->id),
                'is_transfer' => !$isCash,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal transaksi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // ── INDEX (Riwayat Transaksi — DataTables) ────────────────────
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['user', 'payment'])->latest()->get(); 

            return datatables()
                ->of($orders)
                ->addIndexColumn()
                ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i'))
                ->editColumn('total_amount', fn($row) => 'Rp ' . number_format($row->total_amount, 0, ',', '.'))
                ->addColumn('payment_method', function ($row) {
                    $method = $row->payment->payment_method ?? '';
                    return match ($method) {
                        'cash' => '<span class="badge bg-success text-white"><i class="fas fa-money-bill-wave me-1"></i> Cash</span>',
                        'transfer' => '<span class="badge bg-info text-white"><i class="fas fa-university me-1"></i> Transfer</span>',
                        default => '-',
                    };
                })
                ->editColumn('status', function ($row) {
                    return match ($row->status) {
                        'completed' => '<span class="badge bg-success text-white">Completed</span>',
                        'pending' => '<span class="badge bg-warning text-white">Pending</span>',
                        'cancelled' => '<span class="badge bg-danger text-white">Cancelled</span>',
                        default => '-',
                    };
                })
                ->addColumn(
                    'action',
                    fn($row) => '<button class="btn btn-sm btn-primary btn-detail" data-id="' .
                        $row->id .
                        '">
                        <i class="fas fa-print me-1"></i> Struk
                    </button>',
                )
                ->rawColumns(['payment_method', 'status', 'action'])
                ->make(true);
        }

        return view('dashboard.orders.index');
    }

    // ── RECEIPT (Struk) ───────────────────────────────────────────
    public function receipt($id)
    {
        $order = Order::with(['items.product', 'payment', 'user'])->findOrFail($id);
        return view('dashboard.orders.receipt', compact('order'));
    }

    // ── CONFIRMATION  ─────────────────
    public function confirmation(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with('user')->where('status', 'pending')->orderBy('created_at', 'desc')->get();

            return datatables()
                ->of($orders)
                ->addIndexColumn()
                ->editColumn('total_amount', fn($row) => 'Rp ' . number_format($row->total_amount, 0, ',', '.'))
                ->addColumn('action', function ($row) {
                    $approveUrl = route('dashboard.orders.approve', $row->id);
                    $cancelUrl = route('dashboard.orders.cancel', $row->id);
                    $receiptUrl = route('dashboard.orders.receipt', $row->id);

                    return '
                        <div class="action-group">
                            <button class="btn-approve-conf btn-approve" data-id="' .
                        $row->id .
                        '">
                                <i class="fas fa-check-circle"></i> Approve
                            </button>
                            <button class="btn-cancel-conf btn-cancel" data-id="' .
                        $row->id .
                        '">
                                <i class="fas fa-times-circle"></i> Batalkan
                            </button>
                            <a href="' .
                        $receiptUrl .
                        '?from=confirmation" class="btn-struk-conf">
                                <i class="fas fa-print"></i> Struk
                            </a>
                        </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.orders.confirmation');
    }

    // ── APPROVE ───────────────────────────────────────────────────
    public function approve(Order $order)
    {
        DB::beginTransaction();
        try {
            if ($order->status !== 'pending') {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Order bukan dalam status pending.',
                    ],
                    422,
                );
            }

            // 1. Update status order & payment
            $order->update(['status' => 'completed']);

            if ($order->payment) {
                $order->payment->update([
                    'payment_status' => 'paid',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            }

            // 2. POTONG STOK — karena saat transfer stok belum dipotong
            $order->load('items.product');
            foreach ($order->items as $item) {
                if ($item->product) {
                    // Cek stok sebelum potong
                    if ($item->product->stock < $item->qty) {
                        throw new Exception("Stok {$item->product->name} tidak mencukupi saat approve.");
                    }
                    $item->product->decrement('stock', $item->qty);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transaksi disetujui & stok dipotong.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal approve: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // ── CANCEL ────────────────────────────────────────────────────
    // Cancel: status → cancelled, payment → failed
    // STOK TIDAK dikembalikan karena transfer pending belum pernah dipotong
    public function cancel($id)
    {
        DB::beginTransaction();
        try {
            $order = Order::with(['items.product', 'payment'])->findOrFail($id);

            if ($order->status !== 'pending') {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Hanya order berstatus pending yang bisa dibatalkan.',
                    ],
                    422,
                );
            }

            // Update status
            $order->update(['status' => 'cancelled']);

            if ($order->payment) {
                $order->payment->update(['payment_status' => 'failed']);
            }

            // ── TIDAK kembalikan stok ────────────────────────────
            // Transfer pending = stok belum pernah dipotong saat store()
            // Mengembalikan stok di sini akan menyebabkan stok bertambah salah

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi dibatalkan.',
            ]);
        } catch (Exception $e) {
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

    // ── CONFIRM PAYMENT (legacy — dipertahankan) ──────────────────
    public function confirmPayment(Request $request, Order $order)
    {
        return $this->approve($order);
    }
}
