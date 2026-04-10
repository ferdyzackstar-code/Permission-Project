@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm pt-2">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Riwayat Transaksi</h5>
                <a href="{{ route('dashboard.orders.pos') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus me-1"></i> Transaksi Baru
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="orders-table">
                    <thead>
                        <tr class="bg-primary">
                            <th width='1px' class="text-center text-white">No</th>
                            <th class="text-center text-white">Invoice</th>
                            <th class="text-center text-white">Kasir</th>
                            <th class="text-center text-white">Tanggal</th>
                            <th class="text-center text-white">Total</th>
                            <th class="text-center text-white">Status</th>
                            <th class="text-center text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.orders.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            let color = data == 'completed' ? 'success' : 'warning';
                            return `<span class="badge badge-${color}">${data.toUpperCase()}</span>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });
    </script>
@endpush
