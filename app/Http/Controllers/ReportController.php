<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
        // 1. Tentukan Bulan dan Tahun yang mau dilihat (Default: Bulan Ini)
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        // 2. Ambil semua pesanan yang 'Completed' di bulan tersebut
        $orders = Order::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'completed')->get();

        // 3. KELOMPOKKAN BERDASARKAN TANGGAL (Bahasa Bayi: Pisahkan bon ke kotak per tanggal)
        $dailyData = $orders
            ->groupBy(function ($order) {
                // Mengambil tanggalnya saja, misal: "2026-04-15"
                return $order->created_at->format('Y-m-d');
            })
            ->map(function ($dayOrders) {
                // Hitung total transaksi dan uang masuk per kotak tanggal
                return [
                    'total_transaksi' => $dayOrders->count(),
                    'revenue' => $dayOrders->sum('total_amount'),
                ];
            });

        // Urutkan dari tanggal paling tua ke terbaru
        $dailyData = $dailyData->sortKeys();

        return view('dashboard.reports.daily', compact('dailyData', 'month', 'year'));
    }

    public function exportDailyPdf(Request $request)
    {
        // 1. Tangkap filter bulan dan tahun dari URL
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        // 2. Ambil data yang sama persis
        $orders = Order::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'completed')->get();

        // 3. Kelompokkan
        $dailyData = $orders
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })
            ->map(function ($dayOrders) {
                return [
                    'total_transaksi' => $dayOrders->count(),
                    'revenue' => $dayOrders->sum('total_amount'),
                ];
            });

        $dailyData = $dailyData->sortKeys();

        // 4. Proses Pembuatan PDF
        $pdf = Pdf::loadView('dashboard.reports.pdf_daily', compact('dailyData', 'month', 'year'));

        // 5. Download Otomatis! (Bisa diganti ->stream() kalau mau lihat di browser dulu)
        return $pdf->download('Laporan_Harian_PetShop_' . $month . '_' . $year . '.pdf');
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

        // Masukkan peakHour dan peakTrxCount ke dalam compact
        return view('dashboard.reports.hourly', compact('orders', 'startDate', 'endDate', 'statusFilter', 'methodFilter', 'kasirFilter', 'kasirs', 'hours', 'lineStatus', 'barTrx', 'pieData', 'cashierData', 'peakHour', 'peakTrxCount'));
    }

    public function exportHourlyPdf(Request $request)
    {
        // Logikanya sama persis dengan di atas, ini agar data yang di-download = data yang di-filter
        $startDate = $request->start_date ?? date('Y-m-d');
        $endDate = $request->end_date ?? date('Y-m-d');
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.reports.pdf_hourly', compact('orders', 'startDate', 'endDate'));

        // Agar nama file PDF-nya otomatis dinamis mengikuti tanggal filter
        return $pdf->download("Laporan_Transaksi_{$startDate}_sampai_{$endDate}.pdf");
    }
}
