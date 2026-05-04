@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        /* ══════════════════════════════════════════════
               FIX LAYOUT SHIFT — strip semua padding-right
               yang ditambah Bootstrap & SweetAlert ke <body>
            ══════════════════════════════════════════════ */
        html {
            overflow-y: scroll;
        }

        /* scrollbar selalu ada → tidak ada shift */
        body.modal-open {
            padding-right: 0 !important;
            overflow: hidden !important;
        }

        body.swal2-shown {
            padding-right: 0 !important;
        }

        .swal2-container {
            overflow-y: auto !important;
        }

        .modal-backdrop {
            padding-right: 0 !important;
        }

        /* ══════════════════════════════════════════════
               ROOT TOKENS
            ══════════════════════════════════════════════ */
        :root {
            --bp: #4e73df;
            --bs: #224abe;
            --ba: #36b9cc;
            --bsu: #1cc88a;
            --bw: #f6c23e;
            --bd: #e74a3b;
            --dark: #5a5c69;
            --shad: 0 4px 24px rgba(78, 115, 223, .12);
            --rad: .85rem;
            --tr: .2s ease;
        }

        /* ══════════════════════════════════════════════
               PAGE HEADER
            ══════════════════════════════════════════════ */
        .page-header-wrap {
            background: linear-gradient(135deg, #4e73df, #224abe);
            border-radius: var(--rad);
            padding: 1.4rem 1.8rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shad);
            position: relative;
            overflow: hidden;
        }

        .page-header-wrap::before,
        .page-header-wrap::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .page-header-wrap::before {
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, .07);
        }

        .page-header-wrap::after {
            bottom: -60px;
            right: 60px;
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, .05);
        }

        .header-inner {
            position: relative;
            z-index: 1;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        .page-subtitle {
            font-size: .8rem;
            color: rgba(255, 255, 255, .75);
            margin: .15rem 0 0;
        }

        .stat-chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 2rem;
            padding: .35rem .85rem;
            font-size: .78rem;
            color: #fff;
            font-weight: 600;
        }

        .stat-chip .chip-count {
            background: rgba(255, 255, 255, .25);
            border-radius: 2rem;
            padding: .05rem .55rem;
            font-weight: 800;
            min-width: 26px;
            text-align: center;
        }

        /* ══════════════════════════════════════════════
               BUTTONS
            ══════════════════════════════════════════════ */
        .btn-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .8rem;
            font-weight: 600;
            padding: .45rem 1rem;
            border-radius: .5rem;
            transition: all var(--tr);
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-pill i {
            font-size: .75rem;
        }

        .btn-primary-pill {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(78, 115, 223, .35);
        }

        .btn-primary-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(78, 115, 223, .45);
            color: #fff !important;
        }

        .btn-primary-pill:disabled {
            opacity: .55;
            transform: none;
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-warn-pill {
            background: #fff;
            color: var(--bw) !important;
            border: 1.5px solid var(--bw) !important;
        }

        .btn-warn-pill:hover {
            background: var(--bw);
            color: #fff !important;
            transform: translateY(-2px);
        }

        .btn-succ-pill {
            background: #fff;
            color: var(--bsu) !important;
            border: 1.5px solid var(--bsu) !important;
        }

        .btn-succ-pill:hover {
            background: var(--bsu);
            color: #fff !important;
            transform: translateY(-2px);
        }

        .btn-info-pill {
            background: #fff;
            color: var(--bp) !important;
            border: 1.5px solid var(--bp) !important;
        }

        .btn-info-pill:hover {
            background: var(--bp);
            color: #fff !important;
            transform: translateY(-2px);
        }

        .btn-danger-pill {
            background: #fff;
            color: var(--bd) !important;
            border: 1.5px solid var(--bd) !important;
        }

        .btn-danger-pill:hover {
            background: var(--bd);
            color: #fff !important;
            transform: translateY(-2px);
        }

        .btn-cancel-pill {
            background: #fff;
            color: var(--dark) !important;
            border: 1.5px solid #d1d3e2 !important;
        }

        .btn-cancel-pill:hover {
            background: #f0f0f5;
        }

        /* ══════════════════════════════════════════════
               TOOLBAR
            ══════════════════════════════════════════════ */
        .action-toolbar {
            background: #fff;
            border-radius: var(--rad);
            padding: 1rem 1.25rem;
            box-shadow: var(--shad);
            margin-bottom: 1.25rem;
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
            align-items: center;
        }

        .toolbar-label {
            font-size: .72rem;
            font-weight: 700;
            color: var(--dark);
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-right: auto;
        }

        /* ══════════════════════════════════════════════
               INLINE PANEL (replaces semua modal)
            ══════════════════════════════════════════════ */
        .product-panel {
            border: none;
            border-radius: var(--rad);
            box-shadow: var(--shad);
            overflow: hidden;
            margin-bottom: 1.25rem;
            transition: all .3s ease;
        }

        .panel-header {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            border-bottom: 2px solid rgba(255, 255, 255, .15);
        }

        .panel-header-create {
            background: linear-gradient(135deg, #4e73df, #224abe);
        }

        .panel-header-edit {
            background: linear-gradient(135deg, #f6c23e, #d4a017);
        }

        .panel-header-show {
            background: linear-gradient(135deg, #36b9cc, #258391);
        }

        .panel-header-icon {
            width: 38px;
            height: 38px;
            border-radius: .5rem;
            background: rgba(255, 255, 255, .18);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .panel-header-icon i {
            color: #fff;
            font-size: .9rem;
        }

        .panel-header-title {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
        }

        .panel-header-sub {
            font-size: .73rem;
            color: rgba(255, 255, 255, .75);
            margin: .1rem 0 0;
        }

        .panel-body {
            background: #fff;
            padding: 1.5rem;
        }

        /* Form section labels */
        .fs-label {
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #858796;
            padding: .4rem 0;
            border-bottom: 1.5px solid #e3e6f0;
            margin-bottom: .9rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .fs-label i {
            color: #4e73df;
        }

        .fs-label.edit-accent i {
            color: #d4a017;
        }

        .fs-label.show-accent i {
            color: #36b9cc;
        }

        /* Form controls */
        .fc-label {
            font-size: .78rem;
            font-weight: 700;
            color: #5a5c69;
            margin-bottom: .35rem;
            display: block;
        }

        .fc-input {
            border: 1.5px solid #d1d3e2;
            border-radius: .5rem;
            padding: .5rem .85rem;
            font-size: .84rem;
            color: #5a5c69;
            transition: border-color .2s, box-shadow .2s;
            background: #fff;
            width: 100%;
        }

        .fc-input:focus {
            outline: none;
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, .12);
        }

        .fc-input::placeholder {
            color: #b7bac7;
        }

        .fc-input:disabled {
            background: #f8f9fc;
            cursor: not-allowed;
        }

        .fc-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23858796'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            padding-right: 2rem !important;
        }

        .fc-select:disabled {
            background-color: #f8f9fc !important;
            cursor: not-allowed;
        }

        .fc-prefix {
            background: #eaecf4;
            border: 1.5px solid #d1d3e2;
            border-right: none;
            border-radius: .5rem 0 0 .5rem;
            padding: .5rem .85rem;
            font-size: .8rem;
            color: #858796;
            font-weight: 700;
        }

        /* Upload zone */
        .img-upload-zone {
            border: 2px dashed #d1d3e2;
            border-radius: .65rem;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            background: #f8f9fc;
        }

        .img-upload-zone:hover {
            border-color: #4e73df;
            background: #f0f4ff;
        }

        .img-upload-zone .upload-icon {
            font-size: 1.4rem;
            color: #b7bac7;
            transition: color .2s;
        }

        .img-upload-zone:hover .upload-icon {
            color: #4e73df;
        }

        .img-preview-box {
            margin-top: .75rem;
            position: relative;
            display: inline-block;
        }

        .img-preview-box img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: .6rem;
            border: 2.5px solid #4e73df;
            box-shadow: 0 4px 12px rgba(78, 115, 223, .2);
        }

        .img-remove-btn {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            background: #e74a3b;
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: .55rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Show detail rows */
        .detail-row {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: .55rem 0;
        }

        .detail-row.top {
            align-items: flex-start;
        }

        .detail-icon {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border-radius: .45rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
        }

        .detail-label {
            font-size: .67rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #b7bac7;
            margin-bottom: .1rem;
        }

        .detail-value {
            font-size: .84rem;
            color: #2d3748;
            font-weight: 500;
        }

        .detail-divider {
            height: 1px;
            background: linear-gradient(90deg, #e3e6f0, transparent);
            margin: .05rem 0;
        }

        .cat-pill {
            font-size: .7rem;
            font-weight: 700;
            padding: .2em .65em;
            border-radius: 2rem;
            display: inline-block;
        }

        .cat-parent {
            background: rgba(78, 115, 223, .1);
            color: #4e73df;
            border: 1px solid rgba(78, 115, 223, .2);
        }

        .cat-child {
            background: rgba(54, 185, 204, .1);
            color: #258391;
            border: 1px solid rgba(54, 185, 204, .2);
        }

        /* ══════════════════════════════════════════════
               TABLE CARD
            ══════════════════════════════════════════════ */
        .product-card {
            border: none;
            border-radius: var(--rad);
            box-shadow: var(--shad);
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #f8f9fc, #eaecf4);
            border-bottom: 2px solid #e3e6f0;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .card-header-custom .hdr-icon {
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

        .card-header-custom .hdr-title {
            font-size: .9rem;
            font-weight: 700;
            color: var(--dark);
        }

        .record-count {
            margin-left: auto;
            font-size: .72rem;
            color: #858796;
            font-weight: 500;
        }

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
            transition: background var(--tr);
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

        .tbl-img {
            width: 46px;
            height: 46px;
            border-radius: .45rem;
            object-fit: cover;
            border: 2px solid #e3e6f0;
            transition: transform .2s;
        }

        .tbl-img:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
        }

        .badge-status {
            font-size: .7rem;
            font-weight: 700;
            padding: .33em .75em;
            border-radius: 2rem;
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

        .stock-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .74rem;
            font-weight: 700;
            padding: .24em .68em;
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

        .tbl-actions {
            display: flex;
            gap: .3rem;
            justify-content: center;
        }

        .tbl-btn {
            width: 28px;
            height: 28px;
            border-radius: .4rem;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .68rem;
            transition: all var(--tr);
            cursor: pointer;
        }

        .tbl-btn-view {
            background: rgba(54, 185, 204, .12);
            color: var(--ba);
        }

        .tbl-btn-view:hover {
            background: var(--ba);
            color: #fff;
            transform: translateY(-2px);
        }

        .tbl-btn-edit {
            background: rgba(246, 194, 62, .12);
            color: #d4a017;
        }

        .tbl-btn-edit:hover {
            background: var(--bw);
            color: #fff;
            transform: translateY(-2px);
        }

        .tbl-btn-delete {
            background: rgba(231, 74, 59, .1);
            color: var(--bd);
        }

        .tbl-btn-delete:hover {
            background: var(--bd);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Alerts */
        .alert-modern {
            border: none;
            border-radius: var(--rad);
            box-shadow: var(--shad);
        }

        .alert-modern.alert-success {
            background: linear-gradient(135deg, #f0fff8, #f7fffc);
            border-left: 4px solid var(--bsu);
        }

        .alert-modern.alert-warning {
            background: linear-gradient(135deg, #fff9e6, #fffdf0);
            border-left: 4px solid var(--bw);
        }

        /* DataTable UI */
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #e3e6f0;
            border-radius: .45rem;
            padding: .35rem .75rem;
            font-size: .82rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: var(--bp);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, .1);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid #e3e6f0;
            border-radius: .45rem;
            padding: .3rem .5rem;
            font-size: .82rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--bp) !important;
            border-color: var(--bp) !important;
            color: #fff !important;
            border-radius: .4rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #eaecf4 !important;
            border-color: #eaecf4 !important;
            color: var(--bp) !important;
            border-radius: .4rem;
        }

        .dataTables_wrapper .dataTables_info {
            font-size: .78rem;
            color: #858796;
        }

        .dataTables_processing {
            background: rgba(255, 255, 255, .92) !important;
            border: none !important;
            box-shadow: var(--shad) !important;
            border-radius: .5rem !important;
            font-size: .82rem !important;
            color: var(--bp) !important;
            font-weight: 600 !important;
        }

        @media(max-width:768px) {
            .page-header-wrap {
                padding: 1rem 1.2rem;
            }

            .action-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-pill {
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')

    {{-- ══════════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════════ --}}
    <div class="page-header-wrap animate__animated animate__fadeInDown animate__faster">
        <div class="header-inner d-flex flex-wrap align-items-center">
            <div class="mr-auto">
                <h4 class="page-title"><i class="fas fa-box-open mr-2" style="opacity:.85"></i> Manajemen Produk</h4>
                <p class="page-subtitle">Kelola data produk petshop kamu dengan mudah</p>
            </div>
            <div class="d-flex align-items-center mt-2 mt-md-0" style="gap:.6rem;flex-wrap:wrap">
                <span class="stat-chip">
                    <i class="fas fa-box"></i> Total: <span class="chip-count" id="chip-total">...</span>
                </span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
     FLASH — success
     FIX #1: <strong> literal di SweetAlert.
     Solusi: controller TIDAK pakai <strong> lagi.
     Tampilan bold ditangani di sini / di swal html:
══════════════════════════════════════════ --}}
    @if (session('success'))
        <div class="alert alert-modern alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2" style="color:var(--bsu)"></i>
            {!! session('success') !!}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if (session('import_failures'))
        <div class="alert alert-modern alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Import selesai dengan {{ session('import_total_failed') }} baris gagal:</strong>
            </div>
            <ul class="mb-0 pl-3" style="max-height:180px;overflow-y:auto">
                @foreach (session('import_failures') as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════════
     ACTION TOOLBAR
══════════════════════════════════════════ --}}
    <div class="action-toolbar">
        <span class="toolbar-label"><i class="fas fa-tools mr-1"></i> Aksi Data</span>
        <a href="{{ route('dashboard.products.downloadImportTemplate') }}" class="btn-pill btn-warn-pill">
            <i class="fas fa-file-download"></i> Template
        </a>
        <button type="button" class="btn-pill btn-succ-pill" data-toggle="modal" data-target="#modalImport">
            <i class="fas fa-upload"></i> Import
        </button>
        <a href="{{ route('dashboard.products.export') }}" class="btn-pill btn-info-pill">
            <i class="fas fa-file-export"></i> Export
        </a>
    </div>

    {{-- ══════════════════════════════════════════
     INLINE PANEL — Create / Edit / Show
     Satu panel, 3 mode. Mode default = CREATE.
══════════════════════════════════════════ --}}
    @canany(['product.create', 'product.edit', 'product.show'])
        <div class="product-panel animate__animated animate__fadeInUp animate__faster" id="productPanel">

            {{-- Panel Header (warna berganti via JS) --}}
            <div class="panel-header panel-header-create" id="panelHeader">
                <div class="panel-header-icon" id="panelIcon"><i class="fas fa-plus" id="panelIconFa"></i></div>
                <div>
                    <div class="panel-header-title" id="panelTitle">Tambah Produk Baru</div>
                    <div class="panel-header-sub" id="panelSub">Lengkapi semua field bertanda * untuk mengaktifkan tombol
                        simpan</div>
                </div>
                {{-- Tombol collapse --}}
                <button type="button" class="ml-auto btn-pill btn-cancel-pill btn-sm" id="btnCollapsePanel"
                    style="padding:.3rem .75rem;font-size:.72rem">
                    <i class="fas fa-chevron-up" id="collapseIcon"></i>
                </button>
            </div>

            {{-- Panel Body --}}
            <div id="panelBody">

                {{-- ── MODE: SHOW ──────────────────────────────── --}}
                <div id="showView" class="d-none panel-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img id="showImg" src="" alt="foto" class="img-thumbnail shadow-sm"
                                style="width:130px;height:130px;object-fit:cover;border-radius:.65rem;border:2.5px solid #36b9cc">
                            <div class="mt-2">
                                <span class="badge-status" id="showStatus"></span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="fs-label show-accent"><i class="fas fa-info-circle"></i> Detail Produk</div>
                            <div class="detail-row">
                                <div class="detail-icon" style="background:rgba(78,115,223,.1);color:#4e73df"><i
                                        class="fas fa-tag"></i></div>
                                <div>
                                    <div class="detail-label">Nama</div>
                                    <div class="detail-value font-weight-bold" id="showName" style="font-size:.95rem"></div>
                                </div>
                            </div>
                            <div class="detail-divider"></div>
                            <div class="detail-row">
                                <div class="detail-icon" style="background:rgba(28,200,138,.1);color:#1cc88a"><i
                                        class="fas fa-money-bill-wave"></i></div>
                                <div>
                                    <div class="detail-label">Harga</div>
                                    <div class="detail-value font-weight-700" id="showPrice"
                                        style="color:#1cc88a;font-size:.95rem"></div>
                                </div>
                            </div>
                            <div class="detail-divider"></div>
                            <div class="detail-row">
                                <div class="detail-icon" style="background:rgba(246,194,62,.15);color:#d4a017"><i
                                        class="fas fa-boxes"></i></div>
                                <div>
                                    <div class="detail-label">Stok</div>
                                    <div class="detail-value"><span class="stock-pill" id="showStock"></span></div>
                                </div>
                            </div>
                            <div class="detail-divider"></div>
                            <div class="detail-row">
                                <div class="detail-icon" style="background:rgba(78,115,223,.1);color:#4e73df"><i
                                        class="fas fa-sitemap"></i></div>
                                <div>
                                    <div class="detail-label">Kategori</div>
                                    <div class="detail-value">
                                        <span class="cat-pill cat-parent" id="showSpecies"></span>
                                        <i class="fas fa-chevron-right mx-1" style="font-size:.6rem;color:#b7bac7"></i>
                                        <span class="cat-pill cat-child" id="showCategory"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="detail-divider"></div>
                            <div class="detail-row top">
                                <div class="detail-icon mt-1" style="background:rgba(90,92,105,.1);color:#5a5c69"><i
                                        class="fas fa-align-left"></i></div>
                                <div>
                                    <div class="detail-label">Deskripsi</div>
                                    <div class="detail-value small" id="showDetail" style="line-height:1.6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3 pt-3" style="border-top:1px solid #e3e6f0;gap:.5rem">
                        <button type="button" class="btn-pill btn-cancel-pill" id="btnShowCancel">
                            <i class="fas fa-times mr-1"></i> Tutup
                        </button>
                        @can('product.edit')
                            <button type="button" class="btn-pill" id="btnShowToEdit"
                                style="background:linear-gradient(135deg,#f6c23e,#d4a017);color:#fff;box-shadow:0 4px 12px rgba(246,194,62,.35)">
                                <i class="fas fa-edit"></i> Edit Produk Ini
                            </button>
                        @endcan
                    </div>
                </div>

                {{-- ── MODE: CREATE / EDIT FORM ─────────────────── --}}
                <form id="productForm" action="{{ route('dashboard.products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <span id="methodField"></span>{{-- JS inject @method('PUT') saat edit --}}
                    <input type="hidden" id="productId" name="_product_id" value="">

                    <div class="panel-body">
                        {{-- Row 1: Nama + Status --}}
                        <div class="fs-label"><i class="fas fa-info-circle"></i> Informasi Dasar</div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="fc-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="fieldName" class="fc-input req-field"
                                    placeholder="Contoh: Whiskas Tuna 1kg">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fc-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="fieldStatus" class="fc-input fc-select req-field">
                                    <option value="active">✅ Active</option>
                                    <option value="inactive">❌ Inactive</option>
                                </select>
                            </div>
                        </div>

                        {{-- Row 2: Harga + Stok --}}
                        <div class="fs-label mt-1"><i class="fas fa-tags"></i> Harga & Stok</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fc-label">Harga (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="fc-prefix">Rp</span></div>
                                    <input type="text" name="price" id="fieldPrice"
                                        class="fc-input input-rupiah req-field" placeholder="50.000"
                                        style="border-radius:0 .5rem .5rem 0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fc-label">Stok (Pcs) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="fc-prefix"><i
                                                class="fas fa-boxes"></i></span></div>
                                    <input type="number" name="stock" id="fieldStock" class="fc-input req-field"
                                        placeholder="10" min="0" style="border-radius:0 .5rem .5rem 0">
                                </div>
                            </div>
                        </div>

                        {{-- Row 3: Species + Kategori --}}
                        <div class="fs-label mt-1"><i class="fas fa-sitemap"></i> Kategori</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fc-label">Species (Induk) <span class="text-danger">*</span></label>
                                <select id="fieldSpecies" class="fc-input fc-select req-field">
                                    <option value="">-- Pilih Species --</option>
                                    @foreach ($categories as $cat)
                                        @if (empty($cat->parent_id))
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fc-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" id="fieldCategory" class="fc-input fc-select req-field" disabled>
                                    <option value="">-- Pilih Species Dulu --</option>
                                </select>
                                <span class="small text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle mr-1"></i>Pilih species untuk memuat kategori
                                </span>
                            </div>
                        </div>

                        {{-- Row 4: Foto + Detail (opsional) --}}
                        <div class="fs-label mt-1"><i class="fas fa-image"></i> Foto & Deskripsi <small
                                class="text-muted font-weight-normal ml-1">(opsional)</small></div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="fc-label">Foto Produk</label>
                                <div class="img-upload-zone" id="uploadZone"
                                    onclick="document.getElementById('fieldImage').click()">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <p class="mb-0 mt-1 small" style="color:#5a5c69">Klik untuk pilih foto</p>
                                    <p class="mb-0 small text-muted">JPG, PNG — maks. 2MB</p>
                                    <input type="file" id="fieldImage" name="image" class="d-none" accept="image/*"
                                        onchange="handleImageUpload(this)">
                                </div>
                                <div class="img-preview-box d-none mt-2" id="imgPreviewBox">
                                    <img id="imgPreview" src="" alt="preview">
                                    <button type="button" class="img-remove-btn" onclick="removeProductImage()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                {{-- Edit mode: tampilkan foto saat ini --}}
                                <div class="d-none mt-2" id="currentImgWrap">
                                    <div class="small text-muted mb-1"><i class="fas fa-image mr-1"></i>Foto saat ini:</div>
                                    <img id="currentImg" src="" alt="current"
                                        style="width:70px;height:70px;object-fit:cover;border-radius:.45rem;border:2px solid #e3e6f0">
                                </div>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="fc-label">Detail / Deskripsi</label>
                                <textarea name="detail" id="fieldDetail" class="fc-input" rows="5"
                                    placeholder="Tulis deskripsi lengkap produk..."></textarea>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-3"
                            style="border-top:1px solid #e3e6f0">
                            <span class="small text-muted"> 
                                <i class="fas fa-asterisk text-danger mr-1" style="font-size:.55rem"></i>
                                Field bertanda * wajib diisi sebelum tombol simpan aktif
                            </span>
                            <div style="display:flex;gap:.5rem">
                                <button type="button" class="btn-pill btn-cancel-pill" id="btnFormCancel">
                                    <i class="fas fa-times mr-1"></i> Reset
                                </button>
                                @canany(['product.create', 'product.edit'])
                                    <button type="submit" class="btn-pill btn-primary-pill" id="btnSubmit" disabled>
                                        <i class="fas fa-save" id="submitIcon"></i>
                                        <span id="submitLabel">Simpan Produk</span>
                                    </button>
                                @endcanany
                            </div>
                        </div>

                    </div>{{-- /panel-body --}}
                </form>

            </div>{{-- /panelBody --}}
        </div>{{-- /productPanel --}}
    @endcanany

    {{-- ══════════════════════════════════════════
     DATATABLE CARD
══════════════════════════════════════════ --}}
    <div class="card product-card animate__animated animate__fadeInUp animate__faster">
        <div class="card-header-custom">
            <div class="hdr-icon"><i class="fas fa-th-list"></i></div>
            <span class="hdr-title">Daftar Produk</span>
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
                            <th width="110px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
     MODAL IMPORT (tetap pakai modal karena
     hanya upload file, tidak perlu inline)
══════════════════════════════════════════ --}}
    <div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content"
                style="border:none;border-radius:var(--rad);overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.18)">
                <div class="modal-header" style="background:linear-gradient(135deg,#1cc88a,#17a673);border:none">
                    <div class="d-flex align-items-center">
                        <div
                            style="width:34px;height:34px;background:rgba(255,255,255,.2);border-radius:.45rem;display:flex;align-items:center;justify-content:center;margin-right:.6rem">
                            <i class="fas fa-upload text-white"></i>
                        </div>
                        <h5 class="modal-title text-white font-weight-bold mb-0">Import Data Produk</h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal"
                        style="opacity:.8"><span>&times;</span></button>
                </div>
                <form action="{{ route('dashboard.products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div id="dropZone" onclick="document.getElementById('importFile').click()"
                            style="border:2.5px dashed #d1d3e2;border-radius:.75rem;padding:2rem 1.5rem;text-align:center;cursor:pointer;transition:all .2s;background:#f8f9fc">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:#b7bac7"></i>
                            <p class="mb-1 font-weight-bold" style="color:#5a5c69;font-size:.88rem">Klik atau drag &amp;
                                drop file</p>
                            <p class="mb-0 small text-muted">Format: .xlsx, .xls, .csv</p>
                            <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" required
                                class="d-none" onchange="handleImportFile(this)">
                        </div>
                        <div id="fileSelected" class="mt-3 d-none">
                            <div class="d-flex align-items-center p-2 rounded"
                                style="background:#f0fff8;border:1px solid #c3f0dc">
                                <i class="fas fa-file-excel mr-2" style="color:#1cc88a"></i>
                                <span id="fileName" class="small font-weight-bold" style="color:#169a6b"></span>
                                <button type="button" class="ml-auto btn btn-sm btn-link text-danger p-0"
                                    onclick="clearImportFile()"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="mt-3 p-3 rounded" style="background:#fff9e6;border-left:3px solid #f6c23e">
                            <p class="mb-1 small font-weight-bold" style="color:#856404"><i
                                    class="fas fa-lightbulb mr-1"></i> Tips:</p>
                            <ul class="mb-0 pl-3 small text-muted">
                                <li>Gunakan template yang disediakan</li>
                                <li>Baris valid tetap diimport meski ada error</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #f0f0f5;background:#fafbff">
                        <button type="button" class="btn-pill btn-cancel-pill" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-pill btn-succ-pill"><i class="fas fa-upload mr-1"></i> Mulai
                            Import</button>
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
        /* ══════════════════════════════════════════════════════
       FIX LAYOUT SHIFT — patch Bootstrap _adjustDialog
       Harus dijalankan SEBELUM document.ready agar
       intercepted sebelum modal pertama kali terbuka.
    ══════════════════════════════════════════════════════ */
        (function patchBootstrapModal() {
            var interval = setInterval(function() {
                if (window.jQuery && $.fn.modal && $.fn.modal.Constructor) {
                    $.fn.modal.Constructor.prototype._adjustDialog = function() {};
                    $.fn.modal.Constructor.prototype._resetAdjustments = function() {};
                    clearInterval(interval);
                }
            }, 50);
        }());

        $(document).ready(function() {

            /* ─────────────────────────────────────────────────────
               PANEL STATE
            ───────────────────────────────────────────────────── */
            var panelMode = 'create'; // 'create' | 'edit' | 'show'
            var editingProduct = null; // object dari DataTable row
            var panelCollapsed = false;

            // Elemen cache
            var $panel = $('#productPanel');
            var $panelHeader = $('#panelHeader');
            var $panelBody = $('#panelBody');
            var $showView = $('#showView');
            var $productForm = $('#productForm');
            var $panelTitle = $('#panelTitle');
            var $panelSub = $('#panelSub');
            var $panelIconFa = $('#panelIconFa');
            var $btnSubmit = $('#btnSubmit');
            var $submitLabel = $('#submitLabel');
            var $submitIcon = $('#submitIcon');
            var $methodField = $('#methodField');
            var $productId = $('#productId');

            /* ─────────────────────────────────────────────────────
               PANEL MODE FUNCTIONS
            ───────────────────────────────────────────────────── */
            function switchMode(mode, data) {
                panelMode = mode;

                // Expand jika sedang collapse
                if (panelCollapsed) toggleCollapse();

                // Sembunyikan semua dulu
                $showView.addClass('d-none');
                $productForm.closest('.panel-body').parent().show();

                // Hapus semua class header lalu tambah yang sesuai
                $panelHeader.removeClass('panel-header-create panel-header-edit panel-header-show');

                if (mode === 'show') {
                    $panelHeader.addClass('panel-header-show');
                    $panelIconFa.attr('class', 'fas fa-eye');
                    $panelTitle.text('Detail Produk');
                    $panelSub.text(data ? data.name : '');
                    $productForm.addClass('d-none');
                    $showView.removeClass('d-none');
                    populateShow(data);

                } else if (mode === 'edit') {
                    $panelHeader.addClass('panel-header-edit');
                    $panelIconFa.attr('class', 'fas fa-edit');
                    $panelTitle.text('Edit Produk');
                    $panelSub.text(data ? data.name : '');
                    $productForm.removeClass('d-none');
                    editingProduct = data;
                    populateForm(data);
                    $submitLabel.text('Update Produk');
                    $submitIcon.attr('class', 'fas fa-save');
                    // Inject method PUT
                    $methodField.html('<input type="hidden" name="_method" value="PUT">');
                    // Ganti action URL ke update route
                    $productForm.attr('action', '/dashboard/products/' + data.id);
                    $productId.val(data.id);

                } else { // create
                    $panelHeader.addClass('panel-header-create');
                    $panelIconFa.attr('class', 'fas fa-plus');
                    $panelTitle.text('Tambah Produk Baru');
                    $panelSub.text('Lengkapi semua field bertanda * untuk mengaktifkan tombol simpan');
                    $productForm.removeClass('d-none');
                    resetForm();
                    editingProduct = null;
                    $methodField.html('');
                    $productForm.attr('action', '{{ route('dashboard.products.store') }}');
                    $productId.val('');
                    $submitLabel.text('Simpan Produk');
                    $submitIcon.attr('class', 'fas fa-save');
                }

                // Scroll ke panel
                $('html, body').animate({
                    scrollTop: $panel.offset().top - 80
                }, 300);
            }

            /* ─────────────────────────────────────────────────────
               POPULATE SHOW VIEW
            ───────────────────────────────────────────────────── */
            function populateShow(d) {
                // Foto
                var imgSrc = d.image_url || '/storage/uploads/products/default-product.jpg';
                $('#showImg').attr('src', imgSrc);

                // Status badge
                var iA = d.status_raw === 'active';
                $('#showStatus')
                    .attr('class', 'badge-status ' + (iA ? 'badge-active' : 'badge-inactive'))
                    .text(iA ? '✅ Active' : '❌ Inactive');

                // Fields
                $('#showName').text(d.name_raw || d.name || '—');
                $('#showPrice').text('Rp ' + formatRupiah(String(d.price_raw || 0)));
                $('#showSpecies').text(d.species_raw || '—');
                $('#showCategory').text(d.category_raw || '—');
                $('#showDetail').text(d.detail || '—');

                // Stok pill
                var stk = parseInt(d.stock_raw) || 0;
                var sClass = stk === 0 ? 'stock-zero' : (stk <= 5 ? 'stock-low' : 'stock-ok');
                var sIcon = stk === 0 ? 'fa-times-circle' : (stk <= 5 ? 'fa-exclamation-circle' :
                'fa-check-circle');
                $('#showStock')
                    .attr('class', 'stock-pill ' + sClass)
                    .html('<i class="fas ' + sIcon + '"></i> ' + stk + ' Pcs');
            }

            /* ─────────────────────────────────────────────────────
               POPULATE FORM (edit mode)
            ───────────────────────────────────────────────────── */
            function populateForm(d) {
                $('#fieldName').val(d.name_raw || '');
                $('#fieldStatus').val(d.status_raw || 'active');
                $('#fieldDetail').val(d.detail || '');
                // Harga — format rupiah
                var priceRaw = String(d.price_raw || 0);
                $('#fieldPrice').val(formatRupiah(priceRaw));
                $('#fieldStock').val(d.stock_raw || 0);

                // Foto existing
                if (d.image_url) {
                    $('#currentImg').attr('src', d.image_url);
                    $('#currentImgWrap').removeClass('d-none');
                } else {
                    $('#currentImgWrap').addClass('d-none');
                }

                // Species & Category via AJAX
                var speciesId = d.species_id;
                var categoryId = d.category_id;

                if (speciesId) {
                    $('#fieldSpecies').val(speciesId);
                    fetchSubCategories(speciesId, $('#fieldCategory'), categoryId);
                }

                checkFormValidity();
            }

            /* ─────────────────────────────────────────────────────
               RESET FORM
            ───────────────────────────────────────────────────── */
            function resetForm() {
                document.getElementById('productForm').reset();
                $('#fieldCategory').prop('disabled', true).empty().append(
                    '<option value="">-- Pilih Species Dulu --</option>');
                $('#imgPreviewBox').addClass('d-none');
                $('#currentImgWrap').addClass('d-none');
                removeProductImage();
                checkFormValidity();
                // Reset upload zone warna
                var zone = document.getElementById('uploadZone');
                if (zone) {
                    zone.style.borderColor = '';
                    zone.style.background = '';
                }
            }

            /* ─────────────────────────────────────────────────────
               COLLAPSE PANEL
            ───────────────────────────────────────────────────── */
            function toggleCollapse() {
                panelCollapsed = !panelCollapsed;
                if (panelCollapsed) {
                    $panelBody.slideUp(250);
                    $('#collapseIcon').attr('class', 'fas fa-chevron-down');
                } else {
                    $panelBody.slideDown(250);
                    $('#collapseIcon').attr('class', 'fas fa-chevron-up');
                }
            }
            $('#btnCollapsePanel').on('click', toggleCollapse);

            /* ─────────────────────────────────────────────────────
               RESET ke CREATE mode
            ───────────────────────────────────────────────────── */
            $('#btnFormCancel, #btnShowCancel').on('click', function() {
                switchMode('create');
            });

            /* ─────────────────────────────────────────────────────
               SHOW → EDIT shortcut button
            ───────────────────────────────────────────────────── */
            $('#btnShowToEdit').on('click', function() {
                if (editingProduct) switchMode('edit', editingProduct);
            });

            /* ─────────────────────────────────────────────────────
               FORM VALIDITY — disable/enable submit
            ───────────────────────────────────────────────────── */
            function checkFormValidity() {
                var valid = true;
                $('#productForm .req-field').each(function() {
                    if (!$(this).val() || $(this).val() === '') {
                        valid = false;
                        return false;
                    }
                });
                $btnSubmit.prop('disabled', !valid);
            }

            $(document).on('input change', '#productForm .req-field', checkFormValidity);

            /* ─────────────────────────────────────────────────────
               FORM SUBMIT — strip dots dari rupiah
            ───────────────────────────────────────────────────── */
            $('#productForm').on('submit', function(e) {
                // Konfirmasi edit
                if (panelMode === 'edit') {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Simpan Perubahan?',
                        text: 'Pastikan semua data sudah benar.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#f6c23e',
                        cancelButtonColor: '#858796',
                        confirmButtonText: '<i class="fas fa-save mr-1"></i> Ya, Update!',
                        cancelButtonText: 'Cek Lagi',
                    }).then(function(r) {
                        if (r.isConfirmed) {
                            // strip dots
                            $('#fieldPrice').val($('#fieldPrice').val().replace(/\./g, ''));
                            document.getElementById('productForm').submit();
                        }
                    });
                    return;
                }
                // Strip dots untuk create
                $('#fieldPrice').val($('#fieldPrice').val().replace(/\./g, ''));
            });

            /* ─────────────────────────────────────────────────────
               SUB-CATEGORY FETCH
            ───────────────────────────────────────────────────── */
            function fetchSubCategories(parentId, $target, preselectId) {
                if (!parentId) {
                    $target.prop('disabled', true).empty().append(
                        '<option value="">-- Pilih Species Dulu --</option>');
                    checkFormValidity();
                    return;
                }
                $target.prop('disabled', true).empty().append('<option value="">Memuat...</option>');
                $.ajax({
                    url: '{{ url('dashboard/get-subcategories') }}/' + parentId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $target.empty().append('<option value="">-- Pilih Kategori --</option>');
                        $.each(data, function(k, v) {
                            $target.append('<option value="' + v.id + '">' + v.name +
                                '</option>');
                        });
                        $target.prop('disabled', data.length === 0);
                        if (preselectId) $target.val(preselectId);
                        checkFormValidity();
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memuat kategori.', 'error');
                        $target.prop('disabled', false).empty().append(
                            '<option value="">-- Error --</option>');
                    }
                });
            }

            $('#fieldSpecies').on('change', function() {
                fetchSubCategories($(this).val(), $('#fieldCategory'), null);
            });

            /* ─────────────────────────────────────────────────────
               RUPIAH FORMAT
            ───────────────────────────────────────────────────── */
            $(document).on('keyup', '#fieldPrice', function() {
                $(this).val(formatRupiah($(this).val()));
                checkFormValidity();
            });

            /* ─────────────────────────────────────────────────────
               DELETE CONFIRM
            ───────────────────────────────────────────────────── */
            $(document).on('click', '.btn-delete-product', function() {
                var form = $(this).closest('form');
                var name = $(this).data('name') || 'produk ini';
                Swal.fire({
                    title: 'Hapus Produk?',
                    html: 'Kamu akan menghapus <b>' + name +
                        '</b>.<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                }).then(function(r) {
                    if (r.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            didOpen: function() {
                                Swal.showLoading();
                            }
                        });
                        form[0].submit();
                    }
                });
            });

            /* ─────────────────────────────────────────────────────
               DATATABLE
            ───────────────────────────────────────────────────── */
            var table = $('#data-products').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                language: {
                    processing: '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat...',
                    search: '',
                    searchPlaceholder: 'Cari produk...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ produk',
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    },
                    emptyTable: '<div class="text-center py-4"><i class="fas fa-box-open fa-2x text-muted"></i><br>Tidak ada produk</div>',
                },
                ajax: {
                    url: "{{ route('dashboard.products.index') }}",
                    error: function(xhr) {
                        console.error('DT Error:', xhr.responseText);
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
                drawCallback: function() {
                    var total = this.api().page.info().recordsTotal;
                    $('#chip-total').text(total);
                    $('#record-count-label').text(total + ' produk terdaftar');
                }
            });

            /* ─────────────────────────────────────────────────────
               ACTION BUTTONS dari DataTable
               Tombol View/Edit inject data- attributes di controller
            ───────────────────────────────────────────────────── */
            $(document).on('click', '.btn-view-product', function() {
                var data = $(this).data('product');
                if (data) switchMode('show', data);
            });

            $(document).on('click', '.btn-edit-product', function() {
                var data = $(this).data('product');
                if (data) switchMode('edit', data);
            });

            /* ─────────────────────────────────────────────────────
               IMPORT drag & drop
            ───────────────────────────────────────────────────── */
            var dz = document.getElementById('dropZone');
            if (dz) {
                dz.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#4e73df';
                    this.style.background = '#f0f4ff';
                });
                dz.addEventListener('dragleave', function() {
                    this.style.borderColor = '';
                    this.style.background = '';
                });
                dz.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '';
                    this.style.background = '';
                    try {
                        document.getElementById('importFile').files = e.dataTransfer.files;
                    } catch (err) {}
                    handleImportFile({
                        files: e.dataTransfer.files
                    });
                });
            }

        }); // end document.ready

        /* ══════════════════════════════════════════════════════
           GLOBAL HELPERS
        ══════════════════════════════════════════════════════ */
        function formatRupiah(angka) {
            var str = String(angka).replace(/[^,\d]/g, '');
            var split = str.split(',');
            var sisa = split[0].length % 3;
            var rupiah = split[0].substr(0, sisa);
            var ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
            return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        }

        function handleImageUpload(input) {
            if (!input.files || !input.files[0]) return;
            var file = input.files[0];
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('File Terlalu Besar', 'Maksimal 2MB.', 'warning');
                input.value = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imgPreview').attr('src', e.target.result);
                $('#imgPreviewBox').removeClass('d-none');
                var zone = document.getElementById('uploadZone');
                if (zone) {
                    zone.style.borderColor = '#1cc88a';
                    zone.style.background = '#f0fff8';
                }
            };
            reader.readAsDataURL(file);
        }

        function removeProductImage() {
            document.getElementById('fieldImage').value = '';
            $('#imgPreviewBox').addClass('d-none');
            var zone = document.getElementById('uploadZone');
            if (zone) {
                zone.style.borderColor = '';
                zone.style.background = '';
            }
        }

        function handleImportFile(input) {
            if (!input.files || !input.files[0]) return;
            var ext = input.files[0].name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls', 'csv'].includes(ext)) {
                Swal.fire('Format Tidak Valid', 'Hanya .xlsx, .xls, .csv.', 'warning');
                return;
            }
            document.getElementById('fileName').textContent = input.files[0].name;
            document.getElementById('fileSelected').classList.remove('d-none');
            var zone = document.getElementById('dropZone');
            if (zone) {
                zone.style.borderColor = '#1cc88a';
                zone.style.background = '#f0fff8';
            }
        }

        function clearImportFile() {
            document.getElementById('importFile').value = '';
            document.getElementById('fileSelected').classList.add('d-none');
            var zone = document.getElementById('dropZone');
            if (zone) {
                zone.style.borderColor = '';
                zone.style.background = '';
            }
        }

        /* SweetAlert custom styles */
        (function() {
            var s = document.createElement('style');
            s.textContent =
                '.swal2-popup{border-radius:.85rem!important;font-family:inherit!important}.swal2-title{font-size:1.1rem!important;color:#2d3748!important}.swal2-html-container{font-size:.88rem!important}.swal2-confirm,.swal2-cancel{border-radius:.5rem!important;font-size:.82rem!important;font-weight:700!important;padding:.5rem 1.2rem!important}';
            document.head.appendChild(s);
        }());
    </script>
@endpush
