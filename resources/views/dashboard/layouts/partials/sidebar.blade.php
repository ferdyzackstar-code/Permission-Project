<style>
    /* ============================================================
       SMART SIDEBAR v2 - Fixed: narrower collapse, mobile toggle,
       topbar alignment, locked push content
    ============================================================ */

    :root {
        --sb-w-col: 54px;
        --sb-w-exp: 240px;
        --sb-ease: 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        --topbar-h: 56px;
    }

    /* ── Prevent sb-admin-2 overrides ── */
    body {
        overflow-x: hidden !important;
    }

    #wrapper {
        display: flex !important;
        overflow: hidden !important;
    }

    /* ══ SIDEBAR ══ */
    #sidebar-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: var(--sb-w-col);
        z-index: 1050;
        background: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
        box-shadow: 2px 0 18px rgba(0, 0, 0, 0.16);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: width var(--sb-ease);
        will-change: width;
    }

    /* Hover → overlay expand (desktop only) */
    @media (min-width: 768px) {
        #sidebar-wrapper:hover {
            width: var(--sb-w-exp);
        }
    }

    /* Locked → push content */
    #sidebar-wrapper.sb-locked {
        width: var(--sb-w-exp);
    }

    /* ══ CONTENT ══ */
    #content-wrapper {
        margin-left: var(--sb-w-col) !important;
        transition: margin-left var(--sb-ease);
        min-width: 0;
        flex: 1;
    }

    #content-wrapper.sb-pushed {
        margin-left: var(--sb-w-exp) !important;
    }

    /* ══ TOPBAR ══
       Lives inside #content-wrapper → inherits width automatically.
       Only need sticky + proper height.
    ══ */
    .topbar-main {
        position: sticky;
        top: 0;
        z-index: 1040;
        height: var(--topbar-h);
        min-height: var(--topbar-h);
        background: #fff;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.07);
        display: flex;
        align-items: center;
        padding: 0 1.25rem;
        width: 100%;
        /* kill sb-admin mb-4 */
        margin-bottom: 0 !important;
    }

    /* ══ BRAND ══ */
    .sb-brand {
        display: flex;
        align-items: center;
        height: var(--topbar-h);
        min-height: var(--topbar-h);
        text-decoration: none !important;
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
    }

    .sb-brand-icon {
        width: var(--sb-w-col);
        min-width: var(--sb-w-col);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
        flex-shrink: 0;
    }

    .sb-brand-icon img {
        height: 30px;
        width: 30px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.38);
    }

    .sb-brand-text {
        opacity: 0;
        max-width: 0;
        white-space: nowrap;
        overflow: hidden;
        font-size: 0.92rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: 0.02em;
        padding-right: 30px;
        /* room for lock btn */
        transition: opacity var(--sb-ease), max-width var(--sb-ease);
    }

    #sidebar-wrapper:hover .sb-brand-text,
    #sidebar-wrapper.sb-locked .sb-brand-text {
        opacity: 1;
        max-width: 200px;
    }

    /* ══ LOCK BTN ══ */
    #sb-lock-btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.20);
        color: rgba(255, 255, 255, 0.60);
        border-radius: 5px;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.60rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.18s, background 0.18s, color 0.18s;
        z-index: 6;
    }

    #sidebar-wrapper:hover #sb-lock-btn {
        opacity: 1;
        pointer-events: all;
    }

    #sidebar-wrapper.sb-locked #sb-lock-btn {
        opacity: 1;
        pointer-events: all;
        background: rgba(255, 255, 255, 0.20);
        color: #fff;
    }

    /* ══ DIVIDER ══ */
    .sb-div {
        border: 0;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        margin: 3px 8px;
        flex-shrink: 0;
    }

    /* ══ SCROLLABLE NAV ══ */
    .sb-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        padding-bottom: 12px;
    }

    .sb-nav::-webkit-scrollbar {
        width: 2px;
    }

    .sb-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 2px;
    }

    /* ══ SECTION LABELS ══ */
    .sb-section {
        font-size: 0.54rem;
        font-weight: 700;
        letter-spacing: 0.13em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.35);
        padding: 10px 0 3px;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        transition: text-align var(--sb-ease), padding var(--sb-ease);
    }

    #sidebar-wrapper:hover .sb-section,
    #sidebar-wrapper.sb-locked .sb-section {
        text-align: left;
        padding: 10px 14px 3px;
    }

    .sb-section .ss {
        display: inline;
    }

    .sb-section .sf {
        display: none;
    }

    #sidebar-wrapper:hover .sb-section .ss,
    #sidebar-wrapper.sb-locked .sb-section .ss {
        display: none;
    }

    #sidebar-wrapper:hover .sb-section .sf,
    #sidebar-wrapper.sb-locked .sb-section .sf {
        display: inline;
    }

    /* ══ NAV ITEMS ══ */
    .sb-item {
        list-style: none;
        margin: 1px 0;
    }

    .sb-link {
        display: flex;
        align-items: center;
        height: 42px;
        color: rgba(255, 255, 255, 0.72) !important;
        text-decoration: none !important;
        border-left: 3px solid transparent;
        transition: background 0.14s, color 0.14s;
        cursor: pointer;
        overflow: hidden;
        white-space: nowrap;
    }

    .sb-link:hover {
        background: rgba(255, 255, 255, 0.09);
        color: #fff !important;
    }

    .sb-link.active {
        background: rgba(255, 255, 255, 0.15);
        color: #fff !important;
        border-left-color: rgba(255, 255, 255, 0.65);
    }

    .sb-icon {
        width: var(--sb-w-col);
        min-width: var(--sb-w-col);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.92rem;
        flex-shrink: 0;
    }

    .sb-label {
        flex: 1;
        font-size: 0.82rem;
        font-weight: 500;
        opacity: 0;
        transition: opacity var(--sb-ease);
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sb-arrow {
        width: 20px;
        min-width: 20px;
        margin-right: 8px;
        font-size: 0.58rem;
        opacity: 0;
        transition: opacity var(--sb-ease), transform 0.2s;
        color: rgba(255, 255, 255, 0.42);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #sidebar-wrapper:hover .sb-label,
    #sidebar-wrapper.sb-locked .sb-label {
        opacity: 1;
    }

    #sidebar-wrapper:hover .sb-arrow,
    #sidebar-wrapper.sb-locked .sb-arrow {
        opacity: 1;
    }

    .sb-arrow.open {
        transform: rotate(90deg);
    }

    /* ══ SUBMENU ══ */
    .sb-sub {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(0, 0, 0, 0.09);
    }

    .sb-sub.open {
        max-height: 500px;
    }

    /* Force close when sidebar collapsed (not hovered, not locked) */
    #sidebar-wrapper:not(:hover):not(.sb-locked) .sb-sub {
        max-height: 0 !important;
    }

    /* On mobile: when not mobile-open, also force close */
    @media (max-width: 767.98px) {
        #sidebar-wrapper:not(.mobile-open) .sb-sub {
            max-height: 0 !important;
        }
    }

    .sb-sub-link {
        display: flex;
        align-items: center;
        height: 37px;
        color: rgba(255, 255, 255, 0.62) !important;
        text-decoration: none !important;
        font-size: 0.79rem;
        border-left: 3px solid transparent;
        transition: background 0.13s, color 0.13s;
        white-space: nowrap;
        overflow: hidden;
    }

    .sb-sub-link:hover {
        background: rgba(255, 255, 255, 0.07);
        color: #fff !important;
    }

    .sb-sub-link.active {
        background: rgba(255, 255, 255, 0.10);
        color: #fff !important;
        font-weight: 600;
        border-left-color: rgba(255, 255, 255, 0.42);
    }

    .sb-sub-icon {
        width: var(--sb-w-col);
        min-width: var(--sb-w-col);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.70rem;
        color: rgba(255, 255, 255, 0.42);
        flex-shrink: 0;
    }

    .sb-sub-label {
        flex: 1;
        opacity: 0;
        transition: opacity var(--sb-ease);
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #sidebar-wrapper:hover .sb-sub-label,
    #sidebar-wrapper.sb-locked .sb-sub-label {
        opacity: 1;
    }

    /* On mobile: show labels when open */
    #sidebar-wrapper.mobile-open .sb-label,
    #sidebar-wrapper.mobile-open .sb-arrow,
    #sidebar-wrapper.mobile-open .sb-sub-label,
    #sidebar-wrapper.mobile-open .sb-brand-text,
    #sidebar-wrapper.mobile-open .sb-section .sf {
        opacity: 1;
    }

    #sidebar-wrapper.mobile-open .sb-section .ss {
        display: none;
    }

    #sidebar-wrapper.mobile-open .sb-section {
        text-align: left;
        padding: 10px 14px 3px;
    }

    /* ══ MOBILE ══ */
    @media (max-width: 767.98px) {
        #sidebar-wrapper {
            width: 0 !important;
            transition: width var(--sb-ease) !important;
        }

        #sidebar-wrapper.mobile-open {
            width: var(--sb-w-exp) !important;
        }

        #content-wrapper,
        #content-wrapper.sb-pushed {
            margin-left: 0 !important;
        }
    }

    /* ══ MOBILE BACKDROP ══ */
    #sb-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.40);
        z-index: 1049;
    }

    #sb-backdrop.active {
        display: block;
    }
</style>

{{-- Backdrop --}}
<div id="sb-backdrop"></div>

{{-- ════════════ SIDEBAR ════════════ --}}
<div id="sidebar-wrapper">

    @php
        $appImage = \App\Models\SettingApp::get('app_image');
        $appName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
        $hasImage = $appImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($appImage);
    @endphp

    <a class="sb-brand" href="{{ route('dashboard.index') }}">
        <div class="sb-brand-icon">
            @if ($hasImage)
                <img src="{{ Storage::url($appImage) }}" alt="{{ $appName }}">
            @else
                <i class="fas fa-cat"></i>
            @endif
        </div>
        <span class="sb-brand-text">{{ $appName }}</span>
        <button id="sb-lock-btn" title="Kunci Sidebar" onclick="sbLock(event)">
            <i class="fas fa-thumbtack" id="sb-lock-icon"></i>
        </button>
    </a>

    <hr class="sb-div">

    <nav class="sb-nav">
        <ul style="list-style:none;margin:0;padding:0;">

            <li class="sb-item">
                <a class="sb-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.index') }}">
                    <span class="sb-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span class="sb-label">Dashboard</span>
                </a>
            </li>

            <hr class="sb-div">

            <li>
                <div class="sb-section"><span class="ss">—</span><span class="sf">Operasional</span></div>
            </li>

            @can('order.pos')
                <li class="sb-item">
                    <a class="sb-link {{ request()->routeIs('dashboard.orders.pos') ? 'active' : '' }}"
                        href="{{ route('dashboard.orders.pos') }}">
                        <span class="sb-icon"><i class="fas fa-cash-register"></i></span>
                        <span class="sb-label">Point of Sales</span>
                    </a>
                </li>
            @endcan

            @canany(['order.history', 'order.confirm'])
                @php $pjA = request()->routeIs('dashboard.orders.index*','dashboard.orders.confirmation*'); @endphp
                <li class="sb-item">
                    <div class="sb-link {{ $pjA ? 'active' : '' }}" onclick="sbSub('penjualan',this)">
                        <span class="sb-icon"><i class="fas fa-bag-shopping"></i></span>
                        <span class="sb-label">Penjualan</span>
                        <span class="sb-arrow {{ $pjA ? 'open' : '' }}"><i class="fas fa-chevron-right"></i></span>
                    </div>
                    <div class="sb-sub {{ $pjA ? 'open' : '' }}" id="sub-penjualan">
                        @can('order.history')
                            <a class="sb-sub-link {{ request()->routeIs('dashboard.orders.index*') ? 'active' : '' }}"
                                href="{{ route('dashboard.orders.index') }}">
                                <span class="sb-sub-icon"><i class="fas fa-history"></i></span><span
                                    class="sb-sub-label">Riwayat Pesanan</span>
                            </a>
                        @endcan
                        @can('order.confirm')
                            <a class="sb-sub-link {{ request()->routeIs('dashboard.orders.confirmation*') ? 'active' : '' }}"
                                href="{{ route('dashboard.orders.confirmation') }}">
                                <span class="sb-sub-icon"><i class="fas fa-check-circle"></i></span><span
                                    class="sb-sub-label">Konfirmasi Bayar</span>
                            </a>
                        @endcan
                    </div>
                </li>
            @endcanany

            @php $pbA = request()->routeIs('dashboard.purchases.index*','dashboard.purchases.confirmation*'); @endphp
            <li class="sb-item">
                <div class="sb-link {{ $pbA ? 'active' : '' }}" onclick="sbSub('pembelian',this)">
                    <span class="sb-icon"><i class="fas fa-cart-plus"></i></span>
                    <span class="sb-label">Pembelian</span>
                    <span class="sb-arrow {{ $pbA ? 'open' : '' }}"><i class="fas fa-chevron-right"></i></span>
                </div>
                <div class="sb-sub {{ $pbA ? 'open' : '' }}" id="sub-pembelian">
                    <a class="sb-sub-link {{ request()->routeIs('dashboard.purchases.index*') ? 'active' : '' }}"
                        href="{{ route('dashboard.purchases.index') }}">
                        <span class="sb-sub-icon"><i class="fas fa-history"></i></span><span
                            class="sb-sub-label">Riwayat Pembelian</span>
                    </a>
                    <a class="sb-sub-link {{ request()->routeIs('dashboard.purchases.confirmation*') ? 'active' : '' }}"
                        href="{{ route('dashboard.purchases.confirmation') }}">
                        <span class="sb-sub-icon"><i class="fas fa-check-circle"></i></span><span
                            class="sb-sub-label">Konfirmasi Beli</span>
                    </a>
                </div>
            </li>

            <hr class="sb-div">

            @canany(['category.index', 'product.index', 'supplier.index'])
                <li>
                    <div class="sb-section"><span class="ss">—</span><span class="sf">Manajemen Stok</span></div>
                </li>
                @php $invA = request()->is('dashboard/categories*','dashboard/products*','dashboard/suppliers*'); @endphp
                <li class="sb-item">
                    <div class="sb-link {{ $invA ? 'active' : '' }}" onclick="sbSub('inventori',this)">
                        <span class="sb-icon"><i class="fas fa-box"></i></span>
                        <span class="sb-label">Inventori</span>
                        <span class="sb-arrow {{ $invA ? 'open' : '' }}"><i class="fas fa-chevron-right"></i></span>
                    </div>
                    <div class="sb-sub {{ $invA ? 'open' : '' }}" id="sub-inventori">
                        @can('product.index')
                            <a class="sb-sub-link {{ request()->is('dashboard/products*') ? 'active' : '' }}"
                                href="{{ route('dashboard.products.index') }}">
                                <span class="sb-sub-icon"><i class="fas fa-boxes-stacked"></i></span><span
                                    class="sb-sub-label">Daftar Produk</span>
                            </a>
                        @endcan
                        @can('category.index')
                            <a class="sb-sub-link {{ request()->is('dashboard/categories*') ? 'active' : '' }}"
                                href="{{ route('dashboard.categories.index') }}">
                                <span class="sb-sub-icon"><i class="fas fa-layer-group"></i></span><span
                                    class="sb-sub-label">Kategori</span>
                            </a>
                        @endcan
                        @can('supplier.index')
                            <a class="sb-sub-link {{ request()->is('dashboard/suppliers*') ? 'active' : '' }}"
                                href="{{ route('dashboard.suppliers.index') }}">
                                <span class="sb-sub-icon"><i class="fas fa-truck-field"></i></span><span
                                    class="sb-sub-label">Supplier</span>
                            </a>
                        @endcan
                    </div>
                </li>
                <hr class="sb-div">
            @endcanany

            @canany(['report.hourly', 'report.daily', 'report.monthly'])
                <li>
                    <div class="sb-section"><span class="ss">—</span><span class="sf">Laporan</span></div>
                </li>
                @php $lapA = request()->is('dashboard/reports*'); @endphp
                <li class="sb-item">
                    <div class="sb-link {{ $lapA ? 'active' : '' }}" onclick="sbSub('laporan',this)">
                        <span class="sb-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="sb-label">Analisis Penjualan</span>
                        <span class="sb-arrow {{ $lapA ? 'open' : '' }}"><i class="fas fa-chevron-right"></i></span>
                    </div>
                    <div class="sb-sub {{ $lapA ? 'open' : '' }}" id="sub-laporan">
                        @can('report.hourly')
                            <a class="sb-sub-link {{ request()->is('dashboard/reports/hourly') ? 'active' : '' }}"
                                href="{{ route('dashboard.reports.hourly') }}">
                                <span class="sb-sub-icon"><i class="fas fa-clock"></i></span><span class="sb-sub-label">Per
                                    Jam</span>
                            </a>
                        @endcan
                        @can('report.daily')
                            <a class="sb-sub-link {{ request()->is('dashboard/reports/daily') ? 'active' : '' }}"
                                href="{{ route('dashboard.reports.daily') }}">
                                <span class="sb-sub-icon"><i class="fas fa-calendar-days"></i></span><span
                                    class="sb-sub-label">Harian</span>
                            </a>
                        @endcan
                        @can('report.monthly')
                            <a class="sb-sub-link {{ request()->is('dashboard/reports/monthly') ? 'active' : '' }}"
                                href="{{ route('dashboard.reports.monthly') }}">
                                <span class="sb-sub-icon"><i class="fas fa-calendar-week"></i></span><span
                                    class="sb-sub-label">Bulanan</span>
                            </a>
                        @endcan
                    </div>
                </li>
                <hr class="sb-div">
            @endcanany

            @canany(['user.index', 'role.index', 'permission.index'])
                <li>
                    <div class="sb-section"><span class="ss">—</span><span class="sf">Administrator</span></div>
                </li>
                @php $admA = request()->is('dashboard/users*','dashboard/roles*','dashboard/permissions*'); @endphp
                <li class="sb-item">
                    <div class="sb-link {{ $admA ? 'active' : '' }}" onclick="sbSub('akses',this)">
                        <span class="sb-icon"><i class="fas fa-user-shield"></i></span>
                        <span class="sb-label">Kontrol Akses</span>
                        <span class="sb-arrow {{ $admA ? 'open' : '' }}"><i class="fas fa-chevron-right"></i></span>
                    </div>
                    <div class="sb-sub {{ $admA ? 'open' : '' }}" id="sub-akses">
                        @can('user.index')
                            <a class="sb-sub-link {{ request()->is('dashboard/users*') ? 'active' : '' }}"
                                href="{{ route('dashboard.users.index') }}">
                                <span class="sb-sub-icon"><i class="fas fa-users-gear"></i></span><span
                                    class="sb-sub-label">Pengguna</span>
                            </a>
                        @endcan
                        @can('role.index')
                            <a class="sb-sub-link {{ request()->is('dashboard/roles*') ? 'active' : '' }}"
                                href="{{ route('dashboard.roles.index') }}">
                                <span class="sb-sub-icon"><i class="fas fa-address-card"></i></span><span
                                    class="sb-sub-label">Peran (Role)</span>
                            </a>
                        @endcan
                        @can('permission.index')
                            <a class="sb-sub-link {{ request()->is('dashboard/permissions*') ? 'active' : '' }}"
                                href="{{ route('dashboard.permissions.index') }}">
                                <span class="sb-sub-icon"><i class="fas fa-key"></i></span><span class="sb-sub-label">Hak
                                    Akses</span>
                            </a>
                        @endcan
                    </div>
                </li>
                <hr class="sb-div">
            @endcanany

            <li>
                <div class="sb-section"><span class="ss">—</span><span class="sf">Sistem</span></div>
            </li>
            <li class="sb-item">
                <a class="sb-link {{ request()->is('dashboard/settings*') ? 'active' : '' }}"
                    href="{{ route('dashboard.settings.index') }}">
                    <span class="sb-icon"><i class="fas fa-cog"></i></span>
                    <span class="sb-label">Pengaturan Aplikasi</span>
                </a>
            </li>

        </ul>
    </nav>
</div>

{{-- ════════════ JS ════════════ --}}
<script>
    (function() {
        'use strict';

        const sb = document.getElementById('sidebar-wrapper');
        const content = document.getElementById('content-wrapper');
        const backdrop = document.getElementById('sb-backdrop');
        const lockIcon = document.getElementById('sb-lock-icon');
        const isMobile = () => window.innerWidth < 768;

        /* ── Lock ── */
        let locked = localStorage.getItem('sbLocked') === 'true';

        function setLock(val) {
            locked = val;
            localStorage.setItem('sbLocked', val);
            if (locked) {
                sb.classList.add('sb-locked');
                lockIcon.style.transform = 'rotate(-45deg)';
                if (!isMobile() && content) content.classList.add('sb-pushed');
            } else {
                sb.classList.remove('sb-locked');
                lockIcon.style.transform = '';
                if (content) content.classList.remove('sb-pushed');
            }
        }

        setLock(locked);

        window.sbLock = function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!isMobile()) setLock(!locked);
        };

        /* ── Submenu ── */
        window.sbSub = function(name, el) {
            const expanded = isMobile() ?
                sb.classList.contains('mobile-open') :
                (sb.matches(':hover') || locked);
            if (!expanded) return;

            const sub = document.getElementById('sub-' + name);
            const arrow = el.querySelector('.sb-arrow');
            const open = sub.classList.contains('open');
            sub.classList.toggle('open', !open);
            if (arrow) arrow.classList.toggle('open', !open);
        };

        /* ── Mobile open/close ── */
        function mobileOpen() {
            sb.classList.add('mobile-open');
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function mobileClose() {
            sb.classList.remove('mobile-open');
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

        backdrop.addEventListener('click', mobileClose);

        /* Bind burger — defer so topbar partial is in DOM */
        function bindBurger() {
            const burger = document.getElementById('sidebarToggleTop');
            if (!burger) return;
            const clone = burger.cloneNode(true);
            burger.replaceWith(clone);
            clone.addEventListener('click', function(e) {
                e.preventDefault();
                sb.classList.contains('mobile-open') ? mobileClose() : mobileOpen();
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bindBurger);
        } else {
            // slight delay ensures topbar partial rendered
            setTimeout(bindBurger, 0);
        }

        window.addEventListener('resize', function() {
            if (!isMobile()) {
                mobileClose();
                setLock(locked);
            } else {
                if (content) content.classList.remove('sb-pushed');
            }
        });
    })();
</script>
