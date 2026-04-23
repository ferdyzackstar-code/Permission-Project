<style>
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

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard.index') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-cat"></i>
        </div>
        <div class="sidebar-brand-text mx-2">Anda Petshop</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    {{-- GRUP TRANSAKSI KASIR --}}
    @canany(['order.pos', 'order.history', 'order.confirm'])

        <div class="sidebar-heading">PENJUALAN</div>
        <li class="nav-item {{ request()->is('dashboard/reports*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->is('dashboard/reports*') ? '' : 'collapsed' }}" href="#"
                data-toggle="collapse" data-target="#collapseLaporan"
                aria-expanded="{{ request()->is('dashboard/reports*') ? 'true' : 'false' }}"
                aria-controls="collapseLaporan">
                <i class="fa-solid fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
            <div id="collapseLaporan" class="collapse {{ request()->is('dashboard/reports*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner">
                    <a class="collapse-item {{ request()->is('dashboard/reports/hourly') ? 'active' : '' }}"
                        href="{{ route('dashboard.reports.hourly') }}">
                        <i class="fa-solid fa-clock"></i> Laporan Per-jam</a>

                    <a class="collapse-item {{ request()->is('dashboard/reports/daily') ? 'active' : '' }}"
                        href="{{ route('dashboard.reports.daily') }}"><i class="fa-solid fa-calendar-days"></i> Laporan
                        Harian</a>

                    <a class="collapse-item {{ request()->is('dashboard/reports/monthly') ? 'active' : '' }}"
                        href="{{ route('dashboard.reports.monthly') }}"><i class="fa-solid fa-calendar-week"></i> Laporan
                        Bulanan</a>
                </div>
            </div>
        </li>

        @can('order.pos')
            <li class="nav-item {{ request()->routeIs('dashboard.orders.pos') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard.orders.pos') }}">
                    <i class="fas fa-fw fa-cash-register"></i>
                    <span>Mesin Kasir (POS)</span></a>
            </li>
        @endcan

        @can('order.history')
            <li class="nav-item {{ request()->routeIs('dashboard.orders.index*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard.orders.index') }}">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Riwayat Transaksi</span></a>
            </li>
        @endcan

        @can('order.confirm')
            <li class="nav-item {{ request()->routeIs('dashboard.orders.confirmation*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard.orders.confirmation') }}">
                    <i class="fas fa-fw fa-receipt"></i>
                    <span>Konfirmasi Pembayaran</span></a>
            </li>
        @endcan
    @endcanany

    <hr class="sidebar-divider">

    {{-- GRUP PEMBELIAN --}}
    <div class="sidebar-heading">Pembelian</div>

    <li class="nav-item {{ request()->routeIs('dashboard.purchases.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.purchases.index') }}">
            <i class="fa-solid fa-cart-plus"></i>
            <span>Pembelian</span></a>
    </li>

    <li class="nav-item {{-- {{ request()->routeIs('dashboard.orders.index*') ? 'active' : '' }} --}}">
        <a class="nav-link" href="#{{-- {{ route('dashboard.orders.index') }} --}}">
            <i class="fa-solid fa-box-open"></i>
            <span>Item Pembelian</span></a>
    </li>

    <hr class="sidebar-divider">

    {{-- GRUP LOGISTIK --}}
    @canany(['category.index', 'product.index', 'supplier.index', 'outlet.index'])
        <div class="sidebar-heading">Logistik & Stok</div>
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
                    @can('category.index')
                        <a class="collapse-item {{ request()->is('dashboard/categories*') ? 'active' : '' }}"
                            href="{{ route('dashboard.categories.index') }}">
                            <i class="fa-solid fa-layer-group"></i> Kategori Produk
                        </a>
                    @endcan

                    @can('product.index')
                        <a class="collapse-item {{ request()->is('dashboard/products*') ? 'active' : '' }}"
                            href="{{ route('dashboard.products.index') }}">
                            <i class="fa-solid fa-boxes-stacked"></i> Daftar Produk
                        </a>
                    @endcan

                    @can('supplier.index')
                        <a class="collapse-item {{ request()->is('dashboard/suppliers*') ? 'active' : '' }}"
                            href="{{ route('dashboard.suppliers.index') }}">
                            <i class="fa-solid fa-truck-field"></i> Daftar Supplier
                        </a>
                    @endcan

                    @can('outlet.index')
                        <a class="collapse-item {{ request()->is('dashboard/outlets*') ? 'active' : '' }}"
                            href="{{ route('dashboard.outlets.index') }}">
                            <i class="fa-solid fa-shop"></i> Manajemen Outlet
                        </a>
                    @endcan
                </div>
            </div>
        </li>
    @endcanany

    <hr class="sidebar-divider">

    {{-- GRUP SETTINGS --}}
    @canany(['user.index', 'role.index', 'permission.index'])
        <div class="sidebar-heading">System Settings</div>
        <li class="nav-item {{ request()->is('dashboard/users*', 'roles*', 'dashboard/permissions*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->is('dashboard/users*', 'dashboard/roles*', 'dashboard/permissions*') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapseSystem">
                <i class="fas fa-fw fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            <div id="collapseSystem"
                class="collapse {{ request()->is('dashboard/users*', 'dashboard/roles*', 'dashboard/permissions*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner">
                    @can('user.index')
                        <a class="collapse-item {{ request()->is('dashboard/users*') ? 'active' : '' }}"
                            href="{{ route('dashboard.users.index') }}"><i class="fa-solid fa-user-shield"></i> User
                            Management</a>
                    @endcan

                    @can('role.index')
                        <a class="collapse-item {{ request()->is('dashboard/roles*') ? 'active' : '' }}"
                            href="{{ route('dashboard.roles.index') }}"><i class="fa-solid fa-shield-halved"></i> Role &
                            Permissions</a>
                    @endcan

                    @can('permission.index')
                        <a class="collapse-item {{ request()->is('dashboard/permissions*') ? 'active' : '' }}"
                            href="{{ route('dashboard.permissions.index') }}"><i class="fa-solid fa-key"></i> Data
                            Permissions</a>
                    @endcan
                </div>
            </div>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
    @endcanany

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
