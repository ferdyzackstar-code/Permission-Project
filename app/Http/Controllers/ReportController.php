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
    public function index(Request $request)
    {
        // 1. SIAPKAN SARINGAN UTAMA (Query Builder)
        $query = Order::with(['user', 'payment']);

        // 2. PROSES PENYARINGAN BERDASARKAN REQUEST (Filter)
        // Jika ada filter tanggal...
        $query->when($request->start_date && $request->end_date, function ($q) use ($request) {
            return $q->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        });

        // Jika ada filter Kasir...
        $query->when($request->kasir_id, function ($q) use ($request) {
            return $q->where('user_id', $request->kasir_id);
        });

        // Jika ada filter Status (Completed, Pending, Cancelled)...
        $query->when($request->status, function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        // Jika ada filter Metode Pembayaran (Cash/Transfer)...
        $query->when($request->payment_method, function ($q) use ($request) {
            // Karena payment_method ada di tabel payments, kita gunakan whereHas
            return $q->whereHas('payment', function ($p) use ($request) {
                $p->where('payment_method', $request->payment_method);
            });
        });

        // AMBIL DATA YANG SUDAH DISARING
        $orders = $query->latest()->get();

        // 3. HITUNG-HITUNGAN UNTUK GRAFIK (CHART)

        // A. Trend Metode Pembayaran (Berapa Cash, Berapa Transfer)
        $paymentTrend = $orders
            ->groupBy(function ($order) {
                return $order->payment ? $order->payment->payment_method : 'unknown';
            })
            ->map->count();

        // B. Kasir Paling Aktif (Hitung transaksi per kasir)
        $kasirTrend = $orders
            ->groupBy(function ($order) {
                return $order->user ? $order->user->name : 'Unknown';
            })
            ->map->count();

        // C. Top 5 Produk Terlaris (Berdasarkan filter tanggal yang sama)
        $topProducts = OrderItem::with('product')
            ->whereHas('order', function ($q) use ($request) {
                // Terapkan filter tanggal agar akurat dengan laporan
                if ($request->start_date && $request->end_date) {
                    $q->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                }
            })
            ->select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(qty * price) as omset'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Siapkan data kasir untuk dropdown filter di Blade
        $kasirs = User::all(); // Sesuaikan jika kamu punya role khusus kasir

        return view('dashboard.reports.index', compact('orders', 'paymentTrend', 'kasirTrend', 'topProducts', 'kasirs'));
    }

    public function exportPdf(Request $request)
    {
        // Panggil ulang saringan yang sama persis seperti di atas
        // (Bisa dipisahkan ke function private agar tidak mengulang kode, tapi begini lebih mudah dipahami dulu)
        $query = Order::with(['user', 'payment']);
        // ... (Kopi paste logika $query->when() dari atas ke sini) ...

        $orders = $query->latest()->get();

        // Kirim ke view khusus PDF
        $pdf = Pdf::loadView('dashboard.reports.pdf', compact('orders'));

        // Download file PDF
        return $pdf->download('Laporan_Transaksi.pdf');
    }

    public function incomeReport(Request $request)
    {
        // 1. Siapkan Query Dasar
        $query = Order::with(['user', 'payment']);

        // 2. Terapkan Saringan (Filter)
        $query->when($request->start_date && $request->end_date, function ($q) use ($request) {
            return $q->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        });

        $query->when($request->status, function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        $query->when($request->payment_method, function ($q) use ($request) {
            return $q->whereHas('payment', function ($p) use ($request) {
                $p->where('payment_method', $request->payment_method);
            });
        });

        // 3. Logika: Jika tidak ada filter, ambil 5 terakhir. Jika ada filter, ambil semua.
        if (!$request->anyFilled(['start_date', 'status', 'payment_method'])) {
            $orders = $query->latest()->take(5)->get();
        } else {
            $orders = $query->latest()->get();
        }

        // 4. Hitung Data untuk Chart Lingkaran
        $chartData = $orders
            ->groupBy(function ($order) {
                return $order->payment ? ucfirst($order->payment->payment_method) : 'Lainnya';
            })
            ->map->count();

        return view('dashboard.reports.income', compact('orders', 'chartData'));
    }

    public function dailyReport(Request $request)
    {
        // Default: Awal bulan sampai akhir bulan ini
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');

        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;

        $kasirs = User::role('kasir')->get();

        // Ambil data transaksi
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

        // --- C. PENGELOMPOKAN DATA TABEL (PER HARI) ---
        $dailyData = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('Y-m-d');
        });

        $tableData = [];
        foreach ($dailyData as $date => $dayOrders) {
            Carbon::setLocale('id'); // Pastikan bahasa Indonesia
            $tableData[] = [
                'date_raw' => $date,
                'date_formatted' => Carbon::parse($date)->translatedFormat('l, d F Y'),
                'total_trx' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total_amount'),
            ];
        }

        // Urutkan berdasarkan tanggal terlama ke terbaru
        $tableData = collect($tableData)->sortBy('date_raw')->values();

        // --- F. BOX INFORMATION ---
        $totalTransaksiKeseluruhan = $orders->count();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $peakDay = collect($tableData)->sortByDesc('total_trx')->first();
        $peakDate = $peakDay ? $peakDay['date_formatted'] : '-';
        $peakTrxCount = $peakDay ? $peakDay['total_trx'] : 0;

        // --- E. DATA UNTUK CHART ---
        // 1. Chart Volume Transaksi (Tren Hari)
        // Gunakan 'date_raw' (format 2026-04-09) agar Carbon bisa membacanya tanpa error
        $chartDates = $tableData->pluck('date_raw')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->translatedFormat('d M'); // Hasil: 09 Apr
        });
        $chartVolume = $tableData->pluck('total_trx');

        // 2. Chart Status Transaksi
        $chartStatusCompleted = [];
        $chartStatusPending = [];
        $chartStatusCancelled = [];

        foreach ($tableData as $row) {
            // Ambil data order asli dari grup berdasarkan tanggal
            $dayOrders = $dailyData[$row['date_raw']];

            $chartStatusCompleted[] = $dayOrders->where('status', 'completed')->count();
            $chartStatusPending[] = $dayOrders->where('status', 'pending')->count();
            $chartStatusCancelled[] = $dayOrders->where('status', 'cancelled')->count();
        }

        // 3. Chart Metode Pembayaran
        $pieData = [
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
        ];

        // 4. Chart Performa Kasir
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

        return view('dashboard.reports.daily', compact('tableData', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'totalTransaksiKeseluruhan', 'totalKeuntunganKeseluruhan', 'peakDate', 'peakTrxCount', 'chartDates', 'chartVolume', 'chartStatusCompleted', 'chartStatusPending', 'chartStatusCancelled', 'pieData', 'cashierData', 'orders'));
    }

    public function exportDailyPdf(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;

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

        // Kelompokkan data sama seperti di View
        $dailyData = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('Y-m-d');
        });

        $tableData = [];
        foreach ($dailyData as $date => $dayOrders) {
            Carbon::setLocale('id');
            $tableData[] = [
                'date_formatted' => Carbon::parse($date)->translatedFormat('l, d F Y'),
                'total_trx' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total_amount'),
            ];
        }
        $tableData = collect($tableData)->sortBy('date_raw')->values();
        $totalKeuntunganKeseluruhan = $orders->sum('total_amount');

        $pdf = Pdf::loadView('dashboard.reports.pdf_daily', compact('tableData', 'startDate', 'endDate', 'totalKeuntunganKeseluruhan'));

        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("Laporan_Harian_{$startDate}_sampai_{$endDate}.pdf");
    }

    public function monthlyReport(Request $request)
    {
        $year = $request->year ?? date('Y');

        // 1. Ambil data mentah dari database
        $monthlyOrders = Order::whereYear('created_at', $year)->where('status', 'completed')->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total_transaksi'), DB::raw('SUM(total_amount) as revenue'))->groupBy('month')->orderBy('month')->get()->keyBy('month'); // Agar gampang dipanggil berdasarkan nomor bulan (1-12)

        // 2. Kita "bungkus" datanya agar selalu ada 12 bulan (Jan-Des)
        $reportData = [];
        for ($m = 1; $m <= 12; $m++) {
            $reportData[$m] = [
                'month_name' => \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F'),
                'total_transaksi' => $monthlyOrders->get($m)->total_transaksi ?? 0,
                'revenue' => $monthlyOrders->get($m)->revenue ?? 0,
            ];
        }

        return view('dashboard.reports.monthly', compact('reportData', 'year'));
    }

    public function exportMonthlyPdf(Request $request)
    {
        $year = $request->year ?? date('Y');

        // Logika pengolahan data sama seperti di atas
        $monthlyOrders = Order::whereYear('created_at', $year)->where('status', 'completed')->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total_transaksi'), DB::raw('SUM(total_amount) as revenue'))->groupBy('month')->get()->keyBy('month');

        $reportData = [];
        for ($m = 1; $m <= 12; $m++) {
            $reportData[$m] = [
                'month_name' => \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F'),
                'total_transaksi' => $monthlyOrders->get($m)->total_transaksi ?? 0,
                'revenue' => $monthlyOrders->get($m)->revenue ?? 0,
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.reports.pdf_monthly', compact('reportData', 'year'));
        return $pdf->download("Laporan_Tahunan_PetShop_$year.pdf");
    }

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

        // --- 1. SIAPKAN WADAH UNTUK DATA TABEL & GRAND TOTAL ---
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

        // --- 2. KELOMPOKKAN DATA BERDASARKAN JAM ---
        // Mengubah '2026-04-17 09:15:00' menjadi '09:00'
        $groupedByHour = $orders->groupBy(function ($item) {
            return $item->created_at->format('H:00');
        });

        // --- 3. HITUNG TOTAL MASING-MASING JAM ---
        foreach ($groupedByHour as $hour => $hourOrders) {
            $completed = $hourOrders->where('status', 'completed')->count();
            $pending = $hourOrders->where('status', 'pending')->count();
            $cancelled = $hourOrders->where('status', 'cancelled')->count();

            $cash = $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count();
            $transfer = $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count();

            $totalTrx = $hourOrders->count();

            // PERUBAHAN DI SINI: Langsung jumlahkan semua total_amount tanpa memfilter status
            $estimasiKeuntungan = $hourOrders->sum('total_amount');

            // Masukkan ke array per baris
            $tableData[] = [
                'waktu' => $hour,
                'completed' => $completed,
                'pending' => $pending,
                'cancelled' => $cancelled,
                'cash' => $cash,
                'transfer' => $transfer,
                'total_trx' => $totalTrx,
                'revenue' => $estimasiKeuntungan,
            ];

            // Tambahkan ke Grand Total Bawah
            $totals['completed'] += $completed;
            $totals['pending'] += $pending;
            $totals['cancelled'] += $cancelled;
            $totals['cash'] += $cash;
            $totals['transfer'] += $transfer;
            $totals['total_trx'] += $totalTrx;
            $totals['revenue'] += $estimasiKeuntungan;
        }

        // --- 4. URUTKAN JAM DARI 00:00 KE 23:00 ---
        $tableData = collect($tableData)->sortBy('waktu')->values()->all();

        // --- DATA UNTUK CHART ---
        $hours = [];
        $lineStatus = ['completed' => [], 'pending' => [], 'cancelled' => []];
        $barTrx = [];

        for ($i = 0; $i < 24; $i++) {
            $label = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $hours[] = $label;

            $hourOrders = $orders->filter(fn($o) => $o->created_at->hour == $i);

            // Data Line Chart (Status)
            $lineStatus['completed'][] = $hourOrders->where('status', 'completed')->count();
            $lineStatus['pending'][] = $hourOrders->where('status', 'pending')->count();
            $lineStatus['cancelled'][] = $hourOrders->where('status', 'cancelled')->count();

            // Data Bar Chart (Total Trx)
            $barTrx[] = $hourOrders->count();
        }

        // --- TAMBAHAN LOGIKA PEAK HOUR ---
        // Mencari nilai tertinggi di array barTrx
        $peakTrxCount = max($barTrx);
        $peakHourIndex = array_search($peakTrxCount, $barTrx);

        // Jika ada transaksi, tampilkan jamnya, jika 0 tampilkan "-"
        $peakHour = $peakTrxCount > 0 ? str_pad($peakHourIndex, 2, '0', STR_PAD_LEFT) . ':00' : '-';

        // Data Pie Chart (Metode)
        $pieData = [
            'cash' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count(),
            'transfer' => $orders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count(),
        ];

        // Data Horizontal Bar (Kasir)
        $cashierData = $orders
            ->groupBy('user_id')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->user->name ?? 'Unknown',
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count');

        return view('dashboard.reports.hourly', compact('orders', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'hours', 'lineStatus', 'barTrx', 'pieData', 'cashierData', 'peakHour', 'peakTrxCount', 'tableData', 'totals'));
    }

    public function exportHourlyPdf(Request $request)
    {
        // 1. Ambil data filter (Default ke hari ini)
        $startDate = $request->start_date ?? date('Y-m-d');
        $endDate = $request->end_date ?? date('Y-m-d');
        $statusFilter = $request->status;
        $methodFilter = $request->payment_method;
        $kasirFilter = $request->kasir_id;

        // 2. Build Query yang sama dengan tampilan Dashboard
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

        // ... kode filter sebelumnya tetap sama ...
        $orders = $query->oldest()->get();

        // --- 1. SIAPKAN WADAH UNTUK DATA TABEL & GRAND TOTAL ---
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

        // --- 2. KELOMPOKKAN DATA BERDASARKAN JAM ---
        // Mengubah '2026-04-17 09:15:00' menjadi '09:00'
        $groupedByHour = $orders->groupBy(function ($item) {
            return $item->created_at->format('H:00');
        });

        // --- 3. HITUNG TOTAL MASING-MASING JAM ---
        foreach ($groupedByHour as $hour => $hourOrders) {
            $completed = $hourOrders->where('status', 'completed')->count();
            $pending = $hourOrders->where('status', 'pending')->count();
            $cancelled = $hourOrders->where('status', 'cancelled')->count();

            $cash = $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'cash')->count();
            $transfer = $hourOrders->filter(fn($o) => optional($o->payment)->payment_method == 'transfer')->count();

            $totalTrx = $hourOrders->count();

            // PERUBAHAN DI SINI: Langsung jumlahkan semua total_amount tanpa memfilter status
            $estimasiKeuntungan = $hourOrders->sum('total_amount');

            // Masukkan ke array per baris
            $tableData[] = [
                'waktu' => $hour,
                'completed' => $completed,
                'pending' => $pending,
                'cancelled' => $cancelled,
                'cash' => $cash,
                'transfer' => $transfer,
                'total_trx' => $totalTrx,
                'revenue' => $estimasiKeuntungan,
            ];

            // Tambahkan ke Grand Total Bawah
            $totals['completed'] += $completed;
            $totals['pending'] += $pending;
            $totals['cancelled'] += $cancelled;
            $totals['cash'] += $cash;
            $totals['transfer'] += $transfer;
            $totals['total_trx'] += $totalTrx;
            $totals['revenue'] += $estimasiKeuntungan;
        }

        // 3. Kondisi: Jika data Kosong, jangan export!
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal Export: Tidak ada data transaksi pada periode atau filter yang dipilih.');
        }

        // 4. Jika ada data, lanjut proses PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.reports.pdf_hourly', compact('orders', 'startDate', 'endDate', 'tableData', 'totals'));

        // Custom kertas ke A4 (opsional agar lebih rapi)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Laporan_Transaksi_{$startDate}_sampai_{$endDate}.pdf");
    }
}
