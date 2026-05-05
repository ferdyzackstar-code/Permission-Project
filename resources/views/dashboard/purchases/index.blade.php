@extends('dashboard.layouts.admin')

@section('title', 'Manajemen Pembelian')

@push('styles')
    <style>
        :root {
            --pur-primary: #1565C0;
            --pur-success: #2E7D32;
            --pur-warning: #F57F17;
            --pur-danger: #C62828;
            --pur-info: #0288D1;
            --pur-radius: 12px;
            --pur-border: #E3EAF2;
            --pur-bg: #F0F4F8;
            --pur-text: #1A2332;
            --pur-muted: #7B8FA6;
        }

        /* ── HEADER BANNER ──────────────────────────────────────────── */
        .pur-header-card {
            background: linear-gradient(135deg, #0D47A1 0%, #1565C0 60%, #1976D2 100%);
            border-radius: var(--pur-radius);
            padding: 20px 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 20px rgba(21, 101, 192, .25);
        }

        .pur-header-card h4 {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
        }

        .pur-header-card p {
            color: rgba(255, 255, 255, .7);
            font-size: .82rem;
            margin: 2px 0 0;
        }

        .btn-hdr {
            font-size: .82rem;
            font-weight: 700;
            padding: 9px 18px;
            border-radius: 8px;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            position: relative;
            white-space: nowrap;
            border: 1.5px solid rgba(255, 255, 255, .3);
            color: #fff;
        }

        .btn-hdr:hover {
            text-decoration: none;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-hdr-yellow {
            background: linear-gradient(135deg, #F57F17, #F9A825);
            border-color: rgba(255, 255, 255, .25);
            box-shadow: 0 3px 12px rgba(245, 127, 23, .35);
        }

        .btn-hdr-yellow:hover {
            background: linear-gradient(135deg, #E65100, #F57F17);
        }

        .pending-badge {
            position: absolute;
            top: -9px;
            right: -9px;
            background: #E53935;
            color: #fff;
            font-size: .62rem;
            font-weight: 800;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            animation: badge-pop 2s ease infinite;
        }

        @keyframes badge-pop {

            0%,
            100% {
                transform: scale(1)
            }

            50% {
                transform: scale(1.18)
            }
        }

        /* ── STAT CARDS ─────────────────────────────────────────────── */
        .pur-stat-card {
            background: #fff;
            border-radius: var(--pur-radius);
            padding: 16px 20px;
            box-shadow: 0 2px 12px rgba(21, 101, 192, .07);
            display: flex;
            align-items: center;
            gap: 14px;
            border-left: 4px solid transparent;
            transition: all .2s;
            height: 100%;
        }

        .pur-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(21, 101, 192, .12);
        }

        .pur-stat-card .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            flex-shrink: 0;
        }

        .pur-stat-card .stat-val {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--pur-text);
            line-height: 1;
        }

        .pur-stat-card .stat-lbl {
            font-size: .72rem;
            color: var(--pur-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 600;
            margin-top: 3px;
        }

        .pur-stat-card.blue {
            border-color: var(--pur-primary);
        }

        .pur-stat-card.yellow {
            border-color: var(--pur-warning);
        }

        .pur-stat-card.green {
            border-color: var(--pur-success);
        }

        .pur-stat-card.red {
            border-color: var(--pur-danger);
        }

        .bg-blue {
            background: linear-gradient(135deg, #1565C0, #1976D2);
        }

        .bg-yellow {
            background: linear-gradient(135deg, #F57F17, #F9A825);
        }

        .bg-green {
            background: linear-gradient(135deg, #2E7D32, #43A047);
        }

        .bg-red {
            background: linear-gradient(135deg, #C62828, #E53935);
        }

        /* ── FORM CARD ──────────────────────────────────────────────── */
        .pur-form-card {
            background: #fff;
            border-radius: var(--pur-radius);
            box-shadow: 0 2px 16px rgba(21, 101, 192, .07);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .pur-form-header {
            background: linear-gradient(135deg, #1565C0, #1976D2);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pur-form-header h6 {
            color: #fff;
            margin: 0;
            font-size: .9rem;
            font-weight: 700;
        }

        .pur-form-body {
            padding: 20px 24px;
        }

        /* ── SUPPLIER CARDS — LEBIH KECIL ────────────────────────────── */
        .supplier-grid {
            display: grid;
            /* Mobile: 3 kolom, Desktop: auto-fill min 130px */
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 4px;
        }

        .supplier-card {
            border: 1.5px solid var(--pur-border);
            border-radius: 8px;
            padding: 8px 6px;
            cursor: pointer;
            transition: all .2s;
            text-align: center;
            background: #F8FAFD;
            user-select: none;
        }

        .supplier-card:hover {
            border-color: var(--pur-primary);
            background: #EEF4FF;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(21, 101, 192, .12);
        }

        .supplier-card.selected {
            border-color: var(--pur-primary);
            background: #E3F2FD;
            box-shadow: 0 0 0 2px rgba(21, 101, 192, .2);
        }

        .supplier-card .sc-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: linear-gradient(135deg, #1565C0, #42A5F5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .72rem;
            margin: 0 auto 5px;
        }

        .supplier-card .sc-name {
            font-size: .68rem;
            font-weight: 700;
            color: var(--pur-text);
            line-height: 1.25;
            margin-bottom: 2px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .supplier-card .sc-email {
            font-size: .6rem;
            color: var(--pur-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .supplier-card.selected .sc-name {
            color: var(--pur-primary);
        }

        /* ── PRODUCT ITEM ROW ───────────────────────────────────────── */
        .product-item-row {
            background: #F8FAFD;
            border: 1.5px solid var(--pur-border);
            border-radius: 10px;
            padding: 14px 16px 12px;
            margin-bottom: 12px;
            position: relative;
        }

        /* Qty inline: [ - ] [ input ] [ + ] — sempit */
        .qty-inline {
            display: flex;
            align-items: center;
            border: 1.5px solid var(--pur-border);
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            width: 100%;
        }

        .qty-inline .qty-btn {
            width: 30px;
            height: 36px;
            background: #F0F4F8;
            border: none;
            color: var(--pur-text);
            font-size: .8rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
            flex-shrink: 0;
        }

        .qty-inline .qty-btn:hover {
            background: var(--pur-primary);
            color: #fff;
        }

        .qty-inline .qty-btn.minus:hover {
            background: #EF5350;
        }

        .qty-inline input.qty-input {
            flex: 1;
            min-width: 0;
            height: 36px;
            border: none;
            border-left: 1px solid var(--pur-border);
            border-right: 1px solid var(--pur-border);
            text-align: center;
            font-size: .85rem;
            font-weight: 700;
            color: var(--pur-text);
            outline: none;
            background: #fff;
            padding: 0;
        }

        /* Remove button */
        .btn-remove-product {
            position: absolute;
            top: 10px;
            right: 12px;
            background: #FFEBEE;
            border: 1.5px solid #FFCDD2;
            color: var(--pur-danger);
            width: 26px;
            height: 26px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .68rem;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-remove-product:hover {
            background: var(--pur-danger);
            color: #fff;
            border-color: var(--pur-danger);
        }

        /* Grand total */
        .grand-total-box {
            background: linear-gradient(135deg, #E3F2FD, #EDE7F6);
            border-radius: 10px;
            padding: 12px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }

        .grand-total-box .gt-label {
            font-size: .88rem;
            font-weight: 700;
            color: var(--pur-text);
        }

        .grand-total-box .gt-value {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--pur-primary);
        }

        /* Buttons */
        .btn-pur-primary {
            background: linear-gradient(135deg, #1565C0, #1976D2);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: .88rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .2s;
            box-shadow: 0 3px 10px rgba(21, 101, 192, .25);
        }

        .btn-pur-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(21, 101, 192, .35);
            color: #fff;
        }

        .btn-pur-secondary {
            background: #F0F4F8;
            color: var(--pur-text);
            border: 1.5px solid var(--pur-border);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .15s;
        }

        .btn-pur-secondary:hover {
            background: #E3EAF2;
        }

        .btn-add-product {
            background: linear-gradient(135deg, #2E7D32, #43A047);
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .2s;
        }

        .btn-add-product:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, .3);
        }

        /* ── TABLE CARD ─────────────────────────────────────────────── */
        .pur-table-card {
            background: #fff;
            border-radius: var(--pur-radius);
            box-shadow: 0 2px 16px rgba(21, 101, 192, .07);
            overflow: hidden;
        }

        .pur-table-header {
            background: linear-gradient(135deg, #1565C0, #1976D2);
            padding: 14px 20px;
        }

        .pur-table-header h6 {
            color: #fff;
            margin: 0;
            font-size: .9rem;
            font-weight: 700;
        }

        #purchaseTable thead th {
            background: #F0F4F8 !important;
            color: #546E7A;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            border: none !important;
            padding: 12px 14px !important;
            white-space: nowrap;
        }

        #purchaseTable tbody td {
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-top: 1px solid #F0F4F8 !important;
            font-size: .84rem;
            color: #2C3E50;
        }

        #purchaseTable tbody tr:hover {
            background: #F8FAFD;
        }

        #purchaseTable {
            border-collapse: collapse !important;
        }

        .po-code {
            font-family: monospace;
            font-size: .78rem;
            background: #EEF2FF;
            color: #3949AB;
            padding: 3px 8px;
            border-radius: 5px;
            font-weight: 700;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
        }

        .badge-status.received {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .badge-status.pending {
            background: #FFF8E1;
            color: #F57F17;
        }

        .badge-status.cancelled {
            background: #FFEBEE;
            color: #C62828;
        }

        .btn-tbl {
            padding: 5px 11px;
            border-radius: 7px;
            font-size: .76rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all .15s;
            white-space: nowrap;
        }

        .btn-tbl-info {
            background: #E3F2FD;
            color: #1565C0;
        }

        .btn-tbl-info:hover {
            background: #BBDEFB;
        }

        .btn-tbl-warn {
            background: #FFF8E1;
            color: #F57F17;
            border: 1.5px solid #FFE082;
        }

        .btn-tbl-warn:hover {
            background: #FFE082;
        }

        /* ── FORM FIELD ─────────────────────────────────────────────── */
        .pur-label {
            font-size: .8rem;
            font-weight: 700;
            color: var(--pur-text);
            margin-bottom: 5px;
            display: block;
        }

        .pur-input {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid var(--pur-border);
            border-radius: 8px;
            font-size: .88rem;
            color: var(--pur-text);
            background: #F8FAFD;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .pur-input:focus {
            border-color: #42A5F5;
            box-shadow: 0 0 0 3px rgba(66, 165, 245, .15);
            background: #fff;
        }

        .pur-select {
            appearance: auto;
        }

        /* ── RESPONSIVE ─────────────────────────────────────────────── */
        @media (min-width: 768px) {
            .supplier-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
        }

        @media (max-width: 767px) {
            .supplier-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
            }

            .pur-form-body {
                padding: 14px;
            }

            .grand-total-box {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
        }

        @media (max-width: 400px) {
            .supplier-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <div class="pur-header-card">
            <div>
                <h4><i class="fas fa-shopping-cart mr-2"></i>Manajemen Pembelian Barang</h4>
                <p>Kelola pesanan pembelian ke supplier Anda Petshop</p>
            </div>
            <a href="{{ route('dashboard.purchases.confirmation') }}" class="btn-hdr btn-hdr-yellow">
                <i class="fas fa-hourglass-half"></i> Konfirmasi Pembelian
                @if ($pendingCount > 0)
                    <span class="pending-badge">{{ $pendingCount }}</span>
                @endif
            </a>
        </div>

        {{-- ── STAT CARDS ──────────────────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="pur-stat-card blue">
                    <div class="stat-icon bg-blue"><i class="fas fa-boxes"></i></div>
                    <div>
                        <div class="stat-val">{{ $totalProducts }}</div>
                        <div class="stat-lbl">Total Produk</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="pur-stat-card yellow">
                    <div class="stat-icon bg-yellow"><i class="fas fa-truck"></i></div>
                    <div>
                        <div class="stat-val">{{ $pendingCount }}</div>
                        <div class="stat-lbl">Dalam Perjalanan</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="pur-stat-card green">
                    <div class="stat-icon bg-green"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-val">{{ $receivedCount }}</div>
                        <div class="stat-lbl">Pembelian Selesai</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="pur-stat-card red">
                    <div class="stat-icon bg-red"><i class="fas fa-times-circle"></i></div>
                    <div>
                        <div class="stat-val">{{ $cancelledCount }}</div>
                        <div class="stat-lbl">Pembelian Batal</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── FORM TAMBAH / EDIT ───────────────────────────────────── --}}
        <div class="pur-form-card" id="purchaseFormCard">
            <div class="pur-form-header">
                <h6 id="formTitle"><i class="fas fa-plus-circle mr-2"></i>Tambah Pesanan Pembelian Baru</h6>
                <button type="button" class="btn-pur-secondary" id="resetFormBtn"
                    style="display:none; padding:6px 12px; font-size:.76rem;">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
            <div class="pur-form-body">
                <form id="purchaseForm">
                    @csrf
                    <input type="hidden" id="purchase_id" name="purchase_id">
                    <input type="hidden" id="form_method" value="POST">

                    {{-- Supplier --}}
                    <div class="mb-4">
                        <label class="pur-label">Pilih Supplier <span class="text-danger">*</span></label>
                        <div class="supplier-grid" id="supplierGrid">
                            @foreach ($suppliers as $supplier)
                                <div class="supplier-card" data-supplier-id="{{ $supplier->id }}">
                                    <div class="sc-icon"><i class="fas fa-building"></i></div>
                                    <div class="sc-name">{{ $supplier->name }}</div>
                                    <div class="sc-email">{{ $supplier->email }}</div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="supplier_id" id="supplier_id" required>
                        <small class="text-danger" id="supplierError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> Pilih supplier terlebih dahulu!
                        </small>
                    </div>

                    {{-- Tanggal & Catatan --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="pur-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" id="purchase_date" class="pur-input"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="pur-label">Catatan <span class="text-muted"
                                    style="font-weight:400;">(opsional)</span></label>
                            <input type="text" name="notes" id="notes" class="pur-input"
                                placeholder="Catatan tambahan...">
                        </div>
                    </div>

                    <hr style="border-color:#E3EAF2;">

                    {{-- Detail Produk --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="pur-label mb-0">
                            <i class="fas fa-box-open mr-1 text-primary"></i> Detail Produk
                        </label>
                        <button type="button" class="btn-add-product" id="addProductBtn">
                            <i class="fas fa-plus"></i> Tambah Produk
                        </button>
                    </div>

                    <div id="productItemsContainer"></div>

                    <div class="grand-total-box">
                        <span class="gt-label"><i class="fas fa-receipt mr-1"></i> Total Pembayaran</span>
                        <span class="gt-value" id="grandTotal">Rp 0</span>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4" style="gap:10px;">
                        <button type="submit" class="btn-pur-primary btn-submit">
                            <i class="fas fa-save"></i> Buat Pesanan
                        </button>
                        <button class="btn-pur-primary btn-loading d-none" type="button" disabled
                            style="background:#90A4AE; box-shadow:none; cursor:not-allowed;">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            Menyimpan...
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── TABLE RIWAYAT ───────────────────────────────────────── --}}
        <div class="pur-table-card">
            <div class="pur-table-header">
                <h6><i class="fas fa-list mr-2"></i>Riwayat Pembelian</h6>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="purchaseTable">
                        <thead>
                            <tr>
                                <th width="40px">No</th>
                                <th>No PO</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th class="text-center">Status</th>
                                <th width="130px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $i => $purchase)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><span class="po-code">{{ $purchase->purchase_number }}</span></td>
                                    <td style="white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->isoFormat('DD MMM YYYY') }}
                                    </td>
                                    <td>{{ $purchase->supplier->name }}</td>
                                    <td style="font-weight:700;">
                                        Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if ($purchase->status === 'received')
                                            <span class="badge-status received"><i class="fas fa-check-circle"></i>
                                                Selesai</span>
                                        @elseif($purchase->status === 'cancelled')
                                            <span class="badge-status cancelled"><i class="fas fa-times-circle"></i>
                                                Batal</span>
                                        @else
                                            <span class="badge-status pending"><i class="fas fa-clock"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        <button class="btn-tbl btn-tbl-info detail-btn" data-id="{{ $purchase->id }}">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                        @if ($purchase->status === 'pending')
                                            <button class="btn-tbl btn-tbl-warn edit-btn ml-1"
                                                data-id="{{ $purchase->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- ── MODAL DETAIL ─────────────────────────────────────────── --}}
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none;">
                <div class="modal-header text-white"
                    style="background:linear-gradient(135deg,#0288D1,#03A9F4); border:none;">
                    <h5 class="modal-title mb-0 font-weight-bold">
                        <i class="fas fa-file-invoice mr-2"></i>Detail Pesanan Pembelian
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"
                        style="opacity:1;">&times;</button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div style="background:#F8FAFD; border-radius:10px; padding:16px;">
                                <div class="d-flex justify-content-between py-2 border-bottom"><span
                                        class="text-muted small">No. PO</span><strong id="detail_po" class="small"
                                        style="font-family:monospace;"></strong></div>
                                <div class="d-flex justify-content-between py-2 border-bottom"><span
                                        class="text-muted small">Tanggal</span><span id="detail_date"
                                        class="small"></span></div>
                                <div class="d-flex justify-content-between py-2"><span
                                        class="text-muted small">Supplier</span><span id="detail_supplier"
                                        class="small font-weight-bold"></span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="background:#F8FAFD; border-radius:10px; padding:16px;">
                                <div class="d-flex justify-content-between py-2 border-bottom"><span
                                        class="text-muted small">Status</span><span id="detail_status"></span></div>
                                <div class="d-flex justify-content-between py-2 border-bottom"><span
                                        class="text-muted small">Catatan</span><span id="detail_notes"
                                        class="small"></span></div>
                                <div class="d-flex justify-content-between py-2"><span
                                        class="text-muted small">Total</span><strong id="detail_total"
                                        class="text-primary"></strong></div>
                            </div>
                        </div>
                    </div>
                    <h6 class="font-weight-bold mb-3">Detail Produk</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead style="background:#F0F4F8;">
                                <tr>
                                    <th>Produk</th>
                                    <th width="18%">Harga Satuan</th>
                                    <th width="10%" class="text-center">Qty</th>
                                    <th width="18%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detail_items"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;">
                    <button type="button" class="btn-pur-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let productRowIndex = 0;
        const products = @json($products);

        function formatRupiah(angka) {
            if (angka === undefined || angka === null) return '0';
            let number = Math.round(parseFloat(angka));
            if (isNaN(number)) return '0';
            let s = number.toString();
            let sisa = s.length % 3;
            let rupiah = s.substr(0, sisa);
            let ribuan = s.substr(sisa).match(/\d{3}/gi);
            if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
            return rupiah;
        }

        function calculateGrandTotal() {
            let total = 0;
            $('.product-item-row').each(function() {
                let qty = parseInt($(this).find('.qty-input').val()) || 0;
                let price = parseFloat($(this).find('.price-input').val().replace(/\./g, '')) || 0;
                let sub = qty * price;
                $(this).find('.subtotal-display').text('Rp ' + formatRupiah(sub));
                total += sub;
            });
            $('#grandTotal').text('Rp ' + formatRupiah(total));
        }

        function addProductRow(product_id = '', quantity = 1, price = '') {
            productRowIndex++;
            let opts = '<option value="">-- Pilih Produk --</option>';
            products.forEach(p => {
                opts += `<option value="${p.id}" ${p.id == product_id ? 'selected' : ''}>${p.name}</option>`;
            });

            $('#productItemsContainer').append(`
        <div class="product-item-row" data-index="${productRowIndex}">
            <button type="button" class="btn-remove-product remove-product">
                <i class="fas fa-times"></i>
            </button>

            {{-- Baris 1: Pilih Produk (full) --}}
            <div class="mb-2" style="padding-right:36px;">
                <label class="pur-label">Produk</label>
                <select name="product_id[]" class="pur-input pur-select" required>${opts}</select>
            </div>

            {{-- Baris 2: Jumlah (kiri) & Harga Satuan (kanan) dalam satu baris --}}
            <div class="d-flex align-items-end mb-2" style="gap:12px;">
                <div style="width:100px; flex-shrink:0;">
                    <label class="pur-label">Jumlah</label>
                    <div class="qty-inline">
                        <button type="button" class="qty-btn minus qty-minus">
                            <i class="fas fa-minus" style="font-size:.55rem;"></i>
                        </button>
                        <input type="number" name="quantity[]" class="qty-input" value="${quantity}" min="1" required>
                        <button type="button" class="qty-btn plus qty-plus">
                            <i class="fas fa-plus" style="font-size:.55rem;"></i>
                        </button>
                    </div>
                </div>
                <div style="flex:1; min-width:0;">
                    <label class="pur-label">Harga Satuan</label>
                    <input type="text" name="price[]" class="pur-input price-input"
                           value="${price}" placeholder="0" required>
                </div>
            </div>

            {{-- Baris 3: Subtotal (full) --}}
            <div>
                <label class="pur-label">Subtotal</label>
                <div class="pur-input subtotal-display"
                     style="background:#EEF4FF; color:#1565C0; font-weight:700; cursor:default; font-size:.85rem;">
                    Rp 0
                </div>
            </div>
        </div>
    `);
            calculateGrandTotal();
        }

        $(document).ready(function() {

            $('#purchaseTable').DataTable({
                order: [
                    [2, 'desc']
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Cari PO, supplier...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ pembelian',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    },
                    emptyTable: '<div class="text-center py-3 text-muted">Belum ada data pembelian</div>',
                },
                dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>',
            });

            // Supplier select
            $(document).on('click', '.supplier-card', function() {
                $('.supplier-card').removeClass('selected');
                $(this).addClass('selected');
                $('#supplier_id').val($(this).data('supplier-id'));
                $('#supplierError').hide();
            });

            addProductRow();

            $('#addProductBtn').on('click', () => addProductRow());

            $(document).on('click', '.remove-product', function() {
                if ($('.product-item-row').length > 1) {
                    $(this).closest('.product-item-row').remove();
                    calculateGrandTotal();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Minimal harus ada 1 produk!',
                        confirmButtonColor: '#1565C0'
                    });
                }
            });

            $(document).on('click', '.qty-plus', function() {
                let inp = $(this).siblings('.qty-input');
                inp.val(parseInt(inp.val()) + 1);
                calculateGrandTotal();
            });

            $(document).on('click', '.qty-minus', function() {
                let inp = $(this).siblings('.qty-input');
                let val = parseInt(inp.val()) || 0;
                if (val > 1) {
                    inp.val(val - 1);
                    calculateGrandTotal();
                }
            });

            $(document).on('input', '.price-input', function() {
                let d = $(this).val().replace(/[^0-9]/g, '').substring(0, 15);
                let f = formatRupiah(d);
                $(this).val(f);
                this.setSelectionRange(f.length, f.length);
                calculateGrandTotal();
            });

            $(document).on('input change', '.qty-input', () => calculateGrandTotal());

            // Submit
            $('#purchaseForm').submit(function(e) {
                e.preventDefault();
                if (!$('#supplier_id').val()) {
                    $('#supplierError').show();
                    Swal.fire({
                        icon: 'error',
                        title: 'Pilih Supplier',
                        text: 'Pilih supplier terlebih dahulu!',
                        confirmButtonColor: '#1565C0'
                    });
                    return;
                }
                $('.btn-submit').addClass('d-none');
                $('.btn-loading').removeClass('d-none');

                let method = $('#form_method').val();
                let url = method === 'PUT' ?
                    "{{ url('dashboard/purchases') }}/" + $('#purchase_id').val() :
                    "{{ route('dashboard.purchases.store') }}";

                $.ajax({
                    url,
                    type: 'POST',
                    data: $(this).serialize() + (method === 'PUT' ? '&_method=PUT' : ''),
                    success: res => Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        confirmButtonColor: '#1565C0'
                    }).then(() => location.reload()),
                    error: xhr => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Gagal menyimpan!',
                            confirmButtonColor: '#1565C0'
                        });
                        $('.btn-submit').removeClass('d-none');
                        $('.btn-loading').addClass('d-none');
                    }
                });
            });

            // Detail
            $(document).on('click', '.detail-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('#detail_po').text(data.purchase_number);
                    let d = new Date(data.purchase_date);
                    $('#detail_date').text(d.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }));
                    $('#detail_supplier').text(data.supplier.name);
                    $('#detail_notes').text(data.notes || '-');
                    $('#detail_total').text('Rp ' + formatRupiah(data.total_amount));

                    let badge = data.status === 'received' ?
                        '<span class="badge-status received"><i class="fas fa-check-circle"></i> Selesai</span>' :
                        data.status === 'cancelled' ?
                        '<span class="badge-status cancelled"><i class="fas fa-times-circle"></i> Batal</span>' :
                        '<span class="badge-status pending"><i class="fas fa-clock"></i> Pending</span>';
                    $('#detail_status').html(badge);

                    let html = '';
                    data.items.forEach(item => {
                        html += `<tr>
                    <td>${item.product.name}</td>
                    <td class="text-right">Rp ${formatRupiah(item.price)}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-right font-weight-bold">Rp ${formatRupiah(item.subtotal)}</td>
                </tr>`;
                    });
                    $('#detail_items').html(html);
                    $('#detailModal').modal('show');
                });
            });

            // Edit
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('html, body').animate({
                        scrollTop: $('#purchaseFormCard').offset().top - 80
                    }, 400);
                    $('#formTitle').html('<i class="fas fa-edit mr-2"></i>Edit Pesanan Pembelian');
                    $('#form_method').val('PUT');
                    $('#purchase_id').val(data.id);
                    $('.btn-submit').html('<i class="fas fa-save"></i> Update Pesanan');
                    $('#resetFormBtn').show();
                    $('.supplier-card').removeClass('selected');
                    $(`.supplier-card[data-supplier-id="${data.supplier_id}"]`).addClass(
                    'selected');
                    $('#supplier_id').val(data.supplier_id);
                    $('#purchase_date').val(data.purchase_date);
                    $('#notes').val(data.notes);
                    $('#productItemsContainer').empty();
                    data.items.forEach(item => addProductRow(item.product_id, item.quantity,
                        formatRupiah(item.price)));
                    calculateGrandTotal();
                });
            });

            // Reset
            $('#resetFormBtn').on('click', function() {
                $('#formTitle').html(
                '<i class="fas fa-plus-circle mr-2"></i>Tambah Pesanan Pembelian Baru');
                $('#form_method').val('POST');
                $('#purchase_id').val('');
                $('#purchaseForm')[0].reset();
                $('.supplier-card').removeClass('selected');
                $('#supplier_id').val('');
                $('.btn-submit').html('<i class="fas fa-save"></i> Buat Pesanan');
                $(this).hide();
                $('#productItemsContainer').empty();
                addProductRow();
                calculateGrandTotal();
            });
        });
    </script>
@endpush
