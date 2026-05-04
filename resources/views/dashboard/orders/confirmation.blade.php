@extends('dashboard.layouts.admin')

@section('title', 'Konfirmasi Pembayaran')

@push('styles')
    <style>
        :root {
            --c-radius: 12px;
        }

        /* ── HEADER KUNING ──────────────────────────────────────────── */
        .conf-header-card {
            background: linear-gradient(135deg, #E65100 0%, #F57F17 50%, #F9A825 100%);
            border-radius: var(--c-radius);
            padding: 20px 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 20px rgba(245, 127, 23, .30);
        }

        .conf-header-card h4 {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
        }

        .conf-header-card p {
            color: rgba(255, 255, 255, .8);
            font-size: .82rem;
            margin: 2px 0 0;
        }

        /* Badge total pending di header */
        .conf-pending-pill {
            background: rgba(255, 255, 255, .22);
            border: 1.5px solid rgba(255, 255, 255, .35);
            color: #fff;
            font-size: .82rem;
            font-weight: 700;
            padding: 7px 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(4px);
            white-space: nowrap;
        }

        /* ── TABLE CARD ─────────────────────────────────────────────── */
        .conf-table-card {
            background: #fff;
            border-radius: var(--c-radius);
            box-shadow: 0 2px 16px rgba(245, 127, 23, .08);
            overflow: hidden;
        }

        .conf-table-card .card-body {
            padding: 20px;
        }

        #confirmation-table thead th {
            background: #FFF8E1 !important;
            color: #795548;
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
            border-top: 1px solid #FFF8E1 !important;
            font-size: .84rem;
            color: #2C3E50;
        }

        #confirmation-table tbody tr:hover {
            background: #FFFDE7;
        }

        #confirmation-table {
            border-collapse: collapse !important;
        }

        .invoice-code {
            font-family: monospace;
            font-size: .78rem;
            background: #FFF3E0;
            color: #E65100;
            padding: 3px 8px;
            border-radius: 5px;
            font-weight: 700;
        }

        /* ── ACTION BUTTONS ─────────────────────────────────────────── */
        /* Wrapper agar tombol rapi dalam 1 baris */
        .action-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: nowrap;
        }

        .btn-approve-conf {
            background: linear-gradient(135deg, #2E7D32, #43A047);
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 7px;
            font-size: .76rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(46, 125, 50, .2);
            white-space: nowrap;
        }

        .btn-approve-conf:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, .3);
        }

        .btn-cancel-conf {
            background: linear-gradient(135deg, #C62828, #E53935);
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 7px;
            font-size: .76rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(198, 40, 40, .2);
            white-space: nowrap;
        }

        .btn-cancel-conf:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(198, 40, 40, .3);
        }

        .btn-struk-conf {
            background: #E3F2FD;
            color: #1565C0;
            border: 1.5px solid #BBDEFB;
            padding: 6px 12px;
            border-radius: 7px;
            font-size: .76rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all .2s;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-struk-conf:hover {
            background: #BBDEFB;
            color: #0D47A1;
            text-decoration: none;
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Header Kuning --}}
        @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
        <div class="conf-header-card">
            <div>
                <h4><i class="fas fa-hourglass-half mr-2"></i>Konfirmasi Pembayaran Transfer</h4>
                <p>Daftar transaksi transfer yang menunggu persetujuan admin</p>
            </div>
            <div class="conf-pending-pill">
                <i class="fas fa-clock"></i>
                {{ $pendingCount }} Menunggu Konfirmasi
            </div>
        </div>

        {{-- Table --}}
        <div class="conf-table-card">
            <div class="card-body">
                <div class="table-responsive">
                    {{-- Hapus kolom STATUS karena semua sudah pasti pending --}}
                    <table id="confirmation-table" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Invoice</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th width="280px" class="text-center">Aksi</th>
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
                    processing: '<div class="text-center py-3"><i class="fas fa-spinner fa-spin" style="color:#F57F17;"></i> Memuat...</div>',
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                columnDefs: [{
                    targets: [0, 4],
                    className: 'text-center'
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
                        }, function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dibatalkan!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                table.ajax.reload();
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
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
