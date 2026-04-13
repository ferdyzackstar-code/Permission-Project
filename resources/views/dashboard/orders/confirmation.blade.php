@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0 text-warning">Menunggu Konfirmasi Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="confirmation-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Invoice</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
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
                ]
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
                let url = "{{ route('dashboard.orders.cancel', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: "Batalkan Transaksi?",
                    text: "Transaksi ini akan di-cancel dan stok barang akan dikembalikan.",
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Batalkan!",
                    cancelButtonText: "Kembali",
                    confirmButtonColor: "#dc3545" // Warna merah
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                            _token: "{{ csrf_token() }}"
                        }, function(response) {
                            if (response.success) {
                                Swal.fire("Dibatalkan!", response.message, "success");
                                table.ajax.reload(); // Refresh data
                            } else {
                                Swal.fire("Gagal!", response.message, "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
