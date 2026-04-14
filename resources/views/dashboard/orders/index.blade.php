@extends('dashboard.layouts.admin')

@section('content')
    <style>
        #orders-table {
            width: 100% !important;
        }

        #orders-table th,
        #orders-table td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 8px !important;
        }
    </style>
    <div class="container-fluid">
        <div class="card border-0 shadow-sm pt-2">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Riwayat Transaksi
                </h5>
                <a href="{{ route('dashboard.orders.pos') }}" class="btn btn-primary btn-sm px-5">
                    <i class="fa fa-plus mr-1"></i> Transaksi Baru
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="orders-table">
                        <thead>
                            <tr class="bg-primary border-bottom">
                                <th width='1%' class="text-center text-white border-start border-end">No</th>
                                <th class="text-center text-white border-start border-end">Invoice</th>
                                <th class="text-center text-white border-start border-end">Kasir</th>
                                <th class="text-center text-white border-start border-end">Tanggal</th>
                                <th class="text-center text-white border-start border-end">Metode</th>
                                <th class="text-center text-white border-start border-end">Total</th>
                                <th class="text-center text-white border-start border-end">Status</th>
                                <th width='120px' class="text-center text-white border-start border-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0"></tbody>
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
            let table = $('#orders-table').DataTable({
                autoWidth: false,
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
                        data: 'payment_method',
                        name: 'payment_method'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'status',
                        name: 'status',
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
                    className: "text-center align-middle"
                }, ]
            });

            // 1. Aksi Tombol Detail
            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                // Arahkan ke halaman show
                let url = "{{ route('dashboard.orders.receipt', ':id') }}".replace(':id', id);
                window.location.href = url;
            });

            // 2. Aksi Tombol Approve
            $(document).on('click', '.btn-approve', function() {
                let id = $(this).data('id');
                // Pastikan kamu punya route untuk approve ini (bisa arahkan ke method approve yang sudah kamu buat)
                let url = "{{ route('dashboard.orders.approve', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: "Konfirmasi Pembayaran?",
                    text: "Pastikan dana sudah masuk ke rekening toko.",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Ya, Approve!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST', // Ingat, method approve kamu pakai POST
                            data: {
                                _token: "{{ csrf_token() }}" // Wajib ada untuk keamanan Laravel
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire("Berhasil!", "Pembayaran disetujui.",
                                        "success");
                                    // Reload tabel tanpa memindahkan halaman pagination
                                    table.ajax.reload(null, false);
                                }
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Swal.fire("Error!",
                                    "Terjadi kesalahan saat menyetujui pembayaran.",
                                    "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
