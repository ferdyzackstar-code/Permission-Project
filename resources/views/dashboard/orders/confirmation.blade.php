@extends('dashboard.layouts.admin')

@section('title', 'Konfirmasi Pembayaran')

@push('styles')
    <style>
        :root {
            --c-primary: #1565C0;
            --c-radius: 12px;
        }

        .conf-header-card {
            background: linear-gradient(135deg, #0D47A1, #1976D2);
            border-radius: var(--c-radius);
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(21, 101, 192, .25);
        }

        .conf-header-card h4 {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
        }

        .conf-header-card p {
            color: rgba(255, 255, 255, .7);
            font-size: .82rem;
            margin: 2px 0 0;
        }

        .conf-table-card {
            background: #fff;
            border-radius: var(--c-radius);
            box-shadow: 0 2px 16px rgba(21, 101, 192, .07);
            overflow: hidden;
        }

        .conf-table-card .card-body {
            padding: 20px;
        }

        #confirmation-table thead th {
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

        #confirmation-table tbody td {
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-top: 1px solid #F0F4F8 !important;
            font-size: .84rem;
        }

        #confirmation-table tbody tr:hover {
            background: #F8FAFD;
        }

        .invoice-code {
            font-family: monospace;
            background: #EEF2FF;
            color: #3949AB;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: .78rem;
            font-weight: 700;
        }

        .action-group {
            display: flex;
            gap: 6px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-approve-trx {
            background: linear-gradient(135deg, #2E7D32, #43A047);
            color: #fff;
            border: none;
            padding: 6px 13px;
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(46, 125, 50, .2);
        }

        .btn-approve-trx:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, .3);
        }

        .btn-cancel-trx {
            background: linear-gradient(135deg, #C62828, #E53935);
            color: #fff;
            border: none;
            padding: 6px 13px;
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(198, 40, 40, .2);
        }

        .btn-cancel-trx:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(198, 40, 40, .3);
        }

        .btn-struk-sm {
            background: #E3F2FD;
            color: #1565C0;
            border: none;
            padding: 6px 13px;
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all .2s;
            text-decoration: none;
        }

        .btn-struk-sm:hover {
            background: #BBDEFB;
            color: #0D47A1;
            text-decoration: none;
        }

        /* Pulse badge untuk pending --*/
        .pulse-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #FFF8E1;
            color: #F57F17;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
        }

        .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #F57F17;
            animation: pulse-anim 1.4s ease infinite;
        }

        @keyframes pulse-anim {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .5;
                transform: scale(1.3);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <div class="conf-header-card">
            <h4><i class="fas fa-hourglass-half mr-2"></i>Konfirmasi Pembayaran Transfer</h4>
            <p>Daftar transaksi transfer yang menunggu persetujuan admin</p>
        </div>

        <div class="conf-table-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="confirmation-table" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th width="40px">No</th>
                                <th>Invoice</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th width="240px" class="text-center">Aksi</th>
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
            const table = $('#confirmation-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.orders.confirmation') }}",
                language: {
                    processing: '<div class="text-center py-3"><i class="fas fa-spinner fa-spin text-primary mr-2"></i>Memuat...</div>',
                    emptyTable: '<div class="text-center py-4 text-muted"><i class="fas fa-check-circle d-block mb-2 text-success" style="font-size:1.8rem;"></i>Tidak ada transaksi yang menunggu konfirmasi</div>',
                    search: '',
                    searchPlaceholder: 'Cari invoice...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
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
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                columnDefs: [{
                    targets: [0, 4, 5],
                    className: 'text-center align-middle'
                }, ],
                dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>',
            });

            // Approve
            $(document).on('click', '.btn-approve', function() {
                const id = $(this).data('id');
                const url = "{{ route('dashboard.orders.approve', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Approve Transaksi?',
                    text: 'Pastikan dana transfer sudah masuk ke rekening toko.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2E7D32',
                    cancelButtonColor: '#78909C',
                    confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Approve',
                    cancelButtonText: 'Batal',
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post(url, {
                            _token: "{{ csrf_token() }}"
                        }, function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Transaksi telah disetujui.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            table.ajax.reload();
                        });
                    }
                });
            });

            // Cancel
            $(document).on('click', '.btn-cancel', function() {
                const id = $(this).data('id');
                const url = "{{ route('dashboard.orders.cancel', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Batalkan Transaksi?',
                    text: 'Stok produk akan dikembalikan otomatis.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#C62828',
                    cancelButtonColor: '#78909C',
                    confirmButtonText: '<i class="fas fa-times mr-1"></i> Ya, Batalkan',
                    cancelButtonText: 'Kembali',
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post(url, {
                            _token: "{{ csrf_token() }}"
                        }, function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dibatalkan!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                table.ajax.reload();
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        }).fail(xhr => {
                            Swal.fire('Error!', xhr.responseJSON?.message ??
                                'Terjadi kesalahan.', 'error');
                        });
                    }
                });
            });
        });
    </script>
@endpush
