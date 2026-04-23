<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function hourlyReport(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-d');
        $endDate = $request->end_date ?? date('Y-m-d');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;

        $kasirs = User::role('kasir')->get();
        $query = Order::with(['user', 'payment'])->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($kasirFilter) {
            $query->where('user_id', $kasirFilter);
        }
        if ($methodFilter) {
            $query->whereHas('payment', fn($q) => $q->where('payment_method', $methodFilter));
        }

        $orders = $query->oldest()->get();

        $tableData = [];
        $totals = [
            'completed' => 0,
            'pending' => 0,
            'cancelled' => 0,
            'cash' => 0,
            'transfer' => 0,
            'total_trx' => 0,
            'revenue' => 0,
        ];
        $hourlyData = collect();

        $hourlyData = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('H:00');
        });

        if ($orders->isNotEmpty()) {
            $hourlyData = $orders->groupBy(function ($order) {
                return \Carbon\Carbon::parse($order->created_at)->format('H:00');
            });

            foreach ($hourlyData as $hour => $hourOrders) {
                $tableData[] = [
                    'hour_raw' => $hour,
                    'hour_formatted' => $hour,
                    'completed' => $hourOrders->where('status', 'completed')->count(),
                    'pending' => $hourOrders->where('status', 'pending')->count(),
                    'cancelled' => $hourOrders->where('status', 'cancelled')->count(),
                    'cash' => $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                    'transfer' => $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                    'total_trx' => $hourOrders->count(),
                    'revenue' => $hourOrders->sum('total_amount'),
                ];
            }

            // Hitung total di luar loop (biar lebih cepat)
            $totals = [
                'completed' => $orders->where('status', 'completed')->count(),
                'pending' => $orders->where('status', 'pending')->count(),
                'cancelled' => $orders->where('status', 'cancelled')->count(),
                'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $orders->count(),
                'revenue' => $orders->sum('total_amount'),
            ];
        }

        $tableData = collect($tableData)->sortBy('hour_raw')->values();

        $totalTransaksiKeseluruhan = $orders->count();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $peakHourRow = collect($tableData)->sortByDesc('total_trx')->first();
        $peakHourName = $peakHourRow ? $peakHourRow['hour_formatted'] : '-';
        $peakHourTrxCount = $peakHourRow ? $peakHourRow['total_trx'] : 0;

        $chartHours = $tableData->pluck('hour_raw')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->translatedFormat('H:00');
        });
        $chartVolume = $tableData->pluck('total_trx');

        $chartStatusCompleted = [];
        $chartStatusPending = [];
        $chartStatusCancelled = [];

        foreach ($tableData as $row) {
            $dayOrders = $hourlyData[$row['hour_raw']];

            $chartStatusCompleted[] = $dayOrders->where('status', 'completed')->count();
            $chartStatusPending[] = $dayOrders->where('status', 'pending')->count();
            $chartStatusCancelled[] = $dayOrders->where('status', 'cancelled')->count();
        }

        $pieData = [
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
        ];

        $cashierData = $orders
            ->groupBy('user_id')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->user->name ?? 'Unknown',
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();
        return view('dashboard.reports.hourly', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalTransaksiKeseluruhan', 'totalKeuntunganKeseluruhan', 'peakHourName', 'peakHourTrxCount', 'chartHours', 'chartVolume', 'chartStatusCompleted', 'chartStatusPending', 'chartStatusCancelled', 'pieData', 'cashierData', 'orders', 'totals'));
    }

    public function exportHourlyPdf(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-d');
        $endDate = $request->end_date ?? date('Y-m-d');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;

        $kasirs = User::role('kasir')->get();
        $query = Order::with(['user', 'payment'])->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($kasirFilter) {
            $query->where('user_id', $kasirFilter);
        }
        if ($methodFilter) {
            $query->whereHas('payment', fn($q) => $q->where('payment_method', $methodFilter));
        }

        $orders = $query->oldest()->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal Export: Tidak ada data transaksi pada filter yang dipilih.');
        }

        $hourlyData = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('H:00');
        });

        $tableData = [];
        foreach ($hourlyData as $hour => $hourOrders) {
            $tableData[] = [
                'hour_raw' => $hour,
                'hour_formatted' => $hour,
                'completed' => $hourOrders->where('status', 'completed')->count(),
                'pending' => $hourOrders->where('status', 'pending')->count(),
                'cancelled' => $hourOrders->where('status', 'cancelled')->count(),
                'cash' => $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $hourOrders->count(),
                'revenue' => $hourOrders->sum('total_amount'),
            ];
        }

        $totals = [
            'completed' => $orders->where('status', 'completed')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
            'total_trx' => $orders->count(),
            'revenue' => $orders->sum('total_amount'),
        ];

        $tableData = collect($tableData)->sortBy('hour_raw')->values();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $pdf = Pdf::loadView('dashboard.reports.pdf_hourly', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalKeuntunganKeseluruhan', 'orders', 'totals'));

        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("Laporan_PerJam_{$startDate}_sampai_{$endDate}.pdf");
    }

    public function dailyReport(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;

        $kasirs = User::role('kasir')->get();
        $query = Order::with(['user', 'payment'])->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($kasirFilter) {
            $query->where('user_id', $kasirFilter);
        }
        if ($methodFilter) {
            $query->whereHas('payment', fn($q) => $q->where('payment_method', $methodFilter));
        }

        $orders = $query->oldest()->get();

        $dailyData = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('Y-m-d');
        });

        $tableData = [];
        foreach ($dailyData as $date => $dayOrders) {
            $tableData[] = [
                'date_raw' => $date,
                'date_formatted' => \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y'),
                'completed' => $dayOrders->where('status', 'completed')->count(),
                'pending' => $dayOrders->where('status', 'pending')->count(),
                'cancelled' => $dayOrders->where('status', 'cancelled')->count(),
                'cash' => $dayOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $dayOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total_amount'),
            ];
        }

        $totals = [
            'completed' => $orders->where('status', 'completed')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
            'total_trx' => $orders->count(),
            'revenue' => $orders->sum('total_amount'),
        ];

        $tableData = collect($tableData)->sortBy('date_raw')->values();

        $totalTransaksiKeseluruhan = $orders->count();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $peakDateRow = collect($tableData)->sortByDesc('total_trx')->first();
        $peakDateName = $peakDateRow ? $peakDateRow['date_formatted'] : '-';
        $peakDateTrxCount = $peakDateRow ? $peakDateRow['total_trx'] : 0;

        $chartDates = $tableData->pluck('date_raw')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->translatedFormat('d F');
        });
        $chartVolume = $tableData->pluck('total_trx');

        $chartStatusCompleted = [];
        $chartStatusPending = [];
        $chartStatusCancelled = [];

        foreach ($tableData as $row) {
            $dayOrders = $dailyData[$row['date_raw']];

            $chartStatusCompleted[] = $dayOrders->where('status', 'completed')->count();
            $chartStatusPending[] = $dayOrders->where('status', 'pending')->count();
            $chartStatusCancelled[] = $dayOrders->where('status', 'cancelled')->count();
        }

        $pieData = [
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
        ];

        $cashierData = $orders
            ->groupBy('user_id')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->user->name ?? 'Unknown',
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();
        return view('dashboard.reports.daily', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalTransaksiKeseluruhan', 'totalKeuntunganKeseluruhan', 'peakDateName', 'peakDateTrxCount', 'chartDates', 'chartVolume', 'chartStatusCompleted', 'chartStatusPending', 'chartStatusCancelled', 'pieData', 'cashierData', 'orders', 'totals'));
    }

    public function exportDailyPdf(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;

        $kasirs = User::role('kasir')->get();
        $query = Order::with(['user', 'payment'])->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($kasirFilter) {
            $query->where('user_id', $kasirFilter);
        }
        if ($methodFilter) {
            $query->whereHas('payment', fn($q) => $q->where('payment_method', $methodFilter));
        }

        $orders = $query->oldest()->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal Export: Tidak ada data transaksi pada filter yang dipilih.');
        }

        $dailyData = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('Y-m-d');
        });

        $tableData = [];
        foreach ($dailyData as $date => $dayOrders) {
            $tableData[] = [
                'date_raw' => $date,
                'date_formatted' => \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y'),
                'completed' => $dayOrders->where('status', 'completed')->count(),
                'pending' => $dayOrders->where('status', 'pending')->count(),
                'cancelled' => $dayOrders->where('status', 'cancelled')->count(),
                'cash' => $dayOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $dayOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total_amount'),
            ];
        }

        $totals = [
            'completed' => $orders->where('status', 'completed')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
            'total_trx' => $orders->count(),
            'revenue' => $orders->sum('total_amount'),
        ];

        $tableData = collect($tableData)->sortBy('date_raw')->values();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $pdf = Pdf::loadView('dashboard.reports.pdf_daily', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalKeuntunganKeseluruhan', 'orders', 'totals'));

        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("Laporan_Harian_{$startDate}_sampai_{$endDate}.pdf");
    }

    public function monthlyReport(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-12-31');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;

        $kasirs = User::role('kasir')->get();
        $query = Order::with(['user', 'payment'])->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($kasirFilter) {
            $query->where('user_id', $kasirFilter);
        }
        if ($methodFilter) {
            $query->whereHas('payment', fn($q) => $q->where('payment_method', $methodFilter));
        }

        $orders = $query->oldest()->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal Export: Tidak ada data transaksi pada filter yang dipilih.');
        }

        $monthlyData = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('Y-m');
        });

        $tableData = [];
        foreach ($monthlyData as $month => $monthOrders) {
            $tableData[] = [
                'month_raw' => $month,
                'month_formatted' => \Carbon\Carbon::parse($month)->translatedFormat('F Y'),
                'completed' => $monthOrders->where('status', 'completed')->count(),
                'pending' => $monthOrders->where('status', 'pending')->count(),
                'cancelled' => $monthOrders->where('status', 'cancelled')->count(),
                'cash' => $monthOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $monthOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $monthOrders->count(),
                'revenue' => $monthOrders->sum('total_amount'),
            ];
        }

        $totals = [
            'completed' => $orders->where('status', 'completed')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
            'total_trx' => $orders->count(),
            'revenue' => $orders->sum('total_amount'),
        ];

        $tableData = collect($tableData)->sortBy('month_raw')->values();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $totalTransaksiKeseluruhan = $orders->count();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $peakMonthRow = collect($tableData)->sortByDesc('total_trx')->first();
        $peakMonthName = $peakMonthRow ? $peakMonthRow['month_formatted'] : '-';
        $peakMonthTrxCount = $peakMonthRow ? $peakMonthRow['total_trx'] : 0;

        $chartMonths = $tableData->pluck('month_raw')->map(function ($month) {
            return \Carbon\Carbon::parse($month)->translatedFormat('F Y');
        });
        $chartVolume = $tableData->pluck('total_trx');

        $chartStatusCompleted = [];
        $chartStatusPending = [];
        $chartStatusCancelled = [];

        foreach ($tableData as $row) {
            $monthOrders = $monthlyData[$row['month_raw']];
            $chartStatusCompleted[] = $monthOrders->where('status', 'completed')->count();
            $chartStatusPending[] = $monthOrders->where('status', 'pending')->count();
            $chartStatusCancelled[] = $monthOrders->where('status', 'cancelled')->count();
        }

        $pieData = [
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
        ];

        $cashierData = $orders
            ->groupBy('user_id')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->user->name ?? 'Unknown',
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        return view('dashboard.reports.monthly', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalTransaksiKeseluruhan', 'totalKeuntunganKeseluruhan', 'peakMonthName', 'peakMonthTrxCount', 'chartMonths', 'chartVolume', 'chartStatusCompleted', 'chartStatusPending', 'chartStatusCancelled', 'pieData', 'cashierData', 'orders', 'totals'));
    }

    public function exportMonthlyPdf(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-12-31');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;

        $kasirs = User::role('kasir')->get();
        $query = Order::with(['user', 'payment'])->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($kasirFilter) {
            $query->where('user_id', $kasirFilter);
        }
        if ($methodFilter) {
            $query->whereHas('payment', fn($q) => $q->where('payment_method', $methodFilter));
        }

        $orders = $query->oldest()->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal Export: Tidak ada data transaksi pada filter yang dipilih.');
        }

        $monthlyData = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('Y-m');
        });

        $tableData = [];
        foreach ($monthlyData as $month => $monthOrders) {
            $tableData[] = [
                'month_raw' => $month,
                'month_formatted' => \Carbon\Carbon::parse($month)->translatedFormat('F Y'),
                'completed' => $monthOrders->where('status', 'completed')->count(),
                'pending' => $monthOrders->where('status', 'pending')->count(),
                'cancelled' => $monthOrders->where('status', 'cancelled')->count(),
                'cash' => $monthOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
                'transfer' => $monthOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
                'total_trx' => $monthOrders->count(),
                'revenue' => $monthOrders->sum('total_amount'),
            ];
        }

        $totals = [
            'completed' => $orders->where('status', 'completed')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
            'total_trx' => $orders->count(),
            'revenue' => $orders->sum('total_amount'),
        ];

        $tableData = collect($tableData)->sortBy('month_raw')->values();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $pdf = Pdf::loadView('dashboard.reports.pdf_monthly', compact('tableData', 'startDate', 'endDate', 'totalKeuntunganKeseluruhan', 'totals'));

        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("Laporan_Bulanan_{$startDate}_sampai_{$endDate}.pdf");
    }
}
