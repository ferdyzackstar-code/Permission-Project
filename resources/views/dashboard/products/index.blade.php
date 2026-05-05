@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        /* ══════════════════════════════════════════
               FIX LAYOUT SHIFT
               html { overflow-y: scroll } → scrollbar
               selalu ada → lebar konten tidak berubah
               saat modal/swal muncul
            ══════════════════════════════════════════ */
        html {
            overflow-y: scroll;
        }

        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        body.swal2-shown {
            padding-right: 0 !important;
        }

        .swal2-container {
            overflow-y: auto !important;
        }

        /* ══════════════════════════════════════════
               TOKENS
            ══════════════════════════════════════════ */
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

        /* ══════════════════════════════════════════
               PAGE HEADER
            ══════════════════════════════════════════ */
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

        /* ══════════════════════════════════════════
               BUTTONS
            ══════════════════════════════════════════ */
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

        .btn-primary-pill:disabled,
        .btn-primary-pill[disabled] {
            opacity: .5;
            transform: none !important;
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

        .btn-cancel-pill {
            background: #fff;
            color: var(--dark) !important;
            border: 1.5px solid #d1d3e2 !important;
        }

        .btn-cancel-pill:hover {
            background: #f0f0f5;
        }

        /* ══════════════════════════════════════════
               TOOLBAR
            ══════════════════════════════════════════ */
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

        /* ══════════════════════════════════════════
               INLINE PANEL
            ══════════════════════════════════════════ */
        .product-panel {
            border: none;
            border-radius: var(--rad);
            box-shadow: var(--shad);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .panel-header {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
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

        .panel-hdr-icon {
            width: 38px;
            height: 38px;
            border-radius: .5rem;
            background: rgba(255, 255, 255, .18);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .panel-hdr-icon i {
            color: #fff;
            font-size: .9rem;
        }

        .panel-hdr-title {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
        }

        .panel-hdr-sub {
            font-size: .73rem;
            color: rgba(255, 255, 255, .75);
            margin: .1rem 0 0;
        }

        .panel-body-wrap {
            background: #fff;
            padding: 1.5rem;
        }

        /* Section labels */
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

        .fs-label.edit-clr i {
            color: #d4a017;
        }

        .fs-label.show-clr i {
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

        /* FIX #3 — Input group prefix sejajar dengan input */
        .fc-input-group {
            display: flex;
            align-items: stretch;
        }

        .fc-prefix {
            background: #eaecf4;
            border: 1.5px solid #d1d3e2;
            border-right: none;
            border-radius: .5rem 0 0 .5rem;
            padding: 0 .85rem;
            /* padding vertikal 0 agar flex stretch */
            font-size: .8rem;
            color: #858796;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .fc-input-group .fc-input {
            border-radius: 0 .5rem .5rem 0;
            flex: 1;
        }

        /* Image upload */
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

        .upload-icon {
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

        /* Show view detail rows */
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

        /* ══════════════════════════════════════════
               TABLE CARD
            ══════════════════════════════════════════ */
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

        .hdr-icon {
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

        .hdr-title {
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

        /* FIX #2 — Table styling sesuai sebelumnya */
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

        /* FIX #2 — class tbl-img sesuai dengan yang dirender controller */
        .tbl-img {
            width: 48px;
            height: 48px;
            border-radius: .45rem;
            object-fit: cover;
            border: 2px solid #e3e6f0;
            transition: transform .2s, box-shadow .2s;
        }

        .tbl-img:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
        }

        /* Pill badges */
        .tbl-pill {
            display: inline-block;
            font-size: .72rem;
            font-weight: 700;
            padding: .28em .75em;
            border-radius: 2rem;
        }

        .tbl-pill-species {
            background: rgba(78, 115, 223, .1);
            color: #4e73df;
            border: 1px solid rgba(78, 115, 223, .2);
        }

        .tbl-pill-category {
            background: rgba(54, 185, 204, .1);
            color: #258391;
            border: 1px solid rgba(54, 185, 204, .2);
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

        .tbl-price {
            font-weight: 700;
            color: #1cc88a;
            white-space: nowrap;
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

        .tbl-btn-view:hover {
            background: var(--ba);
            color: #fff;
            transform: translateY(-2px);
        }

        .tbl-btn-edit:hover {
            background: var(--bw);
            color: #fff;
            transform: translateY(-2px);
        }

        .tbl-btn-delete:hover {
            background: var(--bd);
            color: #fff;
            transform: translateY(-2px);
        }

        .tbl-btn-view {
            background: rgba(54, 185, 204, .12);
            color: var(--ba);
        }

        .tbl-btn-edit {
            background: rgba(246, 194, 62, .12);
            color: #d4a017;
        }

        .tbl-btn-delete {
            background: rgba(231, 74, 59, .1);
            color: var(--bd);
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

    {{-- PAGE HEADER --}}
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

    {{-- FLASH MESSAGES
     FIX #1: controller sudah kirim plain text (tanpa <strong>)
     Di sini pakai {{ }} biasa — aman dari XSS, tidak ada HTML literal --}}
    @if (session('success'))
        <div class="alert alert-modern alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2" style="color:var(--bsu)"></i>
            {{ session('success') }}
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

    {{-- ACTION TOOLBAR --}}
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
══════════════════════════════════════════ --}}
    @canany(['product.create', 'product.edit', 'product.show'])
        <div class="product-panel animate__animated animate__fadeInUp animate__faster" id="productPanel">

            {{-- Header --}}
            <div class="panel-header panel-header-create" id="panelHeader">
                <div class="panel-hdr-icon"><i class="fas fa-plus" id="panelIconFa"></i></div>
                <div style="flex:1;min-width:0">
                    <div class="panel-hdr-title" id="panelTitle">Tambah Produk Baru</div>
                    <div class="panel-hdr-sub" id="panelSub">Lengkapi semua field bertanda * untuk mengaktifkan tombol simpan
                    </div>
                </div>
                <button type="button" id="btnTogglePanel" class="btn-pill btn-cancel-pill ml-2"
                    style="padding:.3rem .75rem;font-size:.72rem;flex-shrink:0">
                    <i class="fas fa-chevron-up" id="toggleIcon"></i>
                </button>
            </div>

            {{-- Panel Body --}}
            <div id="panelBodyWrap">

                {{-- ── SHOW VIEW ── --}}
                <div id="showView" class="d-none panel-body-wrap">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img id="showImg" src="" alt="foto"
                                style="width:130px;height:130px;object-fit:cover;border-radius:.65rem;border:2.5px solid #36b9cc;box-shadow:0 4px 16px rgba(54,185,204,.2)">
                            <div class="mt-2">
                                <span class="badge-status" id="showStatusBadge"></span>
                            </div>
                            <div class="mt-1 small text-muted" id="showUpdatedAt"></div>
                        </div>
                        <div class="col-md-9">
                            <div class="fs-label show-clr"><i class="fas fa-info-circle"></i> Detail Produk</div>
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
                                    <div class="detail-value font-weight-bold" id="showPrice"
                                        style="color:#1cc88a;font-size:.95rem"></div>
                                </div>
                            </div>
                            <div class="detail-divider"></div>
                            <div class="detail-row">
                                <div class="detail-icon" style="background:rgba(246,194,62,.15);color:#d4a017"><i
                                        class="fas fa-boxes"></i></div>
                                <div>
                                    <div class="detail-label">Stok</div>
                                    <div class="detail-value"><span id="showStock" class="stock-pill"></span></div>
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
                                    <div class="detail-value small" id="showDetail"
                                        style="line-height:1.6;white-space:pre-line"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3 pt-3" style="border-top:1px solid #e3e6f0;gap:.5rem">
                        <button type="button" class="btn-pill btn-cancel-pill" id="btnShowClose">
                            <i class="fas fa-times mr-1"></i> Tutup
                        </button>
                        @can('product.edit')
                            <button type="button" class="btn-pill" id="btnShowToEdit"
                                style="background:linear-gradient(135deg,#f6c23e,#d4a017);color:#fff;box-shadow:0 4px 12px rgba(246,194,62,.35)">
                                <i class="fas fa-edit mr-1"></i> Edit Produk Ini
                            </button>
                        @endcan
                    </div>
                </div>

                {{-- ── CREATE / EDIT FORM ── --}}
                <form id="productForm" action="{{ route('dashboard.products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div id="methodSpan"></div>

                    <div class="panel-body-wrap">

                        {{-- Informasi Dasar --}}
                        <div class="fs-label"><i class="fas fa-info-circle"></i> Informasi Dasar</div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="fc-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="fName" class="fc-input req-field"
                                    placeholder="Contoh: Whiskas Tuna 1kg" autocomplete="off">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fc-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="fStatus" class="fc-input fc-select req-field">
                                    <option value="active">✅ Active</option>
                                    <option value="inactive">❌ Inactive</option>
                                </select>
                            </div>
                        </div>

                        {{-- Harga & Stok --}}
                        <div class="fs-label mt-1"><i class="fas fa-tags"></i> Harga & Stok</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fc-label">Harga (Rp) <span class="text-danger">*</span></label>
                                {{-- FIX #3 — fc-input-group menggantikan bootstrap input-group --}}
                                <div class="fc-input-group">
                                    <span class="fc-prefix">Rp</span>
                                    <input type="text" name="price" id="fPrice" class="fc-input req-field"
                                        placeholder="50.000" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fc-label">Stok (Pcs) <span class="text-danger">*</span></label>
                                <div class="fc-input-group">
                                    <span class="fc-prefix"><i class="fas fa-boxes"></i></span>
                                    <input type="number" name="stock" id="fStock" class="fc-input req-field"
                                        placeholder="10" min="0" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        {{-- Kategori --}}
                        <div class="fs-label mt-1"><i class="fas fa-sitemap"></i> Kategori</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fc-label">Species (Induk) <span class="text-danger">*</span></label>
                                <select id="fSpecies" class="fc-input fc-select req-field">
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
                                <select name="category_id" id="fCategory" class="fc-input fc-select req-field" disabled>
                                    <option value="">-- Pilih Species Dulu --</option>
                                </select>
                                <span class="small text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle mr-1"></i>Pilih species untuk memuat kategori
                                </span>
                            </div>
                        </div>

                        {{-- Foto & Deskripsi (opsional) --}}
                        <div class="fs-label mt-1">
                            <i class="fas fa-image"></i> Foto & Deskripsi
                            <small class="text-muted font-weight-normal ml-1"
                                style="font-size:.65rem;text-transform:none;letter-spacing:0">(opsional)</small>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="fc-label">Foto Produk</label>
                                <div class="img-upload-zone" id="uploadZone"
                                    onclick="document.getElementById('fImage').click()">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <p class="mb-0 mt-1 small" style="color:#5a5c69">Klik untuk pilih foto</p>
                                    <p class="mb-0 small text-muted">JPG, PNG — maks. 2MB</p>
                                    <input type="file" id="fImage" name="image" class="d-none" accept="image/*"
                                        onchange="handleImageUpload(this)">
                                </div>
                                {{-- Preview foto baru --}}
                                <div class="img-preview-box d-none mt-2" id="imgPreviewBox">
                                    <img id="imgPreview" src="" alt="preview">
                                    <button type="button" class="img-remove-btn" onclick="removeProductImage()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                {{-- Foto existing (edit mode) --}}
                                <div class="d-none mt-2" id="currentImgWrap">
                                    <div class="small text-muted mb-1"><i class="fas fa-image mr-1"></i>Foto saat ini:</div>
                                    <img id="currentImg" src="" alt="current"
                                        style="width:70px;height:70px;object-fit:cover;border-radius:.45rem;border:2px solid #e3e6f0">
                                </div>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="fc-label">Detail / Deskripsi</label>
                                <textarea name="detail" id="fDetail" class="fc-input" rows="5"
                                    placeholder="Tulis deskripsi lengkap produk..."></textarea>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-2 pt-3"
                            style="border-top:1px solid #e3e6f0;gap:.5rem">
                            <span class="small text-muted">
                                <i class="fas fa-asterisk text-danger mr-1" style="font-size:.5rem;vertical-align:middle"></i>
                                Field bertanda * wajib diisi sebelum tombol simpan aktif
                            </span>
                            <div class="d-flex" style="gap:.5rem">
                                <button type="button" class="btn-pill btn-cancel-pill" id="btnFormReset">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </button>
                                @canany(['product.create', 'product.edit'])
                                    <button type="submit" class="btn-pill btn-primary-pill" id="btnSubmit" disabled>
                                        <i class="fas fa-save" id="submitIcon"></i>
                                        <span id="submitLabel"> Simpan Produk</span>
                                    </button>
                                @endcanany
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    @endcanany

    {{-- DATATABLE CARD --}}
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

    {{-- MODAL IMPORT --}}
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
        /* ══════════════════════════════════════════════
       PATCH Bootstrap _adjustDialog ASAP
       Harus sebelum document.ready agar intercept
       sebelum modal import pertama kali terbuka
    ══════════════════════════════════════════════ */
        (function patchModal() {
            var t = setInterval(function() {
                if (window.$ && $.fn.modal && $.fn.modal.Constructor) {
                    $.fn.modal.Constructor.prototype._adjustDialog = function() {};
                    $.fn.modal.Constructor.prototype._resetAdjustments = function() {};
                    clearInterval(t);
                }
            }, 30);
        }());

        /* SweetAlert custom styles */
        (function() {
            var s = document.createElement('style');
            s.textContent = [
                '.swal2-popup{border-radius:.85rem!important;font-family:inherit!important}',
                '.swal2-title{font-size:1.1rem!important;color:#2d3748!important}',
                '.swal2-html-container{font-size:.88rem!important}',
                '.swal2-confirm,.swal2-cancel{border-radius:.5rem!important;font-size:.82rem!important;font-weight:700!important;padding:.5rem 1.2rem!important}',
            ].join('');
            document.head.appendChild(s);
        }());

        $(document).ready(function() {

            /* ══════════════════════════════════════════
               PANEL STATE
            ══════════════════════════════════════════ */
            var mode = 'create'; // 'create' | 'edit' | 'show'
            var activeProduct = null; // raw data object dari DataTable
            var collapsed = false;

            var $panel = $('#productPanel');
            var $header = $('#panelHeader');
            var $bodyWrap = $('#panelBodyWrap');
            var $showView = $('#showView');
            var $form = $('#productForm');
            var $btnSubmit = $('#btnSubmit');

            /* ── Switch mode ───────────────────────── */
            function switchMode(newMode, product) {
                mode = newMode;
                activeProduct = product || null;

                if (collapsed) doCollapse(false);

                $header.removeClass('panel-header-create panel-header-edit panel-header-show');

                if (newMode === 'show') {
                    $header.addClass('panel-header-show');
                    $('#panelIconFa').attr('class', 'fas fa-eye');
                    $('#panelTitle').text('Detail Produk');
                    $('#panelSub').text(product ? product.name_raw : '');
                    $form.addClass('d-none');
                    $showView.removeClass('d-none');
                    fillShow(product);

                } else if (newMode === 'edit') {
                    $header.addClass('panel-header-edit');
                    $('#panelIconFa').attr('class', 'fas fa-edit');
                    $('#panelTitle').text('Edit Produk');
                    $('#panelSub').text(product ? product.name_raw : '');
                    $showView.addClass('d-none');
                    $form.removeClass('d-none');
                    fillForm(product);
                    $('#methodSpan').html('<input type="hidden" name="_method" value="PUT">');
                    $form.attr('action', '/dashboard/products/' + product.id);
                    $('#submitIcon').attr('class', 'fas fa-save');
                    $('#submitLabel').text(' Update Produk');

                } else { // create
                    $header.addClass('panel-header-create');
                    $('#panelIconFa').attr('class', 'fas fa-plus');
                    $('#panelTitle').text('Tambah Produk Baru');
                    $('#panelSub').text('Lengkapi semua field bertanda * untuk mengaktifkan tombol simpan');
                    $showView.addClass('d-none');
                    $form.removeClass('d-none');
                    resetForm();
                    $('#methodSpan').html('');
                    $form.attr('action', '{{ route('dashboard.products.store') }}');
                    $('#submitIcon').attr('class', 'fas fa-save');
                    $('#submitLabel').text(' Simpan Produk');
                }

                $('html,body').animate({
                    scrollTop: $panel.offset().top - 72
                }, 280);
            }

            /* ── Fill SHOW view ────────────────────── */
            function fillShow(d) {
                $('#showImg').attr('src', d.image_url || '');

                var isActive = d.status_raw === 'active';
                $('#showStatusBadge')
                    .attr('class', 'badge-status ' + (isActive ? 'badge-active' : 'badge-inactive'))
                    .text(isActive ? '✅ Active' : '❌ Inactive');

                $('#showName').text(d.name_raw || '—');
                $('#showPrice').text('Rp ' + rupiah(String(d.price_raw || 0)));
                $('#showSpecies').text(d.species_raw || '—');
                $('#showCategory').text(d.category_raw || '—');
                $('#showDetail').text(d.detail || '—');

                var stk = parseInt(d.stock_raw) || 0;
                var sc = stk === 0 ? 'stock-zero' : (stk <= 5 ? 'stock-low' : 'stock-ok');
                var si = stk === 0 ? 'fa-times-circle' : (stk <= 5 ? 'fa-exclamation-circle' : 'fa-check-circle');
                $('#showStock')
                    .attr('class', 'stock-pill ' + sc)
                    .html('<i class="fas ' + si + '"></i> ' + stk + ' Pcs');
            }

            /* ── Fill FORM (edit) ───────────────────── */
            function fillForm(d) {
                $('#fName').val(d.name_raw || '');
                $('#fStatus').val(d.status_raw || 'active');
                $('#fDetail').val(d.detail || '');
                $('#fPrice').val(rupiah(String(d.price_raw || 0)));
                $('#fStock').val(d.stock_raw || 0);

                if (d.image_url) {
                    $('#currentImg').attr('src', d.image_url);
                    $('#currentImgWrap').removeClass('d-none');
                } else {
                    $('#currentImgWrap').addClass('d-none');
                }

                if (d.species_id) {
                    $('#fSpecies').val(d.species_id);
                    fetchCats(d.species_id, d.category_id);
                } else {
                    $('#fSpecies').val('');
                    $('#fCategory').prop('disabled', true).empty()
                        .append('<option value="">-- Pilih Species Dulu --</option>');
                }

                checkValid();
            }

            /* ── Reset FORM ─────────────────────────── */
            function resetForm() {
                document.getElementById('productForm').reset();
                $('#fCategory').prop('disabled', true).empty()
                    .append('<option value="">-- Pilih Species Dulu --</option>');
                $('#imgPreviewBox').addClass('d-none');
                $('#currentImgWrap').addClass('d-none');
                document.getElementById('fImage').value = '';
                resetUploadZone();
                checkValid();
            }

            /* ── Collapse / Expand ──────────────────── */
            function doCollapse(toCollapse) {
                collapsed = toCollapse;
                if (collapsed) {
                    $bodyWrap.slideUp(220);
                    $('#toggleIcon').attr('class', 'fas fa-chevron-down');
                } else {
                    $bodyWrap.slideDown(220);
                    $('#toggleIcon').attr('class', 'fas fa-chevron-up');
                }
            }

            $('#btnTogglePanel').on('click', function() {
                doCollapse(!collapsed);
            });

            /* ── Reset to create ────────────────────── */
            $('#btnFormReset').on('click', function() {
                switchMode('create');
            });
            $('#btnShowClose').on('click', function() {
                switchMode('create');
            });

            /* ── Show → Edit button ─────────────────── */
            $('#btnShowToEdit').on('click', function() {
                if (activeProduct) switchMode('edit', activeProduct);
            });

            /* ══════════════════════════════════════════
               VALIDITY CHECK — disable/enable submit
            ══════════════════════════════════════════ */
            function checkValid() {
                var ok = true;
                $('#productForm .req-field').each(function() {
                    var v = $(this).val();
                    if (!v || v.trim() === '' || $(this).prop('disabled')) {
                        if (this.id === 'fCategory' && !$(this).prop('disabled') && !v) {
                            ok = false;
                        } else if (this.id !== 'fCategory' && (!v || v.trim() === '')) {
                            ok = false;
                        }
                        return false;
                    }
                });
                if ($('#fCategory').prop('disabled') || !$('#fCategory').val()) ok = false;

                $btnSubmit.prop('disabled', !ok);
            }

            $(document).on('input change', '#productForm .req-field', checkValid);

            /* ══════════════════════════════════════════
               FORM SUBMIT
            ══════════════════════════════════════════ */
            $('#productForm').on('submit', function(e) {
                var rawPrice = $('#fPrice').val().replace(/\./g, '');
                $('#fPrice').val(rawPrice);

                if (mode === 'edit') {
                    e.preventDefault();
                    $('#fPrice').val(rawPrice); // pastikan sudah stripped
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
                            Swal.fire({
                                title: 'Menyimpan...',
                                allowOutsideClick: false,
                                didOpen: function() {
                                    Swal.showLoading();
                                }
                            });
                            document.getElementById('productForm').submit();
                        } else {
                            $('#fPrice').val(rupiah(rawPrice));
                        }
                    });
                }
            });

            /* ══════════════════════════════════════════
               SUB-CATEGORY FETCH
            ══════════════════════════════════════════ */
            function fetchCats(speciesId, preselectId) {
                var $cat = $('#fCategory');
                if (!speciesId) {
                    $cat.prop('disabled', true).empty().append(
                    '<option value="">-- Pilih Species Dulu --</option>');
                    checkValid();
                    return;
                }
                $cat.prop('disabled', true).empty().append('<option value="">Memuat...</option>');
                $.ajax({
                    url: '{{ url('dashboard/get-subcategories') }}/' + speciesId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $cat.empty().append('<option value="">-- Pilih Kategori --</option>');
                        $.each(data, function(k, v) {
                            $cat.append('<option value="' + v.id + '">' + v.name + '</option>');
                        });
                        $cat.prop('disabled', data.length === 0);
                        if (preselectId) $cat.val(preselectId);
                        checkValid();
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memuat kategori.', 'error');
                        $cat.prop('disabled', false).empty().append(
                            '<option value="">-- Error --</option>');
                    }
                });
            }

            $('#fSpecies').on('change', function() {
                fetchCats($(this).val(), null);
            });

            /* ══════════════════════════════════════════
               RUPIAH
            ══════════════════════════════════════════ */
            $(document).on('keyup', '#fPrice', function() {
                $(this).val(rupiah($(this).val()));
                checkValid();
            });

            /* ══════════════════════════════════════════
               DELETE CONFIRM
            ══════════════════════════════════════════ */
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

            /* ══════════════════════════════════════════
               FIX #4 — TOMBOL VIEW & EDIT
               Controller inject data-product='...' JSON
               Button class: btn-view-product / btn-edit-product
            ══════════════════════════════════════════ */
            $(document).on('click', '.btn-view-product', function() {
                var raw = $(this).attr('data-product');
                if (!raw) return;
                try {
                    var product = JSON.parse(raw);
                    switchMode('show', product);
                } catch (e) {
                    console.error('parse error view:', e);
                }
            });

            $(document).on('click', '.btn-edit-product', function() {
                var raw = $(this).attr('data-product');
                if (!raw) return;
                try {
                    var product = JSON.parse(raw);
                    switchMode('edit', product);
                } catch (e) {
                    console.error('parse error edit:', e);
                }
            });

            /* ══════════════════════════════════════════
               DATATABLE
            ══════════════════════════════════════════ */
            $('#data-products').DataTable({
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
                    url: '{{ route('dashboard.products.index') }}',
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

            /* ── Import drag & drop ─────────────────── */
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

        }); // end ready

        /* ══════════════════════════════════════════
           GLOBAL HELPERS
        ══════════════════════════════════════════ */
        function rupiah(angka) {
            var str = String(angka).replace(/[^,\d]/g, '');
            var sp = str.split(',');
            var sis = sp[0].length % 3;
            var rp = sp[0].substr(0, sis);
            var rb = sp[0].substr(sis).match(/\d{3}/gi);
            if (rb) rp += (sis ? '.' : '') + rb.join('.');
            return sp[1] !== undefined ? rp + ',' + sp[1] : rp;
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
                var icon = zone ? zone.querySelector('.upload-icon') : null;
                if (icon) icon.style.color = '#1cc88a';
            };
            reader.readAsDataURL(file);
        }

        function removeProductImage() {
            document.getElementById('fImage').value = '';
            $('#imgPreviewBox').addClass('d-none');
            resetUploadZone();
        }

        function resetUploadZone() {
            var zone = document.getElementById('uploadZone');
            if (!zone) return;
            zone.style.borderColor = '';
            zone.style.background = '';
            var icon = zone.querySelector('.upload-icon');
            if (icon) icon.style.color = '';
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
    </script>
@endpush
