@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <h4 class="mb-4"><i class="fa-solid fa-file-invoice-dollar mr-2"></i>Laporan Pemasukan</h4>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('dashboard.reports.income') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Mulai Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option> 
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Metode</label>
                        <select name="payment_method" class="form-control">
                            <option value="">Semua Metode</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>
                                Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="fa fa-filter me-1"></i>
                            Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">Metode Pembayaran</div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="paymentPieChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between">
                        <span>Ringkasan Transaksi</span>
                        <span class="badge bg-light text-dark border">{{ $orders->count() }} Data</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3">Tanggal</th>
                                    <th>Invoice</th>
                                    <th>Kasir</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th class="text-end px-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="px-3 small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="fw-bold text-primary">{{ $order->invoice_number }}</td>
                                        <td>{{ $order->user->name ?? '-' }}</td>
                                        <td>
                                            @if (($order->payment->payment_method ?? '') == 'cash')
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle">Cash</span>
                                            @else
                                                <span
                                                    class="badge bg-info-subtle text-info border border-info-subtle">Transfer</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge text-white @if ($order->status == 'completed') bg-primary @elseif($order->status == 'pending') bg-warning @else bg-danger @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end px-3 fw-bold">Rp
                                            {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Data tidak ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('paymentPieChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData->keys()) !!},
                datasets: [{
                    data: {!! json_encode($chartData->values()) !!},
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endpush
