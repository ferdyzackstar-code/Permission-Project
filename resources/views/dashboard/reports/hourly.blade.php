@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid py-4">

        <div class="row align-items-center mb-4">
            <div class="col">
                <h4 class="font-weight-bold mb-0 text-dark">
                    <i class="fa-solid fa-square-poll-horizontal text-primary"></i> Laporan Transaksi Per-Jam
                </h4>
            </div>
            <div class="col-auto">
                <a href="{{ route('dashboard.reports.hourly.export', request()->all()) }}"
                    class="btn btn-danger shadow-sm font-weight-bold">
                    <i class="fas fa-file-pdf fa-sm mr-1"></i> Export PDF
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('dashboard.reports.hourly') }}" method="GET">
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
                            <label class="text-muted small font-weight-bold mb-1">Status</label>
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
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
                                <option value="">Semua Metode</option>
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
                                <p class="small font-weight-bold mb-1 text-uppercase text-white-50">Jam Paling Ramai</p>
                                <h3 class="mb-0 font-weight-bold">{{ $peakHour }}</h3>
                                <div class="small mt-1 font-weight-bold">{{ $peakTrxCount }} Transaksi</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-3x text-white-50"></i>
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
                                <p class="small font-weight-bold mb-1 text-uppercase text-white-50">Total Transaksi</p>
                                <h3 class="mb-0 font-weight-bold">{{ $orders->count() }}</h3>
                                <div class="small mt-1">Periode Terpilih</div>
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
                                <p class="small font-weight-bold mb-1 text-uppercase text-white-50">Estimasi Keuntungan</p>
                                <h3 class="mb-0 font-weight-bold">
                                    Rp{{ number_format($orders->sum('total_amount'), 0, ',', '.') }}
                                </h3>
                                <div class="small mt-1">Estimasi Pendapatan</div>
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
                    <div class="card-header bg-white font-weight-bold border-0 pt-3">
                        <i class="fa-solid fa-chart-line text-primary"></i> Tren Status Transaksi
                    </div>
                    <div class="card-body"><canvas id="lineStatusChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold border-0 pt-3">
                        <i class="fa-solid fa-chart-simple text-primary"></i> Volume Transaksi
                    </div>
                    <div class="card-body"><canvas id="barTrxChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold border-0 pt-3">
                        <i class="fa-solid fa-chart-pie text-primary"></i> Metode Pembayaran
                    </div>
                    <div class="card-body" style="position: relative; height:300px;">
                        <canvas id="pieMethodChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold border-0 pt-3">
                        <i class="fa-solid fa-chart-bar text-primary"></i> Performa Kasir
                    </div>
                    <div class="card-body"><canvas id="horiCashierChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="font-weight-bold mb-0"><i class="fa-solid fa-table-list text-primary"></i> Rincian Transaksi
                </h6>
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
                                    <td><span class="badge badge-secondary">{{ $row['waktu'] }}</span></td>
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
        // Ambil data dari Controller (Pastikan Controller mengirim data-data ini)
        const jamLabels = {!! json_encode($hours) !!};

        // 1. Line Chart: Status Comparison
        new Chart(document.getElementById('lineStatusChart'), {
            type: 'line',
            data: {
                labels: jamLabels,
                datasets: [{
                        label: 'Completed',
                        data: {!! json_encode($lineStatus['completed']) !!},
                        borderColor: '#28a745',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Pending',
                        data: {!! json_encode($lineStatus['pending']) !!},
                        borderColor: '#ffc107',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Cancelled',
                        data: {!! json_encode($lineStatus['cancelled']) !!},
                        borderColor: '#dc3545',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });

        // 2. Bar Chart: Hourly Volume
        new Chart(document.getElementById('barTrxChart'), {
            type: 'bar',
            data: {
                labels: jamLabels,
                datasets: [{
                    label: 'Total Transaksi',
                    data: {!! json_encode($barTrx) !!},
                    backgroundColor: '#4e73df',
                    borderRadius: 5
                }]
            }
        });

        // 3. Pie Chart: Payment Method
        new Chart(document.getElementById('pieMethodChart'), {
            type: 'pie',
            data: {
                labels: ['Cash', 'Transfer'],
                datasets: [{
                    data: [{{ $pieData['cash'] }}, {{ $pieData['transfer'] }}],
                    backgroundColor: ['#1cc88a', '#4e73df'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 4. Horizontal Bar Chart: Cashier Performance
        new Chart(document.getElementById('horiCashierChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($cashierData->pluck('name')) !!},
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: {!! json_encode($cashierData->pluck('count')) !!},
                    backgroundColor: '#36b9cc',
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true
            }
        });
    </script>
@endpush
