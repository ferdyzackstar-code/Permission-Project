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

    <div class="sidebar-heading">
        Petshop Management
    </div>

    <li class="nav-item {{ request()->is('dashboard/categories*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.categories.index') }}">
            <i class="fas fa-fw fa-list"></i>
            <span>Kategori Produk</span></a>
    </li>

    <li class="nav-item {{ request()->is('dashboard/products*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.products.index') }}">
            <i class="fas fa-fw fa-box"></i>
            <span>Daftar Produk</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        System Settings
    </div>

    <li class="nav-item {{ request()->is('dashboard/users*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>User Management</span></a>
    </li>

    <li class="nav-item {{ request()->is('dashboard/roles*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.roles.index') }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Role & Permissions</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
