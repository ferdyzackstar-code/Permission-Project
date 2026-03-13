<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    // Halaman Daftar Transaksi (BARU)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Transaction::with(['outlet', 'user']);

            // Filter Berdasarkan Tanggal
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            // Filter Berdasarkan Outlet (Opsional)
            if ($request->outlet_id) {
                $query->where('outlet_id', $request->outlet_id);
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                // --- PERBAIKAN 1: Matikan order untuk kolom virtual nomor urut ---
                ->orderColumns(['DT_RowIndex'], false)

                // --- PERBAIKAN 2: Tambah filterColumn agar bisa searching Kasir & Outlet ---
                ->filterColumn('kasir', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('outlet_name', function ($query, $keyword) {
                    $query->whereHas('outlet', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })

                ->addColumn('formatted_date', function ($row) {
                    return $row->created_at->format('d M Y H:i');
                })
                ->addColumn('kasir', function ($row) {
                    return $row->user->name ?? '-';
                })
                ->addColumn('outlet_name', function ($row) {
                    return $row->outlet->name ?? '-';
                })
                ->editColumn('total_price', function ($row) {
                    return 'Rp ' . number_format($row->total_price, 0, ',', '.');
                })
                ->make(true);
        }

        $outlets = Outlet::all();
        return view('dashboard.reports.index', compact('outlets'));
    }

    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'index'); // Default ke index jika tidak ada parameter type
        $date = now()->format('d F Y');

        // Ambil input filter dari request (sama dengan yang ada di index dashboard)
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $outletId = $request->get('outlet_id');

        // Logic pemilihan data
        if ($type == 'index') {
            // Query Dasar
            $query = Transaction::with(['outlet', 'user']);

            // Filter Tanggal
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            // Filter Outlet
            if ($outletId) {
                $query->where('outlet_id', $outletId);
            }

            $transactions = $query->latest()->get();
            $totalRevenue = $transactions->sum('total_price');
            $view = 'dashboard.reports.pdf_index';
        } elseif ($type == 'summary') {
            $query = Transaction::query();
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            $transactions = $query->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(total_price) as total_revenue'))->groupBy('date')->orderBy('date', 'desc')->get();

            $totalRevenue = $transactions->sum('total_revenue');
            $view = 'dashboard.reports.pdf_summary';
        } elseif ($type == 'outlet') {
            // Untuk laporan outlet, kita hitung transaksi dalam range tanggal tersebut
            $transactions = Outlet::withCount([
                'transactions' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                },
            ])
                ->withSum(
                    [
                        'transactions' => function ($q) use ($startDate, $endDate) {
                            if ($startDate && $endDate) {
                                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                            }
                        },
                    ],
                    'total_price',
                )
                ->get();

            $totalRevenue = $transactions->sum('transactions_sum_total_price');
            $view = 'dashboard.reports.pdf_outlet';
        } elseif ($type == 'employee') {
            $transactions = User::withCount([
                'transactions' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                },
            ])
                ->withSum(
                    [
                        'transactions' => function ($q) use ($startDate, $endDate) {
                            if ($startDate && $endDate) {
                                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                            }
                        },
                    ],
                    'total_price',
                )
                ->get();

            $totalRevenue = $transactions->sum('transactions_sum_total_price');
            $view = 'dashboard.reports.pdf_employee';
        }

        // Generate PDF
        $pdf = Pdf::loadView($view, compact('transactions', 'date', 'totalRevenue', 'startDate', 'endDate'));

        return $pdf->download("laporan-{$type}-" . now()->format('Ymd') . '.pdf');
    }

    // Method untuk Ringkasan Penjualan
    public function summary(Request $request)
    {
        if ($request->ajax()) {
            // Mengelompokkan transaksi berdasarkan tanggal
            $data = Transaction::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(total_price) as total_revenue'))->groupBy('date')->orderBy('date', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('formatted_date', function ($row) {
                    return date('d M Y', strtotime($row->date));
                })
                ->addColumn('revenue', function ($row) {
                    return 'Rp ' . number_format($row->total_revenue, 0, ',', '.');
                })
                ->make(true);
        }

        // Hitung total keseluruhan untuk widget di atas tabel
        $totalAllTime = Transaction::sum('total_price');
        $transactionCount = Transaction::count();

        return view('dashboard.reports.summary', compact('totalAllTime', 'transactionCount'));
    }

    // Untuk Laporan Per Outlet
    public function outlet(Request $request)
    {
        if ($request->ajax()) {
            $data = Outlet::withCount('transactions')->withSum('transactions', 'total_price')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('total_omzet', function ($row) {
                    return 'Rp ' . number_format($row->transactions_sum_total_price ?? 0, 0, ',', '.');
                })
                ->make(true);
        }

        return view('dashboard.reports.outlet');
    }

    // Untuk Laporan Karyawan
    public function employee(Request $request)
    {
        if ($request->ajax()) {
            $data = User::withCount('transactions')->withSum('transactions', 'total_price')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('total_uang', function ($row) {
                    return 'Rp ' . number_format($row->transactions_sum_total_price ?? 0, 0, ',', '.');
                })
                ->addColumn('status', function ($row) {
                    $badge = $row->transactions_count > 0 ? 'badge-success' : 'badge-secondary';
                    $text = $row->transactions_count > 0 ? 'Aktif' : 'Pasif';
                    return '<span class="badge ' . $badge . '">' . $text . '</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('dashboard.reports.employee');
    }

    public function productReport(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\Product::withSum(
                [
                    'transaction_details as jumlah_terjual' => function ($q) use ($request) {
                        if ($request->start_date && $request->end_date) {
                            $q->whereHas('transaction', function ($t) use ($request) {
                                $t->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                            });
                        }
                    },
                ],
                'quantity',
            )->withSum(
                [
                    'transaction_details as total_omzet' => function ($q) use ($request) {
                        if ($request->start_date && $request->end_date) {
                            $q->whereHas('transaction', function ($t) use ($request) {
                                $t->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                            });
                        }
                    },
                ],
                'subtotal',
            );

            return DataTables::of($query) // Masukkan $query di sini
                ->addIndexColumn()
                ->editColumn('price', fn($row) => 'Rp ' . number_format($row->price, 0, ',', '.'))
                // Pastikan menggunakan alias 'jumlah_terjual' dan 'total_omzet'
                ->addColumn('terjual', fn($row) => ($row->jumlah_terjual ?? 0) . ' Pcs')
                ->addColumn('omzet', fn($row) => 'Rp ' . number_format($row->total_omzet ?? 0, 0, ',', '.'))
                ->addColumn('rata_rata', function ($row) {
                    $terjual = (int) $row->jumlah_terjual;
                    $omzet = (float) $row->total_omzet;
                    $avg = $terjual > 0 ? $omzet / $terjual : 0;
                    return 'Rp ' . number_format($avg, 0, ',', '.');
                })
                ->make(true);
        }

        return view('dashboard.reports.product');
    }
}
