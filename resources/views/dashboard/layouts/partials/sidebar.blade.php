<style>
    /* ============================================================
       SMART SIDEBAR - Icon-only collapsed, hover expand (overlay),
       Lock mode: expand + push content
    ============================================================ */

    :root {
        --sidebar-collapsed-w: 70px;
        --sidebar-expanded-w: 260px;
        --sidebar-transition: 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        --sidebar-bg: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
        --sidebar-text: rgba(255, 255, 255, 0.75);
        --sidebar-text-hover: #ffffff;
        --sidebar-active-bg: rgba(255, 255, 255, 0.18);
        --sidebar-hover-item-bg: rgba(255, 255, 255, 0.10);
        --topbar-h: 60px;
    }

    /* ── Layout wrapper ── */
    body {
        overflow-x: hidden;
    }

    #sidebar-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: var(--sidebar-collapsed-w);
        z-index: 1050;
        transition: width var(--sidebar-transition);
        background: var(--sidebar-bg);
        box-shadow: 3px 0 20px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Hover expand (overlay mode) */
    #sidebar-wrapper:hover,
    #sidebar-wrapper.sidebar-locked {
        width: var(--sidebar-expanded-w);
    }

    /* ── Content wrapper adjusts when locked ── */
    #content-wrapper {
        margin-left: var(--sidebar-collapsed-w);
        transition: margin-left var(--sidebar-transition);
        min-height: 100vh;
    }

    #content-wrapper.sidebar-pushed {
        margin-left: var(--sidebar-expanded-w);
    }

    /* ── Brand area ── */
    .sidebar-brand-area {
        display: flex;
        align-items: center;
        padding: 0;
        height: var(--topbar-h);
        min-height: var(--topbar-h);
        overflow: hidden;
        text-decoration: none;
        flex-shrink: 0;
        position: relative;
    }

    .sidebar-brand-icon-wrap {
        width: var(--sidebar-collapsed-w);
        min-width: var(--sidebar-collapsed-w);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #fff;
        flex-shrink: 0;
    }

    .sidebar-brand-icon-wrap img {
        height: 36px;
        width: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.4);
    }

    .sidebar-brand-text-wrap {
        opacity: 0;
        width: 0;
        white-space: nowrap;
        overflow: hidden;
        transition: opacity var(--sidebar-transition), width var(--sidebar-transition);
        font-size: 1rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: 0.02em;
        padding-right: 36px;
        /* space for lock btn */
    }

    #sidebar-wrapper:hover .sidebar-brand-text-wrap,
    #sidebar-wrapper.sidebar-locked .sidebar-brand-text-wrap {
        opacity: 1;
        width: calc(var(--sidebar-expanded-w) - var(--sidebar-collapsed-w) - 10px);
    }

    /* ── Lock button ── */
    #sidebar-lock-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: rgba(255, 255, 255, 0.7);
        border-radius: 6px;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.7rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease, background 0.2s ease, color 0.2s ease;
        z-index: 10;
        flex-shrink: 0;
    }

    #sidebar-wrapper:hover #sidebar-lock-btn {
        opacity: 1;
        pointer-events: all;
    }

    #sidebar-lock-btn.locked {
        background: rgba(255, 255, 255, 0.28);
        color: #fff;
        border-color: rgba(255, 255, 255, 0.5);
    }

    /* ── Divider ── */
    .sidebar-divider-line {
        border: 0;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        margin: 4px 12px;
        flex-shrink: 0;
    }

    /* ── Scrollable nav area ── */
    .sidebar-nav-area {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        padding-bottom: 12px;
    }

    .sidebar-nav-area::-webkit-scrollbar {
        width: 3px;
    }

    .sidebar-nav-area::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-nav-area::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
    }

    /* ── Section headings ── */
    .sidebar-section-heading {
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.4);
        padding: 10px 0 4px 0;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        transition: opacity var(--sidebar-transition), text-align var(--sidebar-transition), padding var(--sidebar-transition);
    }

    #sidebar-wrapper:hover .sidebar-section-heading,
    #sidebar-wrapper.sidebar-locked .sidebar-section-heading {
        text-align: left;
        padding: 10px 16px 4px 16px;
    }

    .sidebar-section-heading .heading-short {
        display: inline;
    }

    .sidebar-section-heading .heading-full {
        display: none;
    }

    #sidebar-wrapper:hover .sidebar-section-heading .heading-short,
    #sidebar-wrapper.sidebar-locked .sidebar-section-heading .heading-short {
        display: none;
    }

    #sidebar-wrapper:hover .sidebar-section-heading .heading-full,
    #sidebar-wrapper.sidebar-locked .sidebar-section-heading .heading-full {
        display: inline;
    }

    /* ── Nav items ── */
    .sidebar-nav-item {
        list-style: none;
        margin: 1px 0;
    }

    .sidebar-nav-link {
        display: flex;
        align-items: center;
        height: 44px;
        padding: 0;
        color: var(--sidebar-text) !important;
        text-decoration: none !important;
        border-radius: 0;
        transition: background 0.18s ease, color 0.18s ease;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        border-left: 3px solid transparent;
    }

    .sidebar-nav-link:hover {
        background: var(--sidebar-hover-item-bg);
        color: var(--sidebar-text-hover) !important;
    }

    .sidebar-nav-link.active {
        background: var(--sidebar-active-bg);
        color: var(--sidebar-text-hover) !important;
        border-left-color: rgba(255, 255, 255, 0.7);
    }

    .sidebar-nav-icon {
        width: var(--sidebar-collapsed-w);
        min-width: var(--sidebar-collapsed-w);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .sidebar-nav-label {
        flex: 1;
        font-size: 0.845rem;
        font-weight: 500;
        opacity: 0;
        transition: opacity var(--sidebar-transition);
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-nav-arrow {
        width: 24px;
        min-width: 24px;
        margin-right: 12px;
        font-size: 0.65rem;
        opacity: 0;
        transition: opacity var(--sidebar-transition), transform 0.2s ease;
        color: rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #sidebar-wrapper:hover .sidebar-nav-label,
    #sidebar-wrapper.sidebar-locked .sidebar-nav-label {
        opacity: 1;
    }

    #sidebar-wrapper:hover .sidebar-nav-arrow,
    #sidebar-wrapper.sidebar-locked .sidebar-nav-arrow {
        opacity: 1;
    }

    .sidebar-nav-arrow.open {
        transform: rotate(90deg);
    }

    /* ── Submenu (collapse inner) ── */
    .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(0, 0, 0, 0.12);
    }

    .sidebar-submenu.open {
        max-height: 400px;
    }

    .sidebar-submenu-item {
        display: flex;
        align-items: center;
        padding: 9px 12px 9px 0;
        color: rgba(255, 255, 255, 0.7) !important;
        text-decoration: none !important;
        font-size: 0.82rem;
        transition: background 0.15s ease, color 0.15s ease;
        white-space: nowrap;
        overflow: hidden;
        border-left: 3px solid transparent;
    }

    .sidebar-submenu-item:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff !important;
    }

    .sidebar-submenu-item.active {
        background: rgba(255, 255, 255, 0.12);
        color: #fff !important;
        font-weight: 600;
        border-left-color: rgba(255, 255, 255, 0.5);
    }

    .sidebar-submenu-icon {
        width: var(--sidebar-collapsed-w);
        min-width: var(--sidebar-collapsed-w);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.78rem;
        color: rgba(255, 255, 255, 0.5);
        flex-shrink: 0;
    }

    .sidebar-submenu-label {
        flex: 1;
        opacity: 0;
        transition: opacity var(--sidebar-transition);
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #sidebar-wrapper:hover .sidebar-submenu-label,
    #sidebar-wrapper.sidebar-locked .sidebar-submenu-label {
        opacity: 1;
    }

    /* When sidebar is collapsed (not hovered, not locked),
       submenu should be hidden even if open state is stored */
    #sidebar-wrapper:not(:hover):not(.sidebar-locked) .sidebar-submenu {
        max-height: 0 !important;
    }

    /* ── Sidebar bottom toggle (collapse button for mobile / desktop) ── */
    .sidebar-bottom-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 0;
        flex-shrink: 0;
    }

    #sidebarToggleCircle {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    #sidebarToggleCircle:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    /* ── Topbar adjustment ── */
    .topbar-nav {
        margin-left: var(--sidebar-collapsed-w);
        transition: margin-left var(--sidebar-transition);
    }

    .topbar-nav.sidebar-pushed {
        margin-left: var(--sidebar-expanded-w);
    }

    /* ── Mobile: hide sidebar default, overlay on toggle ── */
    @media (max-width: 768px) {
        #sidebar-wrapper {
            width: 0;
            overflow: hidden;
        }

        #sidebar-wrapper.mobile-open {
            width: var(--sidebar-expanded-w);
        }

        #content-wrapper,
        .topbar-nav {
            margin-left: 0 !important;
        }

        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1040;
        }

        #sidebar-overlay.active {
            display: block;
        }
    }
</style>

{{-- ── MOBILE OVERLAY ── --}}
<div id="sidebar-overlay"></div>

{{-- ═══════════════════════════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════════════════════════ --}}
<div id="sidebar-wrapper">

    @php
        $appImage = \App\Models\SettingApp::get('app_image');
        $appName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
        $hasImage = $appImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($appImage);
    @endphp

    {{-- Brand --}}
    <a class="sidebar-brand-area" href="{{ route('dashboard.index') }}">
        <div class="sidebar-brand-icon-wrap">
            @if ($hasImage)
                <img src="{{ Storage::url($appImage) }}" alt="{{ $appName }}">
            @else
                <i class="fas fa-cat"></i>
            @endif
        </div>
        <span class="sidebar-brand-text-wrap">{{ $appName }}</span>
        {{-- Lock button --}}
        <button id="sidebar-lock-btn" title="Lock Sidebar">
            <i class="fas fa-thumbtack" id="lock-icon"></i>
        </button>
    </a>

    <hr class="sidebar-divider-line">

    {{-- ── Scrollable nav ── --}}
    <nav class="sidebar-nav-area">
        <ul style="list-style:none; margin:0; padding:0;">

            {{-- Dashboard --}}
            <li class="sidebar-nav-item">
                <a class="sidebar-nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.index') }}">
                    <span class="sidebar-nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span class="sidebar-nav-label">Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider-line">

            {{-- ── OPERASIONAL ── --}}
            <li>
                <div class="sidebar-section-heading">
                    <span class="heading-short">—</span>
                    <span class="heading-full">Operasional</span>
                </div>
            </li>

            @can('order.pos')
                <li class="sidebar-nav-item">
                    <a class="sidebar-nav-link {{ request()->routeIs('dashboard.orders.pos') ? 'active' : '' }}"
                        href="{{ route('dashboard.orders.pos') }}">
                        <span class="sidebar-nav-icon"><i class="fas fa-cash-register"></i></span>
                        <span class="sidebar-nav-label">Point of Sales (POS)</span>
                    </a>
                </li>
            @endcan

            @canany(['order.history', 'order.confirm'])
                @php $penjualanActive = request()->routeIs('dashboard.orders.index*','dashboard.orders.confirmation*'); @endphp
                <li class="sidebar-nav-item" data-submenu="penjualan">
                    <div class="sidebar-nav-link {{ $penjualanActive ? 'active' : '' }}"
                        onclick="toggleSubmenu('penjualan', this)">
                        <span class="sidebar-nav-icon"><i class="fas fa-bag-shopping"></i></span>
                        <span class="sidebar-nav-label">Penjualan</span>
                        <span class="sidebar-nav-arrow {{ $penjualanActive ? 'open' : '' }}"><i
                                class="fas fa-chevron-right"></i></span>
                    </div>
                    <div class="sidebar-submenu {{ $penjualanActive ? 'open' : '' }}" id="submenu-penjualan">
                        @can('order.history')
                            <a class="sidebar-submenu-item {{ request()->routeIs('dashboard.orders.index*') ? 'active' : '' }}"
                                href="{{ route('dashboard.orders.index') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-history"></i></span>
                                <span class="sidebar-submenu-label">Riwayat Pesanan</span>
                            </a>
                        @endcan
                        @can('order.confirm')
                            <a class="sidebar-submenu-item {{ request()->routeIs('dashboard.orders.confirmation*') ? 'active' : '' }}"
                                href="{{ route('dashboard.orders.confirmation') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-check-circle"></i></span>
                                <span class="sidebar-submenu-label">Konfirmasi Bayar</span>
                            </a>
                        @endcan
                    </div>
                </li>
            @endcanany

            {{-- Pembelian --}}
            @php $pembelianActive = request()->routeIs('dashboard.purchases.index*','dashboard.purchases.confirmation*'); @endphp
            <li class="sidebar-nav-item" data-submenu="pembelian">
                <div class="sidebar-nav-link {{ $pembelianActive ? 'active' : '' }}"
                    onclick="toggleSubmenu('pembelian', this)">
                    <span class="sidebar-nav-icon"><i class="fas fa-cart-plus"></i></span>
                    <span class="sidebar-nav-label">Pembelian</span>
                    <span class="sidebar-nav-arrow {{ $pembelianActive ? 'open' : '' }}"><i
                            class="fas fa-chevron-right"></i></span>
                </div>
                <div class="sidebar-submenu {{ $pembelianActive ? 'open' : '' }}" id="submenu-pembelian">
                    <a class="sidebar-submenu-item {{ request()->routeIs('dashboard.purchases.index*') ? 'active' : '' }}"
                        href="{{ route('dashboard.purchases.index') }}">
                        <span class="sidebar-submenu-icon"><i class="fas fa-history"></i></span>
                        <span class="sidebar-submenu-label">Riwayat Pembelian</span>
                    </a>
                    <a class="sidebar-submenu-item {{ request()->routeIs('dashboard.purchases.confirmation*') ? 'active' : '' }}"
                        href="{{ route('dashboard.purchases.confirmation') }}">
                        <span class="sidebar-submenu-icon"><i class="fas fa-check-circle"></i></span>
                        <span class="sidebar-submenu-label">Konfirmasi Beli</span>
                    </a>
                </div>
            </li>

            <hr class="sidebar-divider-line">

            {{-- ── MANAJEMEN STOK ── --}}
            @canany(['category.index', 'product.index', 'supplier.index'])
                <li>
                    <div class="sidebar-section-heading">
                        <span class="heading-short">—</span>
                        <span class="heading-full">Manajemen Stok</span>
                    </div>
                </li>
                @php $inventoriActive = request()->is('dashboard/categories*','dashboard/products*','dashboard/suppliers*'); @endphp
                <li class="sidebar-nav-item" data-submenu="inventori">
                    <div class="sidebar-nav-link {{ $inventoriActive ? 'active' : '' }}"
                        onclick="toggleSubmenu('inventori', this)">
                        <span class="sidebar-nav-icon"><i class="fas fa-box"></i></span>
                        <span class="sidebar-nav-label">Inventori</span>
                        <span class="sidebar-nav-arrow {{ $inventoriActive ? 'open' : '' }}"><i
                                class="fas fa-chevron-right"></i></span>
                    </div>
                    <div class="sidebar-submenu {{ $inventoriActive ? 'open' : '' }}" id="submenu-inventori">
                        @can('product.index')
                            <a class="sidebar-submenu-item {{ request()->is('dashboard/products*') ? 'active' : '' }}"
                                href="{{ route('dashboard.products.index') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-boxes-stacked"></i></span>
                                <span class="sidebar-submenu-label">Daftar Produk</span>
                            </a>
                        @endcan
                        @can('category.index')
                            <a class="sidebar-submenu-item {{ request()->is('dashboard/categories*') ? 'active' : '' }}"
                                href="{{ route('dashboard.categories.index') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-layer-group"></i></span>
                                <span class="sidebar-submenu-label">Kategori</span>
                            </a>
                        @endcan
                        @can('supplier.index')
                            <a class="sidebar-submenu-item {{ request()->is('dashboard/suppliers*') ? 'active' : '' }}"
                                href="{{ route('dashboard.suppliers.index') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-truck-field"></i></span>
                                <span class="sidebar-submenu-label">Supplier</span>
                            </a>
                        @endcan
                    </div>
                </li>
                <hr class="sidebar-divider-line">
            @endcanany

            {{-- ── LAPORAN ── --}}
            @canany(['report.hourly', 'report.daily', 'report.monthly'])
                <li>
                    <div class="sidebar-section-heading">
                        <span class="heading-short">—</span>
                        <span class="heading-full">Laporan</span>
                    </div>
                </li>
                @php $laporanActive = request()->is('dashboard/reports*'); @endphp
                <li class="sidebar-nav-item" data-submenu="laporan">
                    <div class="sidebar-nav-link {{ $laporanActive ? 'active' : '' }}"
                        onclick="toggleSubmenu('laporan', this)">
                        <span class="sidebar-nav-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="sidebar-nav-label">Analisis Penjualan</span>
                        <span class="sidebar-nav-arrow {{ $laporanActive ? 'open' : '' }}"><i
                                class="fas fa-chevron-right"></i></span>
                    </div>
                    <div class="sidebar-submenu {{ $laporanActive ? 'open' : '' }}" id="submenu-laporan">
                        @can('report.hourly')
                            <a class="sidebar-submenu-item {{ request()->is('dashboard/reports/hourly') ? 'active' : '' }}"
                                href="{{ route('dashboard.reports.hourly') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-clock"></i></span>
                                <span class="sidebar-submenu-label">Per Jam</span>
                            </a>
                        @endcan
                        @can('report.daily')
                            <a class="sidebar-submenu-item {{ request()->is('dashboard/reports/daily') ? 'active' : '' }}"
                                href="{{ route('dashboard.reports.daily') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-calendar-days"></i></span>
                                <span class="sidebar-submenu-label">Harian</span>
                            </a>
                        @endcan
                        @can('report.monthly')
                            <a class="sidebar-submenu-item {{ request()->is('dashboard/reports/monthly') ? 'active' : '' }}"
                                href="{{ route('dashboard.reports.monthly') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-calendar-week"></i></span>
                                <span class="sidebar-submenu-label">Bulanan</span>
                            </a>
                        @endcan
                    </div>
                </li>
                <hr class="sidebar-divider-line">
            @endcanany

            {{-- ── ADMINISTRATOR ── --}}
            @canany(['user.index', 'role.index', 'permission.index'])
                <li>
                    <div class="sidebar-section-heading">
                        <span class="heading-short">—</span>
                        <span class="heading-full">Administrator</span>
                    </div>
                </li>
                @php $akseActive = request()->is('dashboard/users*','dashboard/roles*','dashboard/permissions*'); @endphp
                <li class="sidebar-nav-item" data-submenu="akses">
                    <div class="sidebar-nav-link {{ $akseActive ? 'active' : '' }}"
                        onclick="toggleSubmenu('akses', this)">
                        <span class="sidebar-nav-icon"><i class="fas fa-user-shield"></i></span>
                        <span class="sidebar-nav-label">Kontrol Akses</span>
                        <span class="sidebar-nav-arrow {{ $akseActive ? 'open' : '' }}"><i
                                class="fas fa-chevron-right"></i></span>
                    </div>
                    <div class="sidebar-submenu {{ $akseActive ? 'open' : '' }}" id="submenu-akses">
                        @can('user.index')
                            <a class="sidebar-submenu-item {{ request()->is('dashboard/users*') ? 'active' : '' }}"
                                href="{{ route('dashboard.users.index') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-users-gear"></i></span>
                                <span class="sidebar-submenu-label">Pengguna</span>
                            </a>
                        @endcan
                        @can('role.index')
                            <a class="sidebar-submenu-item {{ request()->is('dashboard/roles*') ? 'active' : '' }}"
                                href="{{ route('dashboard.roles.index') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-address-card"></i></span>
                                <span class="sidebar-submenu-label">Peran (Role)</span>
                            </a>
                        @endcan
                        @can('permission.index')
                            <a class="sidebar-submenu-item {{ request()->is('dashboard/permissions*') ? 'active' : '' }}"
                                href="{{ route('dashboard.permissions.index') }}">
                                <span class="sidebar-submenu-icon"><i class="fas fa-key"></i></span>
                                <span class="sidebar-submenu-label">Hak Akses</span>
                            </a>
                        @endcan
                    </div>
                </li>
                <hr class="sidebar-divider-line">
            @endcanany

            {{-- ── SISTEM ── --}}
            <li>
                <div class="sidebar-section-heading">
                    <span class="heading-short">—</span>
                    <span class="heading-full">Sistem</span>
                </div>
            </li>
            <li class="sidebar-nav-item">
                <a class="sidebar-nav-link {{ request()->is('dashboard/settings*') ? 'active' : '' }}"
                    href="{{ route('dashboard.settings.index') }}">
                    <span class="sidebar-nav-icon"><i class="fas fa-cog"></i></span>
                    <span class="sidebar-nav-label">Pengaturan Aplikasi</span>
                </a>
            </li>

        </ul>
    </nav>

    {{-- Bottom toggle circle --}}
    <div class="sidebar-bottom-toggle d-none d-md-flex">
        <button id="sidebarToggleCircle" title="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════════════ --}}
<script>
    (function() {
        'use strict';

        const sidebar = document.getElementById('sidebar-wrapper');
        const lockBtn = document.getElementById('sidebar-lock-btn');
        const lockIcon = document.getElementById('lock-icon');
        const contentWrapper = document.getElementById('content-wrapper');
        const topbarNav = document.querySelector('.topbar-nav');
        const overlay = document.getElementById('sidebar-overlay');

        // ── Restore lock state from localStorage ──
        let isLocked = localStorage.getItem('sidebarLocked') === 'true';

        function applyLockState(locked, animate) {
            isLocked = locked;
            if (locked) {
                sidebar.classList.add('sidebar-locked');
                lockBtn.classList.add('locked');
                lockIcon.className = 'fas fa-thumbtack';
                if (contentWrapper) contentWrapper.classList.add('sidebar-pushed');
                if (topbarNav) topbarNav.classList.add('sidebar-pushed');
            } else {
                sidebar.classList.remove('sidebar-locked');
                lockBtn.classList.remove('locked');
                lockIcon.className = 'fas fa-thumbtack';
                if (contentWrapper) contentWrapper.classList.remove('sidebar-pushed');
                if (topbarNav) topbarNav.classList.remove('sidebar-pushed');
            }
            localStorage.setItem('sidebarLocked', locked);
        }

        applyLockState(isLocked, false);

        lockBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            applyLockState(!isLocked, true);
        });

        // ── Submenu toggle ──
        window.toggleSubmenu = function(name, triggerEl) {
            // Only works when sidebar is expanded (hovered or locked)
            const isExpanded = sidebar.matches(':hover') || sidebar.classList.contains('sidebar-locked');
            if (!isExpanded) return;

            const submenu = document.getElementById('submenu-' + name);
            const arrow = triggerEl.querySelector('.sidebar-nav-arrow');

            const isOpen = submenu.classList.contains('open');
            submenu.classList.toggle('open', !isOpen);
            if (arrow) arrow.classList.toggle('open', !isOpen);
        };

        // ── Mobile toggle (topbar burger button) ──
        const topbarToggle = document.getElementById('sidebarToggleTop');
        if (topbarToggle) {
            topbarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            });
        }

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });

        // ── Desktop bottom toggle (optional, only visual hint) ──
        const circleBtn = document.getElementById('sidebarToggleCircle');
        if (circleBtn) {
            circleBtn.addEventListener('click', function() {
                applyLockState(!isLocked, true);
            });
        }
    })();
</script>
