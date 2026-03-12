<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Query dasar untuk statistik dan tabel
        $query = Transaction::query();

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // Hitung data untuk Cards
        $totalSales = (clone $query)->sum('total_price');
        $transactionCount = (clone $query)->count();
        $productCount = Product::count(); // Tetap global sesuai keinginanmu

        // Ambil 5 transaksi terbaru
        $recentTransactions = (clone $query)
            ->with(['outlet', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Perbaikan query grafik agar ikut filter tanggal
        $salesData = Transaction::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->when($outletId, function ($q) use ($outletId) {
                return $q->where('outlet_id', $outletId);
            })
            ->when(
                $startDate && $endDate,
                function ($q) use ($startDate, $endDate) {
                    return $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                },
                function ($q) {
                    // Jika tidak ada filter, baru tampilkan 7 hari terakhir
                    return $q->where('created_at', '>=', now()->subDays(6));
                },
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.index', compact('totalSales', 'transactionCount', 'productCount', 'salesData', 'recentTransactions'));
    }
}
