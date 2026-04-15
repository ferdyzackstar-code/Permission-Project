@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm pt-2">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-dark"><i class="fa-solid fa-hourglass-half"></i> Menunggu Konfirmasi Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="confirmation-table">
                        <thead>
                            <tr class="bg-primary border-bottom">
                                <th width='1px' class="text-center text-white border-start border-end">No</th>
                                <th class="text-center text-white border-start border-end">Invoice</th>
                                <th class="text-center text-white border-start border-end">Kasir</th>
                                <th class="text-center text-white border-start border-end">Total</th>
                                <th width='272px' class="text-center text-white border-start border-end">Aksi</th>
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
    <script>
        $(document).ready(function() {
            let table = $('#confirmation-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.orders.confirmation') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex' 
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
                    className: "text-center align-middle"
                }, ]
            });

            $(document).on('click', '.btn-approve', function() {
                let id = $(this).data('id');
                let url = "{{ route('dashboard.orders.approve', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: "Approve Transaksi?",
                    text: "Pastikan saldo sudah masuk ke rekening toko!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Sudah Masuk!",
                    confirmButtonColor: "#28a745"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                            _token: "{{ csrf_token() }}"
                        }, function() {
                            Swal.fire("Berhasil!", "Transaksi selesai.", "success");
                            table.ajax
                                .reload();
                        });
                    }
                });
            });
            $(document).on('click', '.btn-cancel', function() {
                let id = $(this).data('id');
                // Pastikan URL yang dihasilkan benar
                let url = "{{ route('dashboard.orders.cancel', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: "Batalkan Transaksi?",
                    text: "Transaksi ini akan di-cancel dan stok barang akan dikembalikan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Batalkan!",
                    cancelButtonText: "Kembali",
                    confirmButtonColor: "#dc3545"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                            _token: "{{ csrf_token() }}"
                        }, function(response) {
                            if (response.success) {
                                Swal.fire("Dibatalkan!", response.message, "success");
                                table.ajax.reload();
                            } else {
                                Swal.fire("Gagal!", response.message, "error");
                            }
                        }).fail(function(xhr) {
                            // Menampilkan pesan error asli dari Laravel jika ada crash
                            let errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                                "Terjadi kesalahan sistem.";
                            Swal.fire("Error!", errorMsg, "error");
                        });
                    }
                });
            });
        });
    </script>
@endpush
