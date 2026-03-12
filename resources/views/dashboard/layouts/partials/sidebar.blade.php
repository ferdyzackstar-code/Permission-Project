<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-cat"></i>
        </div>
        <div class="sidebar-brand-text mx-2">Anda Petshop <sup>Admin</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    @canany(['report-summary', 'report-outlet', 'report-employee'])
        <div class="sidebar-heading">Penjualan</div>

        <li class="nav-item {{ request()->is('dashboard/reports*') ? 'active' : '' }}">

            <a class="nav-link {{ request()->is('dashboard/reports*') ? '' : 'collapsed' }}" href="#"
                data-toggle="collapse" data-target="#collapsePenjualan"
                aria-expanded="{{ request()->is('dashboard/reports*') ? 'true' : 'false' }}"
                aria-controls="collapsePenjualan">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Daftar Laporan</span>
            </a>    

            <div id="collapsePenjualan" class="collapse {{ request()->is('dashboard/reports*') ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Laporan Outlet:</h6>

                    @can('report-summary')
                        <a class="collapse-item {{ request()->is('dashboard/reports/summary*') ? 'active' : '' }}"
                            href="{{ route('dashboard.reports.summary') }}">Ringkasan Penjualan</a>
                    @endcan

                    @can('report-outlet')
                        <a class="collapse-item {{ request()->is('dashboard/reports/outlet*') ? 'active' : '' }}"
                            href="{{ route('dashboard.reports.outlet') }}">Penjualan Per Outlet</a>
                    @endcan

                    @can('report-employee')
                        <a class="collapse-item {{ request()->is('dashboard/reports/employee*') ? 'active' : '' }}"
                            href="{{ route('dashboard.reports.employee') }}">Laporan Karyawan</a>
                    @endcan
                </div>
            </div>
        </li>
        <hr class="sidebar-divider">
    @endcanany

    <div class="sidebar-heading">Logistik & Stok</div>
    <li
        class="nav-item {{ request()->is('dashboard/categories*', 'dashboard/products*', 'dashboard/outlets*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLogistik"
            aria-expanded="true" aria-controls="collapseLogistik">
            <i class="fas fa-fw fa-box"></i>
            <span>Inventori</span>
        </a>
        <div id="collapseLogistik"
            class="collapse {{ request()->is('dashboard/categories*', 'dashboard/products*', 'dashboard/outlets*') ? 'show' : '' }}"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can('category-list')
                    <a class="collapse-item {{ request()->is('dashboard/categories*') ? 'active' : '' }}"
                        href="{{ route('dashboard.categories.index') }}">Kategori Produk</a>
                @endcan

                @can('product-list')
                    <a class="collapse-item {{ request()->is('dashboard/products*') ? 'active' : '' }}"
                        href="{{ route('dashboard.products.index') }}">Daftar Produk</a>
                @endcan

                @can('outlet-list')
                    <a class="collapse-item {{ request()->is('dashboard/outlets*') ? 'active' : '' }}"
                        href="{{ route('dashboard.outlets.index') }}">Manajemen Outlet</a>
                @endcan
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    @canany(['user-list', 'role-list', 'permission-list'])
        <div class="sidebar-heading">System Settings</div>
        <li
            class="nav-item {{ request()->is('dashboard/users*', 'dashboard/roles*', 'dashboard/permissions*') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSystem"
                aria-expanded="true" aria-controls="collapseSystem">
                <i class="fas fa-fw fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            <div id="collapseSystem"
                class="collapse {{ request()->is('dashboard/users*', 'dashboard/roles*', 'dashboard/permissions*') ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    @can('user-list')
                        <a class="collapse-item {{ request()->is('dashboard/users*') ? 'active' : '' }}"
                            href="{{ route('dashboard.users.index') }}">User Management</a>
                    @endcan

                    @can('role-list')
                        <a class="collapse-item {{ request()->is('dashboard/roles*') ? 'active' : '' }}"
                            href="{{ route('dashboard.roles.index') }}">Role & Permissions</a>
                    @endcan

                    @can('permission-list')
                        <a class="collapse-item {{ request()->is('dashboard/permissions*') ? 'active' : '' }}"
                            href="{{ route('dashboard.permissions.index') }}">Data Permissions</a>
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
