@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <style>
        /* CSS Tambahan agar tabel lebih clean */
        #table-categories tbody tr {
            background-color: #ffffff;
        }

        #table-categories tbody tr:hover {
            background-color: #dfdfdf;
        }

        #table-categories thead th {
            background-color: #4e73df;
            color: #ffffff;
            border-bottom: none;
            vertical-align: middle;
        }

        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc:after {
            color: #ffffff !important;
            opacity: 0.8;
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3 px-3">
        <h4 class="text-dark font-weight-bold">📁 Manajemen Struktur Kategori</h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="cardTitle"><i class="fas fa-plus-circle mr-1"></i> Tambah
                Kategori Baru</h6>
        </div>
        <div class="card-body bg-light-50">
            <form id="categoryForm" action="{{ route('dashboard.categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="small font-weight-bold">Nama Kategori</label>
                            <input type="text" name="name" id="categoryName" class="form-control"
                                placeholder="Misal: Makanan Kucing" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="small font-weight-bold">Parent Kategori</label>
                            <select name="parent_id" id="parentCategory" class="form-control">
                                <option value="">-- Set Sebagai Kategori Utama --</option>
                                @foreach ($parentCategories as $parent)
                                    <option value="{{ $parent->id }}">Sub dari: {{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="small font-weight-bold">Status</label>
                            <select name="status" id="categoryStatus" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="small font-weight-bold">Deskripsi</label>
                            <textarea name="description" id="categoryDescription" class="form-control" rows="2"
                                placeholder="Penjelasan singkat kategori..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" id="submitBtn">
                            <i class="fa fa-save mr-1"></i> Simpan
                        </button>
                        <button type="reset" class="btn btn-light px-4 border" id="resetBtn">
                            <i class="fa fa-sync mr-1"></i> Batal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table w-100" id="table-categories">
                    <thead class="thead-light">
                        <tr class="bg-primary">
                            <th width="5%">No</th>
                            <th>Struktur Nama Kategori</th>
                            <th>Predikat</th>
                            <th width="10%">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#table-categories').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 25, // Lebih banyak lebih baik untuk melihat struktur
                ajax: "{{ route('dashboard.categories.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name_display',
                        name: 'name'
                    },
                    {
                        data: 'type_badge',
                        name: 'type_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                language: {
                    search: "Cari Kategori:",
                    lengthMenu: "Tampilkan _MENU_ data"
                }
            });

            // LOGIKA EDIT (Opsi 1 & 2 gabungan)
            $(document).on('click', '.editCategory', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let parentId = $(this).data('parent');
                let description = $(this).data('description');
                let status = $(this).data('status');

                $('#categoryName').val(name).focus();
                $('#categoryDescription').val(description);
                $('#categoryStatus').val(status);
                $('#cardTitle').html('<i class="fas fa-edit mr-1"></i> Edit Mode: ' + name);
                $('#submitBtn').html('<i class="fa fa-check mr-1"></i> Update Data').removeClass(
                    'btn-primary').addClass('btn-warning');

                // Jika yang diedit adalah Kategori Utama (parentId kosong/null)
                if (parentId === "" || parentId === null) {
                    $('#parentCategory').val("").attr('readonly', true).css('pointer-events', 'none')
                        .addClass('bg-light');
                } else {
                    $('#parentCategory').val(parentId).attr('readonly', false).css('pointer-events', 'auto')
                        .removeClass('bg-light');
                }

                let updateUrl = "{{ route('dashboard.categories.update', ':id') }}".replace(':id', id);
                $('#categoryForm').attr('action', updateUrl);
                $('#formMethod').val('PUT');

                $('#categoryForm').on('submit', function(e) {
                    // Cek apakah form sedang dalam mode PUT (Edit)
                    if ($('#formMethod').val() === 'PUT') {
                        e.preventDefault(); // Berhenti! Jangan submit dulu.
                        let form = this;

                        Swal.fire({
                            title: "Simpan Perubahan Kategori?",
                            text: "Data kategori akan diperbarui.",
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonText: "Ya, Update!",
                            cancelButtonText: "Batal",
                            confirmButtonColor: "#f6c23e", // Warna kuning warning
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // Baru jalankan submit asli
                            }
                        });
                    }
                    // Jika mode POST (Tambah), biarkan langsung submit tanpa konfirmasi (opsional)
                });
            });

            $('#resetBtn').click(function() {
                $('#cardTitle').html('<i class="fas fa-plus-circle mr-1"></i> Tambah Kategori Baru');
                $('#submitBtn').html('<i class="fa fa-save mr-1"></i> Simpan').removeClass('btn-warning')
                    .addClass('btn-primary');
                $('#formMethod').val('POST');
                $('#parentCategory').attr('readonly', false).css('pointer-events', 'auto').removeClass(
                    'bg-light');
                $('#categoryForm').attr('action', "{{ route('dashboard.categories.store') }}");
            });

            $(document).on('click', '.show_confirm', function(e) {
                e.preventDefault();
                let form = $(this).closest("form");
                Swal.fire({
                    title: 'Hapus Kategori?',
                    text: "Seluruh sub-kategori di dalamnya juga akan terhapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Ya, Hapus Kategori',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
