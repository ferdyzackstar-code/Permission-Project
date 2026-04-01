@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
            <div class="mb-2 mb-lg-0">
                <h4 class="text-dark">Data Users</h4>
            </div>
            <div class="text-right">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCreateUser">
                    <i class="fa fa-plus"></i> Create New User
                </button>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('import_failures'))
        <div class="alert alert-danger" role="alert">
            <strong>Beberapa baris gagal diimport:</strong>
            <ul>
                @foreach (session()->get('import_failures') as $failure)
                    <li>
                        Baris ke-{{ $failure->row() }}:
                        @foreach ($failure->errors() as $error)
                            {{ $error }}
                        @endforeach
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @include('dashboard.users.modals.create', ['roles' => $roles])

    <div class="button-action" style="margin-bottom: 20px">
        <a href="{{ route('dashboard.users.downloadImportTemplate') }}" class="btn btn-warning btn-md">
            <i class="fas fa-file-download"></i> IMPORT TEMPLATE
        </a>

        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#import">
            <i class="fas fa-upload"></i> IMPORT
        </button>

        <a href="{{ route('dashboard.users.export') }}" class="btn btn-primary btn-md">
            <i class="fas fa-file-export"></i> EXPORT
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive mt-2">
                <table class="table table-hover table-bordered" id="data-users">
                    <thead>
                        <tr class="bg-primary">
                            <th width="1px" class="text-center text-white">No</th>
                            <th class="text-center text-white">Name</th>
                            <th class="text-center text-white">Email</th>
                            <th class="text-center text-white">Roles</th>
                            <th width="250px" class="text-center text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($data as $user)
        @include('dashboard.users.modals.show', ['user' => $user])
        @include('dashboard.users.modals.edit', ['user' => $user, 'roles' => $roles])
    @endforeach

    <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">IMPORT DATA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.users.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>PILIH FILE</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">TUTUP</button>
                        <button type="submit" class="btn btn-success">IMPORT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#data-users').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('dashboard.users.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                        className: 'text-center'
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
                        data: 'roles',
                        name: 'roles',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false,
                        className: 'text-center'
                    }
                ]
            });
        });

        $(document).on('click', '.show_confirm', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Yakin hapus data?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'swal2-confirm btn btn-danger mr-2',
                        cancelButton: 'swal2-cancel btn btn-secondary'
                    },
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form[0].submit();
                    }
                });
            } else {
                form[0].submit();
            }
        });
    </script>
@endpush
