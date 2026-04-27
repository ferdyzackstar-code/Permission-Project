<style>
    /* CSS kamu tetap dipertahankan sesuai permintaan */
    #accordionSidebar .nav-item .collapse .collapse-inner,
    #accordionSidebar .nav-item .collapsing .collapse-inner {
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
    }

    #accordionSidebar .nav-item .nav-link span,
    #accordionSidebar .nav-item .collapse .collapse-inner a.collapse-item,
    #accordionSidebar .nav-item .collapsing .collapse-inner a.collapse-item {
        white-space: normal !important;
        font-size: 0.85rem !important;
        line-height: 1 !important;
        display: inline-block;
        vertical-align: middle;
        color: rgba(255, 255, 255, 0.8) !important;
    }

    #accordionSidebar .nav-item .nav-link {
        padding: 0.6rem 0.75rem !important;
    }

    #accordionSidebar .nav-item .collapse .collapse-inner a.collapse-item,
    #accordionSidebar .nav-item .collapsing .collapse-inner a.collapse-item {
        padding: 0.6rem 1rem !important;
        margin: 0 !important;
    }

    #accordionSidebar .nav-item .collapse .collapse-inner a.collapse-item:hover,
    #accordionSidebar .nav-item .collapsing .collapse-inner a.collapse-item:hover {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
    }

    #accordionSidebar .nav-item .collapse .collapse-inner a.collapse-item.active,
    #accordionSidebar .nav-item .collapsing .collapse-inner a.collapse-item.active {
        color: #ffffff !important;
        font-weight: 700 !important;
        background-color: rgba(255, 255, 255, 0.2) !important;
    }

    @media (max-width: 768px) {
        #accordionSidebar .nav-item .nav-link span {
            font-size: 0.7rem !important;
        }
    }
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion sticky-top" id="accordionSidebar">

    @php
        $appImage = \App\Models\SettingApp::get('app_image');
        $appName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
        $hasImage = $appImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($appImage);
    @endphp

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard.index') }}">
        <div class="sidebar-brand-icon">
            @if ($hasImage)
                <img src="{{ Storage::url($appImage) }}" alt="{{ $appName }}"
                    style="height:36px; width:36px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.4);">
            @else
                <i class="fas fa-cat"></i>
            @endif
        </div>
        <div class="sidebar-brand-text mx-2">{{ $appName }}</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    {{-- GRUP TRANSAKSI --}}
    <div class="sidebar-heading">Operasional</div>

    @can('order.pos')
        <li class="nav-item {{ request()->routeIs('dashboard.orders.pos') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard.orders.pos') }}">
                <i class="fas fa-fw fa-cash-register"></i>
                <span>Point of Sales (POS)</span></a>
        </li>
    @endcan

    @canany(['order.history', 'order.confirm'])
        <li
            class="nav-item {{ request()->routeIs('dashboard.orders.index*', 'dashboard.orders.confirmation*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('dashboard.orders.index*', 'dashboard.orders.confirmation*') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapseTransaksi"
                aria-expanded="{{ request()->routeIs('dashboard.orders.index*', 'dashboard.orders.confirmation*') ? 'true' : 'false' }}"
                aria-controls="collapseTransaksi">
                <i class="fas fa-solid fa-bag-shopping"></i>
                <span>Penjualan</span>
            </a>
            <div id="collapseTransaksi"
                class="collapse {{ request()->routeIs('dashboard.orders.index*', 'dashboard.orders.confirmation*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner">
                    @can('order.history')
                        <a class="collapse-item {{ request()->routeIs('dashboard.orders.index*') ? 'active' : '' }}"
                            href="{{ route('dashboard.orders.index') }}">
                            <i class="fas fa-history fa-sm fa-fw mr-2"></i>Riwayat Pesanan</a>
                    @endcan
                    @can('order.confirm')
                        <a class="collapse-item {{ request()->routeIs('dashboard.orders.confirmation*') ? 'active' : '' }}"
                            href="{{ route('dashboard.orders.confirmation') }}">
                            <i class="fas fa-check-circle fa-sm fa-fw mr-2"></i>Konfirmasi Bayar</a>
                    @endcan
                </div>
            </div>
        </li>
    @endcanany

    {{-- GRUP SUPPLY --}}
    <li
        class="nav-item {{ request()->routeIs('dashboard.purchases.index*', 'dashboard.purchases.confirmation*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('dashboard.purchases.index*', 'dashboard.purchases.confirmation*') ? '' : 'collapsed' }}"
            href="#" data-toggle="collapse" data-target="#collapsePembelian"
            aria-expanded="{{ request()->routeIs('dashboard.purchases.index*', 'dashboard.purchases.confirmation*') ? 'true' : 'false' }}"
            aria-controls="collapsePembelian">
            <i class="fa-solid fa-cart-plus"></i>
            <span>Pembelian </span>
        </a>
        <div id="collapsePembelian"
            class="collapse {{ request()->routeIs('dashboard.purchases.index*', 'dashboard.purchases.confirmation*') ? 'show' : '' }}"
            data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner">
                <a class="collapse-item {{ request()->routeIs('dashboard.purchases.index*') ? 'active' : '' }}"
                    href="{{ route('dashboard.purchases.index') }}">
                    <i class="fas fa-history fa-sm fa-fw mr-2"></i>Riwayat Pembelian</a>
                <a class="collapse-item {{ request()->routeIs('dashboard.purchases.confirmation*') ? 'active' : '' }}"
                    href="{{ route('dashboard.purchases.confirmation') }}">
                    <i class="fas fa-check-circle fa-sm fa-fw mr-2"></i>Konfirmasi Beli</a>
            </div>
        </div>
        <hr class="sidebar-divider">
    </li>

    {{-- GRUP LOGISTIK --}}
    @canany(['category.index', 'product.index', 'supplier.index', 'outlet.index'])
        <div class="sidebar-heading">Manajemen Stok</div>
        <li
            class="nav-item {{ request()->is('dashboard/categories*', 'dashboard/products*', 'dashboard/outlets*', 'dashboard/suppliers*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->is('dashboard/categories*', 'dashboard/products*', 'dashboard/outlets*', 'dashboard/suppliers*') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapseLogistik"
                aria-expanded="{{ request()->is('dashboard/categories*', 'dashboard/products*', 'dashboard/outlets*', 'dashboard/suppliers*') ? 'true' : 'false' }}"
                aria-controls="collapseLogistik">
                <i class="fas fa-fw fa-box"></i>
                <span>Inventori</span>
            </a>
            <div id="collapseLogistik"
                class="collapse {{ request()->is('dashboard/categories*', 'dashboard/products*', 'dashboard/outlets*', 'dashboard/suppliers*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner">
                    @can('product.index')
                        <a class="collapse-item {{ request()->is('dashboard/products*') ? 'active' : '' }}"
                            href="{{ route('dashboard.products.index') }}">
                            <i class="fa-solid fa-boxes-stacked fa-sm fa-fw mr-2"></i>Daftar Produk</a>
                    @endcan
                    @can('category.index')
                        <a class="collapse-item {{ request()->is('dashboard/categories*') ? 'active' : '' }}"
                            href="{{ route('dashboard.categories.index') }}">
                            <i class="fa-solid fa-layer-group fa-sm fa-fw mr-2"></i>Kategori</a>
                    @endcan
                    @can('supplier.index')
                        <a class="collapse-item {{ request()->is('dashboard/suppliers*') ? 'active' : '' }}"
                            href="{{ route('dashboard.suppliers.index') }}">
                            <i class="fa-solid fa-truck-field fa-sm fa-fw mr-2"></i>Supplier</a>
                    @endcan
                    @can('outlet.index')
                        <a class="collapse-item {{ request()->is('dashboard/outlets*') ? 'active' : '' }}"
                            href="{{ route('dashboard.outlets.index') }}">
                            <i class="fa-solid fa-shop fa-sm fa-fw mr-2"></i>Outlet</a>
                    @endcan
                </div>
            </div>
        </li>
        <hr class="sidebar-divider">
    @endcanany


    {{-- GRUP ANALISIS --}}
    @canany(['order.pos', 'order.history'])
        {{-- Menyesuaikan izin laporan --}}
        <div class="sidebar-heading">Laporan</div>
        <li class="nav-item {{ request()->is('dashboard/reports*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->is('dashboard/reports*') ? '' : 'collapsed' }}" href="#"
                data-toggle="collapse" data-target="#collapseLaporan"
                aria-expanded="{{ request()->is('dashboard/reports*') ? 'true' : 'false' }}"
                aria-controls="collapseLaporan">
                <i class="fa-solid fa-chart-line"></i>
                <span>Analisis Penjualan</span>
            </a>
            <div id="collapseLaporan" class="collapse {{ request()->is('dashboard/reports*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner">
                    <a class="collapse-item {{ request()->is('dashboard/reports/hourly') ? 'active' : '' }}"
                        href="{{ route('dashboard.reports.hourly') }}">
                        <i class="fa-solid fa-clock fa-sm fa-fw mr-2"></i>Per Jam</a>

                    <a class="collapse-item {{ request()->is('dashboard/reports/daily') ? 'active' : '' }}"
                        href="{{ route('dashboard.reports.daily') }}">
                        <i class="fa-solid fa-calendar-days fa-sm fa-fw mr-2"></i>Harian</a>

                    <a class="collapse-item {{ request()->is('dashboard/reports/monthly') ? 'active' : '' }}"
                        href="{{ route('dashboard.reports.monthly') }}">
                        <i class="fa-solid fa-calendar-week fa-sm fa-fw mr-2"></i>Bulanan</a>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider">
    @endcanany


    {{-- GRUP ADMINISTRATOR (SYSTEM SETTINGS) --}}
    @canany(['user.index', 'role.index', 'permission.index'])
        <div class="sidebar-heading">Administrator</div>
        <li
            class="nav-item {{ request()->is('dashboard/users*', 'dashboard/roles*', 'dashboard/permissions*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->is('dashboard/users*', 'dashboard/roles*', 'dashboard/permissions*') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapseSystem">
                <i class="fas fa-fw fa-user-shield"></i>
                <span>Kontrol Akses</span>
            </a>
            <div id="collapseSystem"
                class="collapse {{ request()->is('dashboard/users*', 'dashboard/roles*', 'dashboard/permissions*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner">
                    @can('user.index')
                        <a class="collapse-item {{ request()->is('dashboard/users*') ? 'active' : '' }}"
                            href="{{ route('dashboard.users.index') }}">
                            <i class="fa-solid fa-users-gear fa-sm fa-fw mr-2"></i>Pengguna</a>
                    @endcan

                    @can('role.index')
                        <a class="collapse-item {{ request()->is('dashboard/roles*') ? 'active' : '' }}"
                            href="{{ route('dashboard.roles.index') }}">
                            <i class="fa-solid fa-address-card fa-sm fa-fw mr-2"></i>Peran (Role)</a>
                    @endcan

                    @can('permission.index')
                        <a class="collapse-item {{ request()->is('dashboard/permissions*') ? 'active' : '' }}"
                            href="{{ route('dashboard.permissions.index') }}">
                            <i class="fa-solid fa-key fa-sm fa-fw mr-2"></i>Hak Akses</a>
                    @endcan
                </div>
            </div>
        </li>
        <hr class="sidebar-divider">
    @endcanany


    {{-- PENGATURAN --}}
    <div class="sidebar-heading">Sistem</div>
    <li class="nav-item {{ request()->is('dashboard/settings*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.settings.index') }}">
            <i class="fas fa-fw fa-cog"></i>
            <span>Pengaturan Aplikasi</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
