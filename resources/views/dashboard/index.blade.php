@extends('dashboard.layouts.admin')

@section('title', 'Dashboard Utama')

@push('styles')
    <style>
        /* ── VARIABEL TEMA ─────────────────────────────────────────── */
        :root {
            --pet-primary: #2E7D32;
            --pet-secondary: #66BB6A;
            --pet-accent: #F9A825;
            --pet-danger: #E53935;
            --pet-info: #0288D1;
            --pet-purple: #6A1B9A;
            --pet-dark: #1B2631;
            --pet-light-bg: #F4F6F9;
            --card-radius: 14px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, .08);
            --shadow-hover: 0 8px 30px rgba(0, 0, 0, .14);
        }

        body {
            background: var(--pet-light-bg);
        }

        /* ── WELCOME BANNER ────────────────────────────────────────── */
        .welcome-banner {
            background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 50%, #388E3C 100%);
            border-radius: var(--card-radius);
            padding: 28px 32px;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 28px;
            box-shadow: 0 6px 24px rgba(46, 125, 50, .35);
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, .07);
            border-radius: 50%;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -60px;
            right: 80px;
            width: 140px;
            height: 140px;
            background: rgba(249, 168, 37, .12);
            border-radius: 50%;
        }

        .welcome-banner .paw-icon {
            font-size: 56px;
            opacity: .18;
            position: absolute;
            right: 32px;
            top: 50%;
            transform: translateY(-50%);
        }

        .welcome-banner h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .welcome-banner p {
            font-size: .92rem;
            opacity: .85;
            margin: 0;
        }

        .welcome-banner .badge-time {
            display: inline-block;
            background: rgba(255, 255, 255, .18);
            border-radius: 20px;
            padding: 3px 12px;
            font-size: .78rem;
            margin-top: 10px;
            backdrop-filter: blur(4px);
        }

        /* ── SECTION TITLE ─────────────────────────────────────────── */
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--pet-dark);
            margin: 28px 0 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8ecef;
        }

        .section-title .icon-badge {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            color: #fff;
            flex-shrink: 0;
        }

        /* ── STAT CARD ─────────────────────────────────────────────── */
        .stat-card {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 22px 24px;
            box-shadow: var(--shadow-soft);
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #fff;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            color: var(--pet-dark);
            margin-bottom: 4px;
        }

        .stat-card .stat-label {
            font-size: .8rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 600;
        }

        .stat-card .stat-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .78rem;
            font-weight: 600;
            margin-top: 12px;
            text-decoration: none;
            opacity: .75;
            transition: opacity .15s;
        }

        .stat-card .stat-link:hover {
            opacity: 1;
            text-decoration: none;
        }

        /* Warna border kiri per card */
        .stat-card.green {
            border-color: var(--pet-primary);
        }

        .stat-card.yellow {
            border-color: var(--pet-accent);
        }

        .stat-card.blue {
            border-color: var(--pet-info);
        }

        .stat-card.red {
            border-color: var(--pet-danger);
        }

        .stat-card.purple {
            border-color: var(--pet-purple);
        }

        .stat-card.teal {
            border-color: #00897B;
        }

        .bg-pet-green {
            background: linear-gradient(135deg, #2E7D32, #43A047);
        }

        .bg-pet-yellow {
            background: linear-gradient(135deg, #F9A825, #FDD835);
        }

        .bg-pet-blue {
            background: linear-gradient(135deg, #0288D1, #29B6F6);
        }

        .bg-pet-red {
            background: linear-gradient(135deg, #E53935, #EF5350);
        }

        .bg-pet-purple {
            background: linear-gradient(135deg, #6A1B9A, #AB47BC);
        }

        .bg-pet-teal {
            background: linear-gradient(135deg, #00695C, #00897B);
        }

        /* ── DATA CARD (tabel & list) ──────────────────────────────── */
        .data-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            height: 100%;
        }

        .data-card .data-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .data-card .data-card-header h6 {
            font-size: .88rem;
            font-weight: 700;
            color: var(--pet-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .data-card .data-card-body {
            padding: 0;
        }

        .data-card .data-card-body.padded {
            padding: 16px 20px;
        }

        /* ── TABLE CUSTOM ──────────────────────────────────────────── */
        .pet-table {
            width: 100%;
            font-size: .82rem;
        }

        .pet-table thead th {
            background: #f7f9fc;
            color: #666;
            font-weight: 700;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 10px 14px;
            border: none;
            white-space: nowrap;
        }

        .pet-table tbody td {
            padding: 11px 14px;
            vertical-align: middle;
            border-top: 1px solid #f3f3f3;
            color: var(--pet-dark);
        }

        .pet-table tbody tr:hover {
            background: #fafbff;
        }

        /* ── AVATAR ────────────────────────────────────────────────── */
        .avatar-sm {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e8ecef;
            flex-shrink: 0;
        }

        .avatar-initial {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .78rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        /* ── STATUS BADGE ──────────────────────────────────────────── */
        .pet-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .pet-badge.completed,
        .pet-badge.received {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .pet-badge.pending {
            background: #FFF8E1;
            color: #F57F17;
        }

        .pet-badge.cancelled,
        .pet-badge.failed {
            background: #FFEBEE;
            color: #C62828;
        }

        .pet-badge.paid {
            background: #E3F2FD;
            color: #1565C0;
        }

        /* ── RANK ITEM (Top Product / Kasir / Supplier) ────────────── */
        .rank-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            border-bottom: 1px solid #f3f3f3;
            transition: background .15s;
        }

        .rank-item:last-child {
            border-bottom: none;
        }

        .rank-item:hover {
            background: #fafbff;
        }

        .rank-num {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: var(--pet-light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            font-weight: 800;
            color: #666;
            flex-shrink: 0;
        }

        .rank-num.top1 {
            background: #F9A825;
            color: #fff;
        }

        .rank-num.top2 {
            background: #9E9E9E;
            color: #fff;
        }

        .rank-num.top3 {
            background: #8D6E63;
            color: #fff;
        }

        .rank-info {
            flex: 1;
            min-width: 0;
        }

        .rank-info .rank-name {
            font-size: .83rem;
            font-weight: 600;
            color: var(--pet-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rank-info .rank-sub {
            font-size: .75rem;
            color: #999;
        }

        .rank-value {
            font-size: .82rem;
            font-weight: 700;
            color: var(--pet-primary);
            white-space: nowrap;
        }

        /* ── LOW STOCK ITEM ────────────────────────────────────────── */
        .stock-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 20px;
            border-bottom: 1px solid #f3f3f3;
        }

        .stock-item:last-child {
            border-bottom: none;
        }

        .stock-bar-wrap {
            flex: 1;
            max-width: 80px;
        }

        .stock-bar {
            height: 6px;
            border-radius: 3px;
            background: #eee;
            overflow: hidden;
        }

        .stock-bar-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--pet-danger);
        }

        .stock-num {
            font-size: .8rem;
            font-weight: 700;
            color: var(--pet-danger);
            min-width: 30px;
            text-align: right;
        }

        /* ── CHART CARD ────────────────────────────────────────────── */
        .chart-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            padding: 20px;
            height: 100%;
        }

        .chart-card .chart-title {
            font-size: .88rem;
            font-weight: 700;
            color: var(--pet-dark);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-card canvas {
            max-height: 240px;
        }

        /* ── EMPTY STATE ───────────────────────────────────────────── */
        .empty-state {
            padding: 30px 20px;
            text-align: center;
            color: #bbb;
            font-size: .85rem;
        }

        .empty-state i {
            font-size: 2rem;
            display: block;
            margin-bottom: 8px;
        }

        /* ── RESPONSIVE ────────────────────────────────────────────── */
        @media (max-width: 767px) {
            .welcome-banner {
                padding: 20px;
            }

            .welcome-banner h2 {
                font-size: 1.2rem;
            }

            .welcome-banner .paw-icon {
                display: none;
            }

            .stat-card .stat-value {
                font-size: 1.6rem;
            }

            .pet-table thead th,
            .pet-table tbody td {
                padding: 8px 10px;
            }
        }
    </style>
@endpush

@section('content')

    {{-- ── WELCOME BANNER ────────────────────────────────────────── --}}
    <div class="welcome-banner">
        <div class="paw-icon"><i class="fas fa-paw"></i></div>
        <h2>
            Selamat Datang, {{ Auth::user()->name }}! 🐾
        </h2>
        <p>Berikut ringkasan aktivitas <strong>Anda Petshop</strong> hari ini.</p>
        <span class="badge-time">
            <i class="fas fa-clock mr-1"></i>
            {{ now()->translatedFormat('l, d F Y — H:i') }} WIB
        </span>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1: PENGGUNA & AKSES                                   --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="section-title">
        <div class="icon-badge bg-pet-purple"><i class="fas fa-users-cog"></i></div>
        Pengguna & Kontrol Akses
    </div>

    <div class="row mb-2">
        {{-- Total Pengguna --}}
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="stat-card purple">
                <div>
                    <div class="stat-icon bg-pet-purple"><i class="fas fa-users"></i></div>
                    <div class="stat-value">{{ $totalUsers }}</div>
                    <div class="stat-label">Total Pengguna</div>
                </div>
                <a href="{{ route('dashboard.users.index') }}" class="stat-link text-purple-700">
                    <i class="fas fa-arrow-right"></i> Kelola Pengguna
                </a>
            </div>
        </div>

        {{-- Total Role --}}
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="stat-card blue">
                <div>
                    <div class="stat-icon bg-pet-blue"><i class="fas fa-shield-alt"></i></div>
                    <div class="stat-value">{{ $totalRoles }}</div>
                    <div class="stat-label">Total Role</div>
                </div>
                <a href="{{ route('dashboard.roles.index') }}" class="stat-link text-info">
                    <i class="fas fa-arrow-right"></i> Kelola Role
                </a>
            </div>
        </div>

        {{-- Total Permission --}}
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="stat-card teal">
                <div>
                    <div class="stat-icon bg-pet-teal"><i class="fas fa-key"></i></div>
                    <div class="stat-value">{{ $totalPermissions }}</div>
                    <div class="stat-label">Total Permission</div>
                </div>
                <a href="{{ route('dashboard.permissions.index') }}" class="stat-link text-teal">
                    <i class="fas fa-arrow-right"></i> Kelola Permission
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2: INVENTORI                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="section-title">
        <div class="icon-badge bg-pet-green"><i class="fas fa-boxes"></i></div>
        Informasi Inventori
    </div>

    <div class="row mb-2">
        {{-- Total Produk --}}
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="stat-card green">
                <div>
                    <div class="stat-icon bg-pet-green"><i class="fas fa-box-open"></i></div>
                    <div class="stat-value">{{ $totalProducts }}</div>
                    <div class="stat-label">Total Produk</div>
                </div>
                <a href="{{ route('dashboard.products.index') }}" class="stat-link text-success">
                    <i class="fas fa-arrow-right"></i> Kelola Produk
                </a>
            </div>
        </div>

        {{-- Total Spesies --}}
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="stat-card yellow">
                <div>
                    <div class="stat-icon bg-pet-yellow"><i class="fas fa-paw"></i></div>
                    <div class="stat-value">{{ $totalSpecies }}</div>
                    <div class="stat-label">Total Spesies</div>
                </div>
                <a href="{{ route('dashboard.categories.index') }}" class="stat-link text-warning">
                    <i class="fas fa-arrow-right"></i> Kelola Spesies
                </a>
            </div>
        </div>

        {{-- Total Sub Kategori --}}
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="stat-card blue">
                <div>
                    <div class="stat-icon bg-pet-blue"><i class="fas fa-tags"></i></div>
                    <div class="stat-value">{{ $totalCategories }}</div>
                    <div class="stat-label">Total Kategori</div>
                </div>
                <a href="{{ route('dashboard.categories.index') }}" class="stat-link text-info">
                    <i class="fas fa-arrow-right"></i> Kelola Kategori
                </a>
            </div>
        </div>

        {{-- Total Supplier --}}
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="stat-card red">
                <div>
                    <div class="stat-icon bg-pet-red"><i class="fas fa-truck"></i></div>
                    <div class="stat-value">{{ $totalSuppliers }}</div>
                    <div class="stat-label">Total Supplier</div>
                </div>
                <a href="{{ route('dashboard.suppliers.index') }}" class="stat-link text-danger">
                    <i class="fas fa-arrow-right"></i> Kelola Supplier
                </a>
            </div>
        </div>
    </div>

    {{-- Chart Inventori: Stok per Spesies --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-bar text-success"></i>
                    Total Stok per Spesies
                </div>
                <canvas id="chartStockByCategory"></canvas>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3: PENJUALAN                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="section-title">
        <div class="icon-badge bg-pet-green"><i class="fas fa-cash-register"></i></div>
        Informasi Penjualan
    </div>

    {{-- Chart Penjualan: Line + Pie --}}
    <div class="row mb-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="chart-card h-100">
                <div class="chart-title">
                    <i class="fas fa-chart-line text-primary"></i>
                    Tren Penjualan 30 Hari Terakhir
                </div>
                <canvas id="chartSalesTrend"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <div class="chart-title">
                    <i class="fas fa-chart-pie text-warning"></i>
                    Distribusi Status Order
                </div>
                <canvas id="chartOrderStatus"></canvas>
            </div>
        </div>
    </div>

    {{-- Lima Transaksi Terakhir --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="data-card">
                <div class="data-card-header">
                    <h6><i class="fas fa-receipt text-success"></i> 5 Transaksi Terakhir</h6>
                    <a href="{{ route('dashboard.orders.index') }}" class="btn btn-sm btn-outline-success">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="data-card-body">
                    @if ($latestOrders->isEmpty())
                        <div class="empty-state">
                            <i class="fas fa-receipt"></i> Belum ada transaksi
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="pet-table">
                                <thead>
                                    <tr>
                                        <th>Kasir</th>
                                        <th>No Invoice</th>
                                        <th>Tanggal</th>
                                        <th>Metode</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestOrders as $order)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if ($order->user && $order->user->image && $order->user->image !== 'default-user.jpg')
                                                        <img src="{{ Storage::url('uploads/users/' . $order->user->image) }}"
                                                            alt="{{ $order->user->name }}" class="avatar-sm">
                                                    @else
                                                        <div class="avatar-initial bg-pet-green">
                                                            {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <span style="font-size:.82rem; font-weight:600;">
                                                        {{ $order->user->name ?? '-' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <code
                                                    style="font-size:.78rem; background:#f5f5f5; padding:2px 6px; border-radius:4px;">
                                                    {{ $order->invoice_number }}
                                                </code>
                                            </td>
                                            <td style="white-space:nowrap;">
                                                {{ $order->created_at->format('d M Y') }}<br>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if ($order->payment)
                                                    <i
                                                        class="fas {{ $order->payment->payment_method === 'cash' ? 'fa-money-bill-wave text-success' : 'fa-university text-info' }} mr-1"></i>
                                                    {{ ucfirst($order->payment->payment_method) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="font-weight:700;">
                                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <span class="pet-badge {{ $order->status }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Top Produk & Top Kasir --}}
    <div class="row mb-4">
        {{-- Produk Paling Laris --}}
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="data-card h-100">
                <div class="data-card-header">
                    <h6><i class="fas fa-fire text-danger"></i> Produk Terlaris Bulan Ini</h6>
                    <span class="badge badge-light text-muted" style="font-size:.72rem;">
                        {{ now()->translatedFormat('F Y') }}
                    </span>
                </div>
                <div class="data-card-body">
                    @forelse($topProducts as $i => $product)
                        <div class="rank-item">
                            <div
                                class="rank-num {{ $i === 0 ? 'top1' : ($i === 1 ? 'top2' : ($i === 2 ? 'top3' : '')) }}">
                                {{ $i + 1 }}
                            </div>
                            <div class="rank-info">
                                <div class="rank-name">{{ $product->name }}</div>
                                <div class="rank-sub">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</div>
                            </div>
                            <div class="rank-value">
                                {{ $product->total_qty }} terjual
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            Belum ada data penjualan bulan ini
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Kasir Paling Aktif --}}
        <div class="col-lg-6">
            <div class="data-card h-100">
                <div class="data-card-header">
                    <h6><i class="fas fa-star text-warning"></i> Kasir Paling Aktif Bulan Ini</h6>
                    <span class="badge badge-light text-muted" style="font-size:.72rem;">
                        {{ now()->translatedFormat('F Y') }}
                    </span>
                </div>
                <div class="data-card-body">
                    @forelse($topKasirs as $i => $kasir)
                        <div class="rank-item">
                            <div
                                class="rank-num {{ $i === 0 ? 'top1' : ($i === 1 ? 'top2' : ($i === 2 ? 'top3' : '')) }}">
                                {{ $i + 1 }}
                            </div>
                            <div class="avatar-initial bg-pet-green" style="font-size:.7rem;">
                                {{ strtoupper(substr($kasir->name, 0, 1)) }}
                            </div>
                            <div class="rank-info">
                                <div class="rank-name">{{ $kasir->name }}</div>
                                <div class="rank-sub">Rp {{ number_format($kasir->total_revenue, 0, ',', '.') }}</div>
                            </div>
                            <div class="rank-value">
                                {{ $kasir->total_transactions }}x
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            Belum ada data transaksi bulan ini
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 4: PEMBELIAN                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="section-title">
        <div class="icon-badge bg-pet-blue"><i class="fas fa-shopping-cart"></i></div>
        Informasi Pembelian
    </div>

    {{-- Chart Pembelian per Supplier --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-bar text-info"></i>
                    Nilai Pembelian per Supplier (Horizontal Bar)
                </div>
                <canvas id="chartPurchaseBySupplier"></canvas>
            </div>
        </div>
    </div>

    {{-- Lima Pembelian Terakhir --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="data-card">
                <div class="data-card-header">
                    <h6><i class="fas fa-truck text-info"></i> 5 Pembelian Terakhir</h6>
                    <a href="{{ route('dashboard.purchases.index') }}" class="btn btn-sm btn-outline-info">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="data-card-body">
                    @if ($latestPurchases->isEmpty())
                        <div class="empty-state">
                            <i class="fas fa-truck"></i> Belum ada data pembelian
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="pet-table">
                                <thead>
                                    <tr>
                                        <th>No PO</th>
                                        <th>Tanggal</th>
                                        <th>Supplier</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestPurchases as $purchase)
                                        <tr>
                                            <td>
                                                <code
                                                    style="font-size:.78rem; background:#f5f5f5; padding:2px 6px; border-radius:4px;">
                                                    {{ $purchase->purchase_number }}
                                                </code>
                                            </td>
                                            <td style="white-space:nowrap;">
                                                {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                                            </td>
                                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                            <td style="font-weight:700;">
                                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <span class="pet-badge {{ $purchase->status }}">
                                                    {{ ucfirst($purchase->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Stok Menipis & Supplier Terbanyak --}}
    <div class="row mb-5">
        {{-- Stok Menipis --}}
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="data-card h-100">
                <div class="data-card-header">
                    <h6><i class="fas fa-exclamation-triangle text-danger"></i> Stok Produk Menipis</h6>
                    <span class="pet-badge cancelled" style="font-size:.7rem;">
                        Stok ≤ 10
                    </span>
                </div>
                <div class="data-card-body">
                    @forelse($lowStockProducts as $product)
                        <div class="stock-item">
                            <div class="rank-info">
                                <div class="rank-name">{{ $product->name }}</div>
                                <div class="rank-sub">{{ $product->category->name ?? '-' }}</div>
                            </div>
                            <div class="stock-bar-wrap">
                                <div class="stock-bar">
                                    <div class="stock-bar-fill"
                                        style="width: {{ min(100, ($product->stock / 10) * 100) }}%"></div>
                                </div>
                            </div>
                            <div class="stock-num">{{ $product->stock }}</div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-check-circle text-success"></i>
                            Semua stok dalam kondisi aman 🎉
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Supplier Terbanyak Supply --}}
        <div class="col-lg-6">
            <div class="data-card h-100">
                <div class="data-card-header">
                    <h6><i class="fas fa-handshake text-primary"></i> Supplier Terbanyak Supply Bulan Ini</h6>
                </div>
                <div class="data-card-body">
                    @forelse($topSuppliers as $i => $supplier)
                        <div class="rank-item">
                            <div
                                class="rank-num {{ $i === 0 ? 'top1' : ($i === 1 ? 'top2' : ($i === 2 ? 'top3' : '')) }}">
                                {{ $i + 1 }}
                            </div>
                            <div class="rank-info">
                                <div class="rank-name">{{ $supplier->name }}</div>
                                <div class="rank-sub">Rp {{ number_format($supplier->total_value, 0, ',', '.') }}</div>
                            </div>
                            <div class="rank-value">
                                {{ $supplier->total_purchases }}x supply
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-truck"></i>
                            Belum ada data pembelian bulan ini
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // ── GLOBAL CHART DEFAULTS ──────────────────────────────────────
        Chart.defaults.font.family = "'Nunito', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#888';

        const PET_COLORS = {
            green: '#2E7D32',
            lime: '#66BB6A',
            yellow: '#F9A825',
            blue: '#0288D1',
            red: '#E53935',
            purple: '#6A1B9A',
            teal: '#00897B',
            orange: '#F4511E',
        };

        // ── 1. LINE CHART: Tren Penjualan 30 Hari ─────────────────────
        new Chart(document.getElementById('chartSalesTrend'), {
            type: 'line',
            data: {
                labels: @json($salesChartLabels),
                datasets: [{
                        label: 'Jumlah Order',
                        data: @json($salesChartOrders),
                        borderColor: PET_COLORS.green,
                        backgroundColor: 'rgba(46,125,50,.1)',
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        tension: .4,
                        fill: true,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Revenue (Rp)',
                        data: @json($salesChartRevenue),
                        borderColor: PET_COLORS.yellow,
                        backgroundColor: 'rgba(249,168,37,.08)',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        tension: .4,
                        fill: true,
                        yAxisID: 'y1',
                        borderDash: [4, 3],
                    },
                ],
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                if (ctx.datasetIndex === 1) {
                                    return ' Rp ' + ctx.raw.toLocaleString('id-ID');
                                }
                                return ' ' + ctx.raw + ' order';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Jumlah Order'
                        }
                    },
                    y1: {
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Revenue (Rp)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    x: {
                        ticks: {
                            maxTicksLimit: 10
                        }
                    },
                },
            },
        });

        // ── 2. PIE CHART: Distribusi Status Order ─────────────────────
        const orderStatusData = @json($orderStatusData);
        new Chart(document.getElementById('chartOrderStatus'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(orderStatusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                datasets: [{
                    data: Object.values(orderStatusData),
                    backgroundColor: [PET_COLORS.green, PET_COLORS.yellow, PET_COLORS.red],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.raw} order`
                        }
                    }
                },
            },
        });

        // ── 3. BAR CHART: Stok per Spesies ────────────────────────────
        const stockData = @json($stockByCategory);
        new Chart(document.getElementById('chartStockByCategory'), {
            type: 'bar',
            data: {
                labels: stockData.map(d => d.category_name),
                datasets: [{
                    label: 'Total Stok',
                    data: stockData.map(d => d.total_stock),
                    backgroundColor: [
                        'rgba(46,125,50,.8)',
                        'rgba(2,136,209,.8)',
                        'rgba(249,168,37,.8)',
                        'rgba(229,57,53,.8)',
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` Stok: ${ctx.raw} unit`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Stok (unit)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    },
                },
            },
        });

        // ── 4. HORIZONTAL BAR: Pembelian per Supplier ─────────────────
        const supplierData = @json($purchaseBySupplier);
        new Chart(document.getElementById('chartPurchaseBySupplier'), {
            type: 'bar',
            data: {
                labels: supplierData.map(d => d.name),
                datasets: [{
                    label: 'Total Nilai Pembelian (Rp)',
                    data: supplierData.map(d => d.total_value),
                    backgroundColor: [
                        'rgba(2,136,209,.85)',
                        'rgba(0,137,123,.85)',
                        'rgba(106,27,154,.85)',
                        'rgba(229,57,53,.85)',
                        'rgba(249,168,37,.85)',
                        'rgba(46,125,50,.85)',
                    ],
                    borderRadius: 6,
                    borderSkipped: false,
                }],
            },
            options: {
                indexAxis: 'y', // ← HORIZONTAL
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => 'Rp ' + (val / 1000000).toFixed(1) + 'jt'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    },
                },
            },
        });
    </script>
@endpush
