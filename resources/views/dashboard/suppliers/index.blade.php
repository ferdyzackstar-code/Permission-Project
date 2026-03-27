@extends('dashboard.layouts.admin')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Supplier</h1>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSupplierModal">
            <i class="fa fa-plus"></i> Create New Supplier
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="supplier-table" width="100%">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Supplier</th>
                            <th>Email</th>
                            <th>Kota</th>
                            <th>Telepon</th>
                            <th>Total Produk</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('dashboard.suppliers.modals.create')
    @include('dashboard.suppliers.modals.edit')
    @include('dashboard.suppliers.modals.show')
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            var table = $('#supplier-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.suppliers.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'product_qty', 
                        name: 'product_qty',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Handle Show
            $('body').on('click', '.btn-show', function() {
                var id = $(this).data('id');
                $.get("{{ url('dashboard/suppliers') }}/" + id + "/edit", function(data) {
                    $('#showSupplierModal').modal('show');
                    $('#show_name').text(data.name);
                    $('#show_email').text(data.email ?? '-');
                    $('#show_city').text(data.city ?? '-');
                    $('#show_status').text(data.status ?? '-');
                    $('#show_phone').text(data.phone ?? '-');
                    $('#show_address').text(data.address ?? '-');
                });
            });

            // Handle Edit
            $('body').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get("{{ url('dashboard/suppliers') }}/" + id + "/edit", function(data) {
                    $('#editSupplierModal').modal('show');
                    $('#editSupplierForm').attr('action', "{{ url('dashboard/suppliers') }}/" + id);
                    $('#edit_name').val(data.name);
                    $('#edit_email').val(data.email);
                    $('#edit_city').val(data.city);
                    $('#edit_status').val(data.status);
                    $('#edit_phone').val(data.phone);
                    $('#edit_address').val(data.address);
                });
            });

            // Handle Delete
            $('body').on('click', '.show_confirm', function(e) {
                e.preventDefault();
                var form = $(this).closest("form");
                Swal.fire({
                    title: 'Hapus data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
