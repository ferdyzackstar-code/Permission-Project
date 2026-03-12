@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Outlet</h1>
        @can('outlet-create')
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addOutletModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Outlet Baru
            </button>
        @endcan
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="data-outlets" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Outlet</th>
                            <th class="text-center">Alamat</th>
                            <th class="text-center">Telepon</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Include Modals --}}
    @include('dashboard.outlets.modals.create')
    @include('dashboard.outlets.modals.edit')
    @include('dashboard.outlets.modals.show')
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#data-outlets').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('dashboard.outlets.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                columnDefs: [{
                        targets: 0, // Kolom No tetap sempit
                        width: "1%",
                        className: "text-center align-middle"
                    },
                    {
                        targets: 4, // Kolom Aksi dilebarkan agar muat teks Show, Edit, Delete
                        width: "20%",
                        className: "text-center align-middle",
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: [1, 2, 3],
                        className: "align-middle"
                    }
                ]
            });

            // Delegasi Event untuk tombol delete (karena tombol dibuat secara dinamis)
            $(document).on('click', '.btn-delete', function() {
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'Yakin ingin hapus data outlet?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
