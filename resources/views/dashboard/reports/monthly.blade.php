@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid py-4">

        <div class="row align-items-center mb-4">
            <div class="col">
                <h4 class="font-weight-bold mb-0 text-dark">
                    <i class="fa-solid fa-calendar-week text-primary"></i> Laporan Transaksi Bulanan
                </h4>
            </div>
            <div class="col-auto">
                <a href="{{ route('dashboard.reports.monthly.export', request()->all()) }}"
                    class="btn btn-danger shadow-sm font-weight-bold">
                    <i class="fas fa-file-pdf fa-sm mr-1"></i> Export PDF
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('dashboard.reports.monthly') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Mulai Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Status Pembayaran</label>
                            <select name="status" class="form-control">
                                <option value="">Semua</option>
                                <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Metode</label>
                            <select name="payment_method" class="form-control">
                                <option value="">Semua</option>
                                <option value="cash" {{ $methodFilter == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ $methodFilter == 'transfer' ? 'selected' : '' }}>Transfer
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group mb-md-0">
                            <label class="text-muted small font-weight-bold mb-1">Kasir</label>
                            <select name="kasir_id" class="form-control">
                                <option value="">Semua Kasir</option>
                                @foreach ($kasirs as $ksr)
                                    <option value="{{ $ksr->id }}" {{ $kasirFilter == $ksr->id ? 'selected' : '' }}>
                                        {{ $ksr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <p class="small font-weight-bold mb-1 text-uppercase text-white-50">BULAN PALING RAMAI</p>
                                <h4 class="mb-0 font-weight-bold">{{ $peakMonthName }}</h4>
                                <div class="small mt-1">{{ $peakMonthTrxCount }} Transaksi</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-fire fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm bg-info text-white h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <p class="small font-weight-bold mb-1 text-uppercase text-white-50">TOTAL TRANSAKSI</p>
                                <h3 class="mb-0 font-weight-bold">{{ $totalTransaksiKeseluruhan }}</h3>
                                <div class="small mt-1">Berdasarkan filter</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-basket fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <p class="small font-weight-bold mb-1 text-uppercase text-white-50">ESTIMASI KEUNTUNGAN</p>
                                <h3 class="mb-0 font-weight-bold">Rp
                                    {{ number_format($totalKeuntunganKeseluruhan, 0, ',', '.') }}</h3>
                                <div class="small mt-1">Berdasarkan filter</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-usd fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold border-0 pt-3"><i
                            class="fa-solid fa-chart-simple text-primary"></i> Tren Volume Transaksi</div>
                    <div class="card-body"><canvas id="volumeChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold border-0 pt-3">
                        <i class="fa-solid fa-chart-bar text-primary"></i> Performa Kasir
                    </div>
                    <div class="card-body"><canvas id="cashierChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold border-0 pt-3">
                        <i class="fa-solid fa-chart-line text-primary"></i> Perbandingan Status Transaksi
                    </div>
                    <div class="card-body" style="position: relative; height:250px;"><canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold border-0 pt-3">
                        <i class="fa-solid fa-chart-pie text-primary"></i> Perbandingan Metode Pembayaran
                    </div>
                    <div class="card-body" style="position: relative; height:250px;"><canvas id="methodChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="font-weight-bold mb-0"><i class="fa-solid fa-table-list text-primary"></i> Rincian Laporan
                    Bulanan</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered mb-0 align-middle text-center">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th rowspan="2" class="align-middle">No</th>
                                <th rowspan="2" class="align-middle">Waktu</th>
                                <th colspan="3" class="border-bottom-0">Status</th>
                                <th colspan="2" class="border-bottom-0">Metode Pembayaran</th>
                                <th rowspan="2" class="align-middle">Total Transaksi</th>
                                <th rowspan="2" class="align-middle text-right pr-4">Estimasi Keuntungan</th>
                            </tr>
                            <tr>
                                <th class="bg-success">Completed</th>
                                <th class="bg-warning">Pending</th>
                                <th class="bg-danger">Cancelled</th>
                                <th class="bg-success">Cash</th>
                                <th class="bg-info">Transfer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tableData as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge badge-secondary">{{ $row['month_formatted'] }}</span></td>
                                    <td>{{ $row['completed'] }}</td>
                                    <td>{{ $row['pending'] }}</td>
                                    <td>{{ $row['cancelled'] }}</td>
                                    <td>{{ $row['cash'] }}</td>
                                    <td>{{ $row['transfer'] }}</td>
                                    <td class="font-weight-bold">{{ $row['total_trx'] }}</td>
                                    <td class="text-right pr-4 font-weight-bold text-success">
                                        Rp {{ number_format($row['revenue'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-muted py-5">Tidak ada transaksi ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-warning font-weight-bold text-white">
                            <tr>
                                <td colspan="2" class="text-center">TOTAL :</td>
                                <td>{{ $totals['completed'] }}</td>
                                <td>{{ $totals['pending'] }}</td>
                                <td>{{ $totals['cancelled'] }}</td>
                                <td>{{ $totals['cash'] }}</td>
                                <td>{{ $totals['transfer'] }}</td>
                                <td>{{ $totals['total_trx'] }}</td>
                                <td class="text-right pr-4">Rp {{ number_format($totals['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Line Chart: Volume Transaksi Harian
        new Chart(document.getElementById('volumeChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartMonths) !!},
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: {!! json_encode($chartVolume) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });

        // 2. Horizontal Bar: Performa Kasir
        new Chart(document.getElementById('cashierChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($cashierData->pluck('name')) !!},
                datasets: [{
                    label: 'Total Transaksi',
                    data: {!! json_encode($cashierData->pluck('count')) !!},
                    backgroundColor: '#36b9cc',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y'
            }
        });

        // 3. Line Chart: Perbandingan Status Transaksi
        new Chart(document.getElementById('statusChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartMonths) !!}, // Menggunakan label tanggal yang sama dengan Volume Chart
                datasets: [{
                        label: 'Completed',
                        data: {!! json_encode($chartStatusCompleted) !!},
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Pending',
                        data: {!! json_encode($chartStatusPending) !!},
                        borderColor: '#f6c23e',
                        backgroundColor: 'rgba(246, 194, 62, 0.05)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Cancelled',
                        data: {!! json_encode($chartStatusCancelled) !!},
                        borderColor: '#e74a3b',
                        backgroundColor: 'rgba(231, 74, 59, 0.05)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // Agar angka di sumbu Y bulat (tidak ada 0.5 transaksi)
                        }
                    }
                }
            }
        });

        // 4. Pie Chart: Metode
        new Chart(document.getElementById('methodChart'), {
            type: 'pie',
            data: {
                labels: ['Cash', 'Transfer'],
                datasets: [{
                    data: [{{ $pieData['cash'] }}, {{ $pieData['transfer'] }}],
                    backgroundColor: ['#1cc88a', '#4e73df']
                }]
            },
            options: {
                maintainAspectRatio: false
            }
        });
    </script>
@endpush
