<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Order;
use App\Models\Purchase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // ── RINGKASAN PENGGUNA & AKSES ────────────────────────────────────
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        // ── RINGKASAN INVENTORI ───────────────────────────────────────────
        $totalProducts = Product::count();
        $totalSpecies = Category::whereNull('parent_id')->count(); // Parent category
        $totalCategories = Category::whereNotNull('parent_id')->count(); // Sub category
        $totalSuppliers = Supplier::count();

        // ── LIMA TRANSAKSI TERAKHIR ───────────────────────────────────────
        $latestOrders = Order::with(['user', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        // ── PRODUK PALING LARIS BULAN INI (TOP 5) ────────────────────────
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->select('products.id', 'products.name', 'products.image', DB::raw('SUM(order_items.qty) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // ── KASIR PALING AKTIF BULAN INI (TOP 5) ─────────────────────────
        $topKasirs = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->select('users.id', 'users.name', 'users.image', DB::raw('COUNT(orders.id) as total_transactions'), DB::raw('SUM(orders.total_amount) as total_revenue'))
            ->groupBy('users.id', 'users.name', 'users.image')
            ->orderByDesc('total_transactions')
            ->take(5)
            ->get();

        // ── LIMA PEMBELIAN TERAKHIR ───────────────────────────────────────
        $latestPurchases = Purchase::with('supplier')->latest()->take(5)->get();

        // ── STOK PRODUK MENIPIS (Stock <= 10) ────────────────────────────
        $lowStockProducts = Product::with('category')->where('status', 'active')->where('stock', '<=', 10)->orderBy('stock', 'asc')->take(8)->get();

        // ── SUPPLIER PALING BANYAK SUPPLY BULAN INI ──────────────────────
        // Count langsung dari purchases, GROUP BY supplier_id — simple & akurat
        $topSuppliers = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->where('purchases.status', 'received')
            ->whereBetween('purchases.purchase_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->select('suppliers.id', 'suppliers.name', DB::raw('COUNT(purchases.id) as total_purchases'), DB::raw('SUM(purchases.total_amount) as total_value'))
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_purchases')
            ->take(5)
            ->get();

        // ── CHART: Tren Penjualan 30 Hari Terakhir (Line Chart) ──────────
        $salesTrend = DB::table('orders')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as total_orders'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Lengkapi hari yang kosong supaya chart tidak bolong
        $salesChartLabels = [];
        $salesChartOrders = [];
        $salesChartRevenue = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $found = $salesTrend->firstWhere('date', $date);

            $salesChartLabels[] = now()->subDays($i)->format('d M');
            $salesChartOrders[] = $found ? (int) $found->total_orders : 0;
            $salesChartRevenue[] = $found ? (float) $found->total_revenue : 0;
        }

        // ── CHART: Distribusi Status Order (Pie Chart) ────────────────────
        $orderStatusData = DB::table('orders')
        ->select('status', DB::raw('COUNT(id) as total'))
        ->groupBy('status')
        ->pluck('total', 'status');

        // ── CHART: Pembelian per Supplier (Horizontal Bar) ────────────────
        $purchaseBySupplier = DB::table('purchases')
        ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
        ->where('purchases.status', 'received')
        ->select('suppliers.name', DB::raw('SUM(purchases.total_amount) as total_value'))
        ->groupBy('suppliers.name')
        ->orderByDesc('total_value')
        ->take(6)
        ->get();

        // ── CHART: Stok per Kategori (Bar Chart) ─────────────────────────
        $stockByCategory = DB::table('products')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->join('categories as parent', 'categories.parent_id', '=', 'parent.id')
        ->where('products.status', 'active')
        ->select('parent.name as category_name', DB::raw('SUM(products.stock) as total_stock'))
        ->groupBy('parent.name')
        ->orderByDesc('total_stock')
        ->get();

        return view(
            'dashboard.index',
            compact(
                // Ringkasan Pengguna & Akses
                'totalUsers',
                'totalRoles',
                'totalPermissions',
                // Ringkasan Inventori
                'totalProducts',
                'totalSpecies',
                'totalCategories',
                'totalSuppliers',
                // Penjualan
                'latestOrders',
                'topProducts',
                'topKasirs',
                // Pembelian
                'latestPurchases',
                'lowStockProducts',
                'topSuppliers',
                // Chart Data
                'salesChartLabels',
                'salesChartOrders',
                'salesChartRevenue',
                'orderStatusData',
                'purchaseBySupplier',
                'stockByCategory',
            ),
        );
    }
}
