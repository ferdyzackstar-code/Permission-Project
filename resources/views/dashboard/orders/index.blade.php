@extends('dashboard.layouts.admin')

@section('title', 'Riwayat Transaksi')

@push('styles')
    <style>
        :root {
            --ord-primary: #1565C0;
            --ord-accent: #42A5F5;
            --ord-bg: #F0F4F8;
            --ord-radius: 12px;
        }

        .ord-header-card {
            background: linear-gradient(135deg, #0D47A1 0%, #1565C0 60%, #1976D2 100%);
            border-radius: var(--ord-radius);
            padding: 20px 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 20px rgba(21, 101, 192, .25);
        }

        .ord-header-card h4 {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
        }

        .ord-header-card p {
            color: rgba(255, 255, 255, .7);
            font-size: .82rem;
            margin: 2px 0 0;
        }

        .btn-new-trx {
            background: rgba(255, 255, 255, .15);
            border: 1.5px solid rgba(255, 255, 255, .35);
            color: #fff;
            font-size: .82rem;
            font-weight: 700;
            padding: 9px 20px;
            border-radius: 8px;
            backdrop-filter: blur(4px);
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-new-trx:hover {
            background: rgba(255, 255, 255, .28);
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        /* Table Card */
        .ord-table-card {
            background: #fff;
            border-radius: var(--ord-radius);
            box-shadow: 0 2px 16px rgba(21, 101, 192, .07);
            overflow: hidden;
        }

        .ord-table-card .card-body {
            padding: 20px;
        }

        /* DataTable overrides */
        #orders-table thead th {
            background: #F0F4F8 !important;
            color: #546E7A;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            border: none !important;
            padding: 12px 14px !important;
            white-space: nowrap;
        }

        #orders-table tbody td {
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-top: 1px solid #F0F4F8 !important;
            font-size: .84rem;
            color: #2C3E50;
        }

        #orders-table tbody tr:hover {
            background: #F8FAFD;
        }

        #orders-table {
            border-collapse: collapse !important;
        }

        .invoice-code {
            font-family: monospace;
            font-size: .78rem;
            background: #EEF2FF;
            color: #3949AB;
            padding: 3px 8px;
            border-radius: 5px;
            font-weight: 700;
        }

        .badge-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .4px;
        }

        .badge-status.completed {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .badge-status.pending {
            background: #FFF8E1;
            color: #F57F17;
        }

        .badge-status.cancelled {
            background: #FFEBEE;
            color: #C62828;
        }

        .badge-method {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
        }

        .badge-method.cash {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .badge-method.transfer {
            background: #E3F2FD;
            color: #1565C0;
        }

        .btn-struk {
            background: linear-gradient(135deg, #1565C0, #1976D2);
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 8px rgba(21, 101, 192, .2);
        }

        .btn-struk:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(21, 101, 192, .3);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Header Banner --}}
        <div class="ord-header-card">
            <div>
                <h4><i class="fas fa-clock-rotate-left mr-2"></i>Riwayat Transaksi</h4>
                <p>Semua transaksi penjualan Anda Petshop</p>
            </div>
            <a href="{{ route('dashboard.orders.pos') }}" class="btn-new-trx">
                <i class="fas fa-plus-circle"></i> Transaksi Baru
            </a>
        </div>

        {{-- Table --}}
        <div class="ord-table-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="orders-table" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th width="40px">No</th>
                                <th>Invoice</th>
                                <th>Kasir</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th width="90px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#orders-table').DataTable({
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.orders.index') }}",
                language: {
                    processing: '<div class="text-center py-3"><i class="fas fa-spinner fa-spin text-primary mr-2"></i>Memuat data...</div>',
                    emptyTable: '<div class="text-center py-4 text-muted"><i class="fas fa-receipt d-block mb-2" style="font-size:1.8rem;opacity:.3;"></i>Belum ada transaksi</div>',
                    search: '',
                    searchPlaceholder: 'Cari invoice, kasir...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ transaksi',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    },
                },
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
                        data: 'payment_method',
                        name: 'payment_method',
                        orderable: false
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                columnDefs: [{
                    targets: [0, 4, 6, 7],
                    className: 'text-center align-middle'
                }, ],
                order: [
                    [3, 'desc']
                ],
                dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>',
            });

            // Struk
            $(document).on('click', '.btn-struk', function() {
                const id = $(this).data('id');
                const url = "{{ route('dashboard.orders.receipt', ':id') }}".replace(':id', id);
                window.location.href = url;
            });
        });
    </script>
@endpush
