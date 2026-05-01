@extends('dashboard.layouts.admin')

@push('styles')
    {{-- DataTables --}}
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
    {{-- Animate.css for modal entrance --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════
               ROOT TOKENS — SB Admin 2 Blue Palette
            ═══════════════════════════════════════════ */
        :root {
            --brand-primary: #4e73df;
            --brand-secondary: #224abe;
            --brand-accent: #36b9cc;
            --brand-success: #1cc88a;
            --brand-warning: #f6c23e;
            --brand-danger: #e74a3b;
            --brand-light: #f8f9fc;
            --brand-dark: #5a5c69;
            --card-shadow: 0 4px 24px rgba(78, 115, 223, .12);
            --card-radius: .85rem;
            --transition-base: .2s ease;
        }

        /* ═══════════════════════════════════════════
               PAGE HEADER
            ═══════════════════════════════════════════ */
        .page-header-wrap {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border-radius: var(--card-radius);
            padding: 1.4rem 1.8rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .page-header-wrap::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .07);
        }

        .page-header-wrap::after {
            content: '';
            position: absolute;
            bottom: -60px;
            right: 60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .05);
        }

        .page-header-wrap .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
            letter-spacing: .3px;
        }

        .page-header-wrap .page-subtitle {
            font-size: .8rem;
            color: rgba(255, 255, 255, .75);
            margin: .15rem 0 0;
        }

        /* ═══════════════════════════════════════════
               STAT SUMMARY CHIPS
            ═══════════════════════════════════════════ */
        .stat-chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: rgba(255, 255, 255, .15);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 2rem;
            padding: .35rem .85rem;
            font-size: .78rem;
            color: #fff;
            font-weight: 600;
        }

        .stat-chip i {
            font-size: .7rem;
            opacity: .85;
        }

        /* ═══════════════════════════════════════════
               ACTION TOOLBAR
            ═══════════════════════════════════════════ */
        .action-toolbar {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1rem 1.25rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.25rem;
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
            align-items: center;
        }

        .action-toolbar .toolbar-label {
            font-size: .72rem;
            font-weight: 700;
            color: var(--brand-dark);
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-right: auto;
        }

        /* ═══════════════════════════════════════════
               BUTTONS
            ═══════════════════════════════════════════ */
        .btn-icon-label {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .8rem;
            font-weight: 600;
            padding: .45rem 1rem;
            border-radius: .5rem;
            transition: all var(--transition-base);
            border: none;
            letter-spacing: .2px;
        }

        .btn-icon-label i {
            font-size: .75rem;
        }

        .btn-create {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: #fff;
            box-shadow: 0 4px 12px rgba(78, 115, 223, .35);
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(78, 115, 223, .45);
            color: #fff;
        }

        .btn-template {
            background: #fff;
            color: var(--brand-warning);
            border: 1.5px solid var(--brand-warning);
        }

        .btn-template:hover {
            background: var(--brand-warning);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(246, 194, 62, .35);
        }

        .btn-import {
            background: #fff;
            color: var(--brand-success);
            border: 1.5px solid var(--brand-success);
        }

        .btn-import:hover {
            background: var(--brand-success);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(28, 200, 138, .35);
        }

        .btn-export {
            background: #fff;
            color: var(--brand-primary);
            border: 1.5px solid var(--brand-primary);
        }

        .btn-export:hover {
            background: var(--brand-primary);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, .35);
        }

        /* ═══════════════════════════════════════════
               CARD / TABLE
            ═══════════════════════════════════════════ */
        .product-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .product-card .card-header-custom {
            background: linear-gradient(135deg, #f8f9fc, #eaecf4);
            border-bottom: 2px solid #e3e6f0;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .product-card .card-header-custom .header-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #4e73df, #224abe);
            border-radius: .45rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .75rem;
        }

        .product-card .card-header-custom .header-title {
            font-size: .9rem;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .product-card .card-header-custom .record-count {
            margin-left: auto;
            font-size: .72rem;
            color: #858796;
            font-weight: 500;
        }

        /* Table overrides */
        #data-products thead tr {
            background: linear-gradient(135deg, #4e73df, #224abe) !important;
        }

        #data-products thead th {
            color: #fff !important;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            border: none !important;
            padding: .9rem .75rem;
        }

        #data-products tbody tr {
            transition: background var(--transition-base), transform var(--transition-base);
        }

        #data-products tbody tr:hover {
            background: #f0f4ff !important;
        }

        #data-products tbody td {
            vertical-align: middle;
            font-size: .83rem;
            color: #5a5c69;
            border-color: #f0f0f5;
            padding: .75rem;
        }

        /* Product image in table */
        .tbl-product-img {
            width: 48px;
            height: 48px;
            border-radius: .45rem;
            object-fit: cover;
            border: 2px solid #e3e6f0;
            transition: transform .2s, box-shadow .2s;
        }

        .tbl-product-img:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
        }

        /* Status badges */
        .badge-status {
            font-size: .7rem;
            font-weight: 700;
            padding: .35em .8em;
            border-radius: 2rem;
            letter-spacing: .3px;
        }

        .badge-active {
            background: rgba(28, 200, 138, .12);
            color: #169a6b;
            border: 1px solid rgba(28, 200, 138, .3);
        }

        .badge-inactive {
            background: rgba(231, 74, 59, .1);
            color: #c0392b;
            border: 1px solid rgba(231, 74, 59, .25);
        }

        /* Stock indicator */
        .stock-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .75rem;
            font-weight: 700;
            padding: .25em .7em;
            border-radius: 2rem;
        }

        .stock-ok {
            background: rgba(28, 200, 138, .1);
            color: #169a6b;
        }

        .stock-low {
            background: rgba(246, 194, 62, .15);
            color: #b8860b;
        }

        .stock-zero {
            background: rgba(231, 74, 59, .1);
            color: #c0392b;
        }

        /* Action buttons in table */
        .tbl-actions {
            display: flex;
            gap: .35rem;
            justify-content: center;
        }

        .tbl-btn {
            width: 30px;
            height: 30px;
            border-radius: .4rem;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            transition: all var(--transition-base);
            cursor: pointer;
        }

        .tbl-btn-view {
            background: rgba(54, 185, 204, .12);
            color: var(--brand-accent);
        }

        .tbl-btn-view:hover {
            background: var(--brand-accent);
            color: #fff;
            transform: translateY(-2px);
        }

        .tbl-btn-edit {
            background: rgba(246, 194, 62, .12);
            color: #d4a017;
        }

        .tbl-btn-edit:hover {
            background: var(--brand-warning);
            color: #fff;
            transform: translateY(-2px);
        }

        .tbl-btn-delete {
            background: rgba(231, 74, 59, .1);
            color: var(--brand-danger);
        }

        .tbl-btn-delete:hover {
            background: var(--brand-danger);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ═══════════════════════════════════════════
               ALERT STYLES
            ═══════════════════════════════════════════ */
        .alert-modern {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
        }

        .alert-modern.alert-warning {
            background: linear-gradient(135deg, #fff9e6, #fffdf0);
            border-left: 4px solid var(--brand-warning);
        }

        .alert-modern.alert-success {
            background: linear-gradient(135deg, #f0fff8, #f7fffc);
            border-left: 4px solid var(--brand-success);
        }

        /* ═══════════════════════════════════════════
               DATATABLE FILTER CUSTOMIZATION
            ═══════════════════════════════════════════ */
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #e3e6f0;
            border-radius: .45rem;
            padding: .35rem .75rem;
            font-size: .82rem;
            transition: border-color .2s;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, .1);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid #e3e6f0;
            border-radius: .45rem;
            padding: .3rem .5rem;
            font-size: .82rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: #fff !important;
            border-radius: .4rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #eaecf4 !important;
            border-color: #eaecf4 !important;
            color: var(--brand-primary) !important;
            border-radius: .4rem;
        }

        .dataTables_wrapper .dataTables_info {
            font-size: .78rem;
            color: #858796;
        }

        /* ═══════════════════════════════════════════
               RESPONSIVE TWEAKS
            ═══════════════════════════════════════════ */
        @media (max-width: 768px) {
            .page-header-wrap {
                padding: 1rem 1.2rem;
            }

            .action-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-icon-label {
                justify-content: center;
            }

            .stat-chip {
                font-size: .7rem;
            }
        }

        /* Loading overlay for DataTable */
        .dataTables_processing {
            background: rgba(255, 255, 255, .92) !important;
            border: none !important;
            box-shadow: var(--card-shadow) !important;
            border-radius: .5rem !important;
            font-size: .82rem !important;
            color: var(--brand-primary) !important;
            font-weight: 600 !important;
        }
    </style>
@endpush

@section('content')

    {{-- ══════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════ --}}
    <div class="page-header-wrap animate__animated animate__fadeInDown animate__faster">
        <div class="d-flex flex-wrap align-items-center">
            <div class="mr-auto">
                <h4 class="page-title">
                    <i class="fas fa-box-open mr-2" style="opacity:.85"></i> Manajemen Produk
                </h4>
                <p class="page-subtitle">Kelola data produk petshop kamu dengan mudah</p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0" style="gap:.5rem">
                <span class="stat-chip"><i class="fas fa-circle"></i> Total Produk</span>
                @can('product.create')
                    <button class="btn-icon-label btn-create ml-1" data-toggle="modal" data-target="#modalCreateProduct">
                        <i class="fas fa-plus"></i> Tambah Produk
                    </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         FLASH MESSAGES
    ══════════════════════════════════════ --}}
    @if (session('import_failures'))
        <div class="alert alert-modern alert-warning alert-dismissible fade show animate__animated animate__fadeIn"
            role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Import selesai dengan {{ session('import_total_failed') }} baris gagal:</strong>
            </div>
            <ul class="mb-0 pl-3" style="max-height:180px;overflow-y:auto">
                @foreach (session('import_failures') as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <hr class="my-2">
            <p class="mb-0 small text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Baris yang valid tetap berhasil diimport. Perbaiki baris di atas lalu import ulang.
            </p>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-modern alert-success alert-dismissible fade show animate__animated animate__fadeIn"
            role="alert">
            <i class="fas fa-check-circle mr-2" style="color:var(--brand-success)"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════
         ACTION TOOLBAR
    ══════════════════════════════════════ --}}
    <div class="action-toolbar animate__animated animate__fadeInUp animate__faster">
        <span class="toolbar-label"><i class="fas fa-tools mr-1"></i> Aksi Data</span>

        <a href="{{ route('dashboard.products.downloadImportTemplate') }}" class="btn-icon-label btn-template">
            <i class="fas fa-file-download"></i> Template
        </a>

        <button type="button" class="btn-icon-label btn-import" data-toggle="modal" data-target="#import">
            <i class="fas fa-upload"></i> Import
        </button>

        <a href="{{ route('dashboard.products.export') }}" class="btn-icon-label btn-export">
            <i class="fas fa-file-export"></i> Export
        </a>
    </div>

    {{-- ══════════════════════════════════════
         PRODUCT TABLE CARD
    ══════════════════════════════════════ --}}
    <div class="card product-card animate__animated animate__fadeInUp animate__faster">
        <div class="card-header-custom">
            <div class="header-icon"><i class="fas fa-th-list"></i></div>
            <span class="header-title">Daftar Produk</span>
            <span class="record-count" id="record-count-label">Memuat data...</span>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive mt-1">
                <table class="table table-hover table-bordered" id="data-products" style="width:100%">
                    <thead>
                        <tr>
                            <th width="40px" class="text-center">No</th>
                            <th class="text-center">Foto</th>
                            <th>Nama Produk</th>
                            <th>Spesies</th>
                            <th>Kategori</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Harga</th>
                            <th class="text-center">Stok</th>
                            <th width="120px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODALS — CREATE
    ══════════════════════════════════════ --}}
    @include('dashboard.products.modals.create')

    {{-- ══════════════════════════════════════
         MODALS — SHOW & EDIT (per product)
    ══════════════════════════════════════ --}}
    @canany(['product.show', 'product.edit', 'product.delete'])
        @foreach ($products as $product)
            @can('product.show')
                @include('dashboard.products.modals.show', ['product' => $product])
            @endcan
            @can('product.edit')
                @include('dashboard.products.modals.edit', ['product' => $product])
            @endcan
        @endforeach
    @endcanany

    {{-- ══════════════════════════════════════
         MODAL — IMPORT
    ══════════════════════════════════════ --}}
    <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content"
                style="border:none;border-radius:var(--card-radius);overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.18)">
                <div class="modal-header" style="background:linear-gradient(135deg,#1cc88a,#17a673);border:none">
                    <div class="d-flex align-items-center gap-2">
                        <div
                            style="width:34px;height:34px;background:rgba(255,255,255,.2);border-radius:.45rem;display:flex;align-items:center;justify-content:center;margin-right:.6rem">
                            <i class="fas fa-upload text-white"></i>
                        </div>
                        <h5 class="modal-title text-white font-weight-bold mb-0">Import Data Produk</h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" style="opacity:.8">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        {{-- Drop zone --}}
                        <div id="dropZone" class="drop-zone-area" onclick="document.getElementById('importFile').click()"
                            style="border:2.5px dashed #d1d3e2;border-radius:.75rem;padding:2.5rem 1.5rem;text-align:center;cursor:pointer;transition:all .2s;background:#f8f9fc">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:#b7bac7"></i>
                            <p class="mb-1 font-weight-600" style="color:#5a5c69;font-size:.9rem">
                                Klik atau drag &amp; drop file di sini
                            </p>
                            <p class="mb-0 small text-muted">Format: .xlsx, .xls, .csv</p>
                            <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" required
                                class="d-none" onchange="handleImportFile(this)">
                        </div>
                        <div id="fileSelected" class="mt-3 d-none">
                            <div class="d-flex align-items-center p-2 rounded"
                                style="background:#f0fff8;border:1px solid #c3f0dc">
                                <i class="fas fa-file-excel mr-2" style="color:#1cc88a"></i>
                                <span id="fileName" class="small font-weight-600" style="color:#169a6b"></span>
                                <button type="button" class="ml-auto btn btn-sm btn-link text-danger p-0"
                                    onclick="clearImportFile()"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="mt-3 p-3 rounded" style="background:#fff9e6;border-left:3px solid #f6c23e">
                            <p class="mb-1 small font-weight-700" style="color:#856404">
                                <i class="fas fa-lightbulb mr-1"></i> Tips Import:
                            </p>
                            <ul class="mb-0 pl-3 small text-muted">
                                <li>Gunakan template yang telah disediakan</li>
                                <li>Pastikan kolom tidak ada yang kosong</li>
                                <li>Baris yang valid tetap diimport meski ada error</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #f0f0f5;background:#fafbff">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-icon-label btn-import">
                            <i class="fas fa-upload"></i> Mulai Import
                        </button>
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

    <script>
        $(document).ready(function() {

            /* ── DataTable ─────────────────────────────── */
            var table = $('#data-products').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                language: {
                    processing: '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...',
                    search: '',
                    searchPlaceholder: 'Cari produk...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ produk',
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    },
                    emptyTable: '<div class="text-center py-4"><i class="fas fa-box-open fa-2x text-muted mb-2"></i><br>Tidak ada produk ditemukan</div>',
                },
                ajax: {
                    url: "{{ route('dashboard.products.index') }}",
                    error: function(xhr) {
                        console.error('DataTable Error:', xhr.responseText);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'species',
                        name: 'species'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                columnDefs: [{
                        targets: [0, 1, 5, 7, 8],
                        className: 'text-center align-middle'
                    },
                    {
                        targets: 6,
                        className: 'text-right align-middle'
                    },
                    {
                        targets: [2, 3, 4],
                        className: 'align-middle'
                    },
                ],
                order: [
                    [2, 'asc']
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var total = api.page.info().recordsTotal;
                    $('#record-count-label').text(total + ' produk terdaftar');
                }
            });

            /* ── Sub-category fetch ─────────────────────── */
            function fetchSubCategories(parentId, targetSelect) {
                if (!parentId) {
                    targetSelect.prop('disabled', true).empty()
                        .append('<option value="">-- Pilih Species Dulu --</option>');
                    return;
                }
                targetSelect.empty()
                    .append('<option value=""><i class="fas fa-spinner fa-spin"></i> Memuat...</option>')
                    .prop('disabled', true);

                $.ajax({
                    url: "{{ url('dashboard/get-subcategories') }}/" + parentId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        targetSelect.empty().append('<option value="">-- Pilih Kategori --</option>');
                        if (data.length > 0) {
                            $.each(data, function(k, v) {
                                targetSelect.append('<option value="' + v.id + '">' + v.name +
                                    '</option>');
                            });
                            targetSelect.prop('disabled', false);
                        } else {
                            targetSelect.append('<option value="">Tidak ada sub-kategori</option>');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengambil data kategori.', 'error');
                        targetSelect.prop('disabled', false).empty()
                            .append('<option value="">-- Error --</option>');
                    }
                });
            }

            $(document).on('change', '#species_select', function() {
                fetchSubCategories($(this).val(), $('#category_select'));
            });

            $(document).on('change', '.species-edit', function() {
                fetchSubCategories($(this).val(), $('#category_edit' + $(this).data('product-id')));
            });

            /* ── Rupiah formatting ──────────────────────── */
            $(document).on('keyup', '.input-rupiah', function() {
                $(this).val(formatRupiah($(this).val()));
            });

            function formatRupiah(angka) {
                var str = angka.replace(/[^,\d]/g, '').toString();
                var split = str.split(',');
                var sisa = split[0].length % 3;
                var rupiah = split[0].substr(0, sisa);
                var ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
                return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            }

            $(document).on('submit', 'form', function() {
                $(this).find('.input-rupiah').each(function() {
                    $(this).val($(this).val().replace(/\./g, ''));
                });
            });

            /* ── Delete confirmation (SweetAlert) ──────── */
            $(document).on('click', '.btn-delete-product', function() {
                var form = $(this).closest('form');
                var name = $(this).data('name') || 'produk ini';
                Swal.fire({
                    title: 'Hapus Produk?',
                    html: 'Kamu akan menghapus <strong>' + name +
                        '</strong>.<br>Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    borderRadius: '.85rem',
                }).then(function(result) {
                    if (result.isConfirmed) form.submit();
                });
            });

            /* ── Drag & drop import ─────────────────────── */
            var dropZone = document.getElementById('dropZone');
            if (dropZone) {
                dropZone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    dropZone.style.borderColor = '#4e73df';
                    dropZone.style.background = '#f0f4ff';
                });
                dropZone.addEventListener('dragleave', function() {
                    dropZone.style.borderColor = '#d1d3e2';
                    dropZone.style.background = '#f8f9fc';
                });
                dropZone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    dropZone.style.borderColor = '#d1d3e2';
                    dropZone.style.background = '#f8f9fc';
                    var file = e.dataTransfer.files[0];
                    if (file) {
                        document.getElementById('importFile').files = e.dataTransfer.files;
                        handleImportFile(document.getElementById('importFile'));
                    }
                });
            }
        });

        /* ── Image preview ──────────────────────────── */
        function previewImage(inputId, previewId) {
            var input = document.getElementById(inputId);
            var preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        /* ── Import file handler ────────────────────── */
        function handleImportFile(input) {
            if (input.files && input.files[0]) {
                document.getElementById('fileName').textContent = input.files[0].name;
                document.getElementById('fileSelected').classList.remove('d-none');
            }
        }

        function clearImportFile() {
            document.getElementById('importFile').value = '';
            document.getElementById('fileSelected').classList.add('d-none');
        }
    </script>
@endpush
