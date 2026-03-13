@extends('dashboard.layouts.admin')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Supplier</h1>
        @can('supplier-create')
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSupplierModal">
                <i class="fa fa-plus"></i> Create New Supplier
            </button>
        @endcan
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="supplier-table" width="100%">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama Supplier</th>
                            <th>Produk</th>
                            <th>Harga Beli</th>
                            <th>Telepon</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Create --}}
    @include('dashboard.suppliers.modals.create')

    {{-- Modal Edit, Show, Delete (Harus di-looping karena menggunakan data-target unik) --}}
    @foreach ($suppliers as $supplier)
        @include('dashboard.suppliers.modals.edit', ['supplier' => $supplier])
        @include('dashboard.suppliers.modals.show', ['supplier' => $supplier])
    @endforeach
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // DataTable
            $('#supplier-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.suppliers.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center'
                    },
                    {
                        data: 'item_code',
                        name: 'item_code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'purchase_price',
                        name: 'purchase_price'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ]
            });

            // Konfirmasi Hapus (SweetAlert2)
            $('body').on('click', '.show_confirm', function(event) {
                var form = $(this).closest("form");
                event.preventDefault();
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Data akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
