@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <h4 class="fw-bold mb-4"><i class="fa-solid fa-clock-rotate-left me-2"></i>Laporan Per Jam:
            {{ date('d M Y', strtotime($date)) }}</h4>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('dashboard.reports.hourly') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ $date }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Metode</label>
                        <select name="payment_method" class="form-control">
                            <option value="">Semua Metode</option>
                            <option value="cash" {{ $methodFilter == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ $methodFilter == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2">Filter</button>
                        <a href="{{ route('dashboard.reports.hourly.export', request()->all()) }}" class="btn btn-danger">PDF</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold small">Tren Status Per Jam (Line Chart)</div>
                    <div class="card-body"><canvas id="statusChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold small">Perbandingan Metode (Horizontal Bar)</div>
                    <div class="card-body"><canvas id="methodChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold small">Total Transaksi</div>
                    <div class="card-body"><canvas id="trxChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold small">Pendapatan (Revenue)</div>
                    <div class="card-body"><canvas id="revChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center small">
                    <thead class="bg-light">
                        <tr>
                            <th rowspan="2">Jam</th>
                            <th colspan="3">Status (Jumlah)</th>
                            <th colspan="2">Metode (IDR)</th>
                            <th rowspan="2">Total Revenue</th>
                        </tr>
                        <tr>
                            <th class="text-success">Done</th>
                            <th class="text-warning">Wait</th>
                            <th class="text-danger">Cancel</th>
                            <th>Cash</th>
                            <th>Transfer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $data)
                            <tr>
                                <td class="fw-bold">{{ $data['hour_label'] }}</td>
                                <td>{{ $data['count_completed'] }}</td>
                                <td>{{ $data['count_pending'] }}</td>
                                <td>{{ $data['count_cancelled'] }}</td>
                                <td class="text-end">Rp{{ number_format($data['rev_cash'], 0, ',', '.') }}</td>
                                <td class="text-end">Rp{{ number_format($data['rev_transfer'], 0, ',', '.') }}</td>
                                <td class="fw-bold text-primary">Rp{{ number_format($data['total_revenue'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const labels = {!! json_encode(array_column($reportData, 'hour_label')) !!};

            // B. Chart Line (Status)
            new Chart(document.getElementById('statusChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Completed',
                            data: {!! json_encode(array_column($reportData, 'count_completed')) !!},
                            borderColor: '#198754',
                            tension: 0.3
                        },
                        {
                            label: 'Pending',
                            data: {!! json_encode(array_column($reportData, 'count_pending')) !!},
                            borderColor: '#ffc107',
                            tension: 0.3
                        },
                        {
                            label: 'Cancelled',
                            data: {!! json_encode(array_column($reportData, 'count_cancelled')) !!},
                            borderColor: '#dc3545',
                            tension: 0.3
                        }
                    ]
                }
            });

            // C. Chart Bar Horizontal (Metode)
            new Chart(document.getElementById('methodChart'), {
                type: 'bar',
                data: {
                    labels: ['Cash', 'Transfer'],
                    datasets: [{
                        label: 'Total IDR',
                        data: [{{ collect($reportData)->sum('rev_cash') }},
                            {{ collect($reportData)->sum('rev_transfer') }}
                        ],
                        backgroundColor: ['#4e73df', '#1cc88a']
                    }]
                },
                options: {
                    indexAxis: 'y'
                } // Ini kuncinya biar horizontal
            });

            // D. Total Transaksi (Bar Biasa)
            new Chart(document.getElementById('trxChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Trx',
                        data: {!! json_encode(array_column($reportData, 'total_transactions')) !!},
                        backgroundColor: '#36b9cc'
                    }]
                }
            });

            // E. Pendapatan (Line Area)
            new Chart(document.getElementById('revChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode(array_column($reportData, 'total_revenue')) !!},
                        fill: true,
                        borderColor: '#4e73df'
                    }]
                }
            });
        </script>
    @endpush
@endsection
