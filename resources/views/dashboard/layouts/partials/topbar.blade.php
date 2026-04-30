{{-- TOPBAR v3 - Floating card style, rounded, margin all sides --}}

@php
    $appName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
    $topbarTitle = 'MANAJEMEN ' . strtoupper($appName);
    $user = Auth::user();
    $userPhoto =
        $user->image && file_exists(public_path('storage/uploads/users/' . $user->image))
            ? asset('storage/uploads/users/' . $user->image)
            : asset('storage/uploads/users/default-user.jpg');
@endphp

<style>
    /* ══ TOPBAR FLOATING CARD ══ */
    .topbar-main {
        position: sticky;
        top: 12px;
        /* jarak dari atas */
        z-index: 1040;
        /* Floating card — margin kiri kanan atas */
        margin: 12px 16px 0 16px !important;
        padding: 0 !important;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.10), 0 1px 4px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(232, 234, 240, 0.8);
        /* Height lebih besar */
        height: 62px;
        min-height: 62px;
        display: flex;
        align-items: stretch;
        /* Transition untuk saat sidebar push */
        transition: margin-left 0.28s cubic-bezier(0.4, 0, 0.2, 1),
            width 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .topbar-inner {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 0 1.25rem;
        gap: 0.75rem;
    }

    /* App title — SELALU tampil, no d-none */
    .topbar-title {
        flex: 1;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #3d4466;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        user-select: none;
        /* Tidak pernah disembunyikan */
        display: block !important;
        min-width: 0;
    }

    /* Di layar sangat kecil, shrink font tapi tetap tampil */
    @media (max-width: 380px) {
        .topbar-title {
            font-size: 0.68rem;
            letter-spacing: 0.04em;
        }
    }

    /* ── User button ── */
    .topbar-user-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: none;
        border: none;
        padding: 0 0.4rem;
        cursor: pointer;
        border-radius: 10px;
        height: 62px;
        transition: background 0.15s;
        flex-shrink: 0;
    }

    .topbar-user-btn::after {
        display: none !important;
    }

    .topbar-user-btn:hover {
        background: #f4f6fb;
    }

    .topbar-user-btn:focus {
        outline: none;
        box-shadow: none;
    }

    .topbar-user-name {
        font-size: 0.82rem;
        font-weight: 600;
        color: #3d4466;
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        /* Tampil di md ke atas */
        display: none;
    }

    @media (min-width: 576px) {
        .topbar-user-name {
            display: block;
        }
    }

    .topbar-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e3e6f0;
        flex-shrink: 0;
    }

    .topbar-chevron {
        font-size: 0.58rem;
        color: #9da3b4;
        transition: transform 0.2s;
        display: none;
    }

    @media (min-width: 576px) {
        .topbar-chevron {
            display: inline-block;
        }
    }

    .topbar-user-btn[aria-expanded="true"] .topbar-chevron {
        transform: rotate(180deg);
    }

    /* ── Dropdown ── */
    .topbar-dd {
        border: none !important;
        border-radius: 14px !important;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.14), 0 2px 8px rgba(0, 0, 0, 0.07) !important;
        min-width: 252px;
        padding: 0 !important;
        overflow: hidden;
        margin-top: 6px !important;
    }

    .dd-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .dd-head-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.45);
        flex-shrink: 0;
    }

    .dd-head-info {
        min-width: 0;
    }

    .dd-head-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dd-head-email {
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.72);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 1px;
    }

    .dd-head-role {
        display: inline-block;
        margin-top: 4px;
        font-size: 0.63rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.65);
        background: rgba(255, 255, 255, 0.16);
        border-radius: 20px;
        padding: 1px 8px;
    }

    .dd-body {
        padding: 0.35rem 0;
    }

    .dd-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.58rem 1rem;
        font-size: 0.83rem;
        color: #3d4466;
        text-decoration: none !important;
        transition: background 0.12s, color 0.12s;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .dd-item:hover {
        background: #f4f6fb;
        color: #4e73df;
    }

    .dd-item i {
        width: 15px;
        text-align: center;
        color: #adb5bd;
        font-size: 0.8rem;
        transition: color 0.12s;
    }

    .dd-item:hover i {
        color: #4e73df;
    }

    .dd-sep {
        border: 0;
        border-top: 1px solid #edf0f7;
        margin: 0.25rem 0;
    }

    .dd-item.dd-logout {
        color: #e74a3b;
    }

    .dd-item.dd-logout i {
        color: #e74a3b;
    }

    .dd-item.dd-logout:hover {
        background: #fff5f5;
        color: #c0392b;
    }

    .dd-item.dd-logout:hover i {
        color: #c0392b;
    }

    /* ── Mobile burger ── */
    #sidebarToggleTop {
        display: none;
        align-items: center;
        background: none;
        border: none;
        font-size: 1.15rem;
        color: #5a5c69;
        padding: 0 0.6rem 0 0;
        cursor: pointer;
        flex-shrink: 0;
        line-height: 1;
    }

    @media (max-width: 767.98px) {
        #sidebarToggleTop {
            display: flex;
        }
    }

    /* ── Content area: beri padding-top agar konten tidak tertutup topbar floating ── */
    /* topbar tinggi 62px + top 12px + margin-bottom = ~90px total */
    #content>.container-fluid {
        padding-top: 1rem;
    }
</style>

<nav class="topbar-main">
    <div class="topbar-inner">

        {{-- Burger mobile --}}
        <button id="sidebarToggleTop" aria-label="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>

        {{-- App title - ALWAYS visible --}}
        <span class="topbar-title">{{ $topbarTitle }}</span>

        {{-- User dropdown --}}
        <div class="dropdown" style="flex-shrink:0;">
            <button class="topbar-user-btn" id="userDropdown" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <span class="topbar-user-name">{{ $user->name }}</span>
                <img class="topbar-avatar" src="{{ $userPhoto }}" alt="{{ $user->name }}">
                <i class="fas fa-chevron-down topbar-chevron"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-right topbar-dd" aria-labelledby="userDropdown">

                <div class="dd-head">
                    <img class="dd-head-avatar" src="{{ $userPhoto }}" alt="{{ $user->name }}">
                    <div class="dd-head-info">
                        <div class="dd-head-name">{{ $user->name }}</div>
                        <div class="dd-head-email">{{ $user->email }}</div>
                        @if ($user->roles && $user->roles->isNotEmpty())
                            <span class="dd-head-role">{{ $user->roles->first()->name }}</span>
                        @endif
                    </div>
                </div>

                <div class="dd-body">
                    <a class="dd-item" href="{{ route('profile.index') }}">
                        <i class="fas fa-user-circle"></i>Edit Profil
                    </a>
                    <hr class="dd-sep">
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dd-item dd-logout">
                            <i class="fas fa-sign-out-alt"></i>Keluar
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</nav>

<script>
    window.previewImage = function(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (input && input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                if (preview) preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined') {
            $('.custom-file-input').on('change', function() {
                const name = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass('selected').html(name);
            });
        }
    });
</script>
