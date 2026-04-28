<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | {{ \App\Models\SettingApp::get('app_name', 'Anda Petshop') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@700;800&family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --purple-dark: #3b1f8c;
            --purple-main: #512da8;
            --purple-mid: #6a3fc4;
            --purple-light: #7c5cbf;
            --purple-soft: #ede7f6;
            --input-bg: #f0eef8;
            --text-dark: #2d2640;
            --text-muted: #7a7290;
            --white: #ffffff;
            --shadow: 0 20px 60px rgba(81, 45, 168, .2);
            --radius-card: 28px;
            --radius-input: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0eff8;
            background-image:
                radial-gradient(circle at 15% 50%, rgba(81, 45, 168, .09) 0%, transparent 50%),
                radial-gradient(circle at 85% 20%, rgba(255, 143, 0, .06) 0%, transparent 40%);
            /* Padding atas-bawah + kiri-kanan — penting untuk mobile */
            padding: 24px 16px;
        }

        /* ─────────────────────────────────────────
           KARTU UTAMA
        ───────────────────────────────────────── */
        .card-auth {
            background: var(--white);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow);
            width: 860px;
            max-width: 100%;
            display: flex;
            /* Tidak pakai overflow:hidden di sini agar konten mobile bisa scroll */
        }

        /* ─────────────────────────────────────────
           PANEL KIRI — foto / gradient
        ───────────────────────────────────────── */
        .panel-visual {
            width: 42%;
            flex-shrink: 0;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            /* konten dari atas */
            padding: 36px 24px 36px;
            text-align: center;
            color: var(--white);
            overflow: hidden;
            border-radius: var(--radius-card) 0 0 var(--radius-card);
        }

        .pv-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            background: linear-gradient(145deg, var(--purple-mid), var(--purple-dark));
        }

        .pv-photo {
            position: absolute;
            inset: 0;
            z-index: 1;
            background-size: cover;
            background-position: center;
        }

        .pv-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: linear-gradient(165deg, rgba(59, 31, 140, .82), rgba(81, 45, 168, .75));
        }

        /* Paw dekorasi */
        .paw-decor {
            position: absolute;
            color: var(--white);
            opacity: .07;
            pointer-events: none;
            z-index: 2;
        }

        .paw-decor.tr {
            font-size: 90px;
            top: -22px;
            right: -22px;
            transform: rotate(25deg);
        }

        .paw-decor.bl {
            font-size: 60px;
            bottom: -15px;
            left: -15px;
            transform: rotate(-20deg);
        }

        /* Semua konten panel kiri ada di sini */
        .pv-content {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        /* Logo toko */
        .shop-logo {
            width: 68px;
            height: 68px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .18);
            border: 2px solid rgba(255, 255, 255, .3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .shop-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .shop-logo i {
            font-size: 28px;
            color: rgba(255, 255, 255, .9);
        }

        .shop-name {
            font-family: 'Nunito', sans-serif;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: .3px;
            /* Batas bawah sebelum teks panel */
            margin-bottom: 24px;
        }

        /* Teks panel (berubah sesuai tab) */
        .panel-texts {
            width: 100%;
        }

        .panel-text-item {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .panel-text-item.active {
            display: flex;
        }

        .panel-text-item h2 {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.3;
        }

        .panel-text-item p {
            font-size: 12px;
            opacity: .88;
            line-height: 1.65;
            max-width: 200px;
        }

        .btn-switch {
            margin-top: 16px;
            background: rgba(255, 255, 255, .16);
            border: 1.5px solid rgba(255, 255, 255, .5);
            color: var(--white);
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: 9px 22px;
            border-radius: 50px;
            cursor: pointer;
            white-space: nowrap;
            transition: background .25s, transform .2s;
        }

        .btn-switch:hover {
            background: rgba(255, 255, 255, .28);
            transform: translateY(-1px);
        }

        /* ─────────────────────────────────────────
           PANEL KANAN — form
        ───────────────────────────────────────── */
        .panel-form {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            border-radius: 0 var(--radius-card) var(--radius-card) 0;
            overflow: hidden;
        }

        /* Tab pills */
        .auth-tabs {
            display: flex;
            gap: 8px;
            padding: 28px 36px 0;
            flex-shrink: 0;
        }

        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 9px 0;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            border: 2px solid transparent;
            transition: all .25s;
            color: var(--text-muted);
            background: var(--input-bg);
        }

        .auth-tab.active {
            background: var(--purple-soft);
            color: var(--purple-main);
            border-color: var(--purple-main);
        }

        /* Slides wrapper — tinggi tetap di desktop, scroll jika overflow */
        .slides-wrapper {
            flex: 1;
            position: relative;
            min-height: 400px;
        }

        .form-slide {
            position: absolute;
            inset: 0;
            padding: 24px 36px 36px;
            overflow-y: auto;
            opacity: 0;
            transform: translateX(20px);
            transition: opacity .38s ease, transform .38s ease;
            pointer-events: none;
        }

        .form-slide.active {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        .form-slide h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 3px;
        }

        .form-slide .subtitle {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        /* Input */
        .input-wrap {
            position: relative;
            margin-bottom: 11px;
        }

        .input-wrap i.ico {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--purple-light);
            font-size: 13px;
            width: 16px;
            text-align: center;
        }

        .input-wrap input {
            width: 100%;
            background: var(--input-bg);
            border: 2px solid transparent;
            border-radius: var(--radius-input);
            padding: 12px 14px 12px 40px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            outline: none;
            transition: border-color .25s, background .25s;
        }

        .input-wrap input:focus {
            border-color: var(--purple-main);
            background: #fff;
        }

        .input-wrap input.is-invalid {
            border-color: #e53935 !important;
        }

        .input-wrap input::placeholder {
            color: #b0aac8;
        }

        .error-text {
            font-size: 11px;
            color: #e53935;
            margin-top: -7px;
            margin-bottom: 8px;
            padding-left: 4px;
            display: block;
        }

        .forgot-link {
            display: block;
            text-align: right;
            font-size: 12px;
            color: var(--purple-main);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 16px;
            margin-top: -3px;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            display: block;
            background: linear-gradient(135deg, var(--purple-mid), var(--purple-dark));
            color: var(--white);
            border: none;
            border-radius: var(--radius-input);
            padding: 13px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(81, 45, 168, .35);
            transition: opacity .25s, transform .2s, box-shadow .25s;
        }

        .btn-submit:hover {
            opacity: .92;
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(81, 45, 168, .4);
        }

        .already-link {
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 12px;
        }

        .already-link a {
            color: var(--purple-main);
            font-weight: 600;
            text-decoration: none;
        }

        .already-link a:hover {
            text-decoration: underline;
        }

        .alert-success-box {
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            margin-bottom: 14px;
            border-left: 3px solid #43a047;
        }

        /* ─────────────────────────────────────────
           RESPONSIVE — ≤ 700px (mobile/tablet)
        ───────────────────────────────────────── */
        @media (max-width: 700px) {

            /* Body: biarkan scroll vertikal penuh */
            body {
                align-items: flex-start;
                padding: 16px 12px 32px;
            }

            /* Kartu jadi kolom */
            .card-auth {
                flex-direction: column;
                border-radius: 22px;
            }

            /* Panel visual jadi header compact di atas */
            .panel-visual {
                width: 100%;
                border-radius: 22px 22px 0 0;
                padding: 22px 20px;
                /* Arah baris: logo + teks berdampingan */
                flex-direction: row;
                align-items: center;
                justify-content: flex-start;
                text-align: left;
            }

            .pv-content {
                flex-direction: row;
                align-items: center;
                gap: 14px;
                text-align: left;
            }

            .shop-logo {
                width: 50px;
                height: 50px;
                border-radius: 13px;
                margin-bottom: 0;
                flex-shrink: 0;
            }

            /* Sembunyikan nama toko di mobile — hemat tempat */
            .shop-name {
                display: none;
            }

            .panel-texts {
                width: auto;
                flex: 1;
                min-width: 0;
            }

            .panel-text-item {
                align-items: flex-start;
                gap: 4px;
            }

            .panel-text-item h2 {
                font-size: 14px;
            }

            .panel-text-item p {
                font-size: 11px;
                max-width: 100%;
            }

            .btn-switch {
                font-size: 10px;
                padding: 6px 14px;
                margin-top: 8px;
            }

            .paw-decor.tr {
                font-size: 55px;
            }

            .paw-decor.bl {
                display: none;
            }

            /* Panel form */
            .panel-form {
                border-radius: 0 0 22px 22px;
                overflow: visible;
                /* biarkan konten tumbuh */
            }

            .auth-tabs {
                padding: 18px 18px 0;
                gap: 6px;
            }

            .auth-tab {
                font-size: 12px;
                padding: 8px 0;
                border-radius: 9px;
            }

            /* Di mobile: slides TIDAK pakai absolute — biarkan flow normal
               sehingga halaman bisa di-scroll */
            .slides-wrapper {
                position: static;
                min-height: unset;
                overflow: visible;
            }

            .form-slide {
                position: static !important;
                overflow-y: visible !important;
                transform: none !important;
                opacity: 0 !important;
                pointer-events: none !important;
                padding: 18px 18px 24px !important;
                /* display:none saat tidak aktif — toggle via JS */
                display: none;
                transition: none !important;
            }

            .form-slide.active {
                display: block !important;
                opacity: 1 !important;
                pointer-events: auto !important;
            }

            .form-slide h1 {
                font-size: 18px;
            }

            .form-slide .subtitle {
                font-size: 11.5px;
                margin-bottom: 16px;
            }

            .input-wrap input {
                padding: 11px 14px 11px 38px;
                font-size: 12.5px;
            }

            .btn-submit {
                padding: 12px;
                font-size: 12.5px;
            }
        }
    </style>
</head>

<body>

    @php
        $appImage = \App\Models\SettingApp::get('app_image');
        $appName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
        $titleLogin = \App\Models\SettingApp::get('auth_title_login', 'Selamat Datang Kembali!');
        $subLogin = \App\Models\SettingApp::get(
            'auth_subtitle_login',
            'Masuk ke akun Anda dan kelola toko dengan mudah.',
        );
        $titleReg = \App\Models\SettingApp::get('auth_title_register', 'Bergabung Bersama Kami!');
        $subReg = \App\Models\SettingApp::get(
            'auth_subtitle_register',
            'Daftarkan akun dan mulai perjalanan bersama kami.',
        );
        $hasImage = $appImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($appImage);
        $imgUrl = $hasImage ? Storage::url($appImage) : null;
        $activeTab = $errors->has('name') || $errors->has('password_confirmation') ? 'register' : 'login';
    @endphp

    <div class="card-auth" id="cardAuth">

        {{-- ══ PANEL KIRI ══ --}}
        <div class="panel-visual">
            <div class="pv-bg"></div>
            @if ($hasImage)
                <div class="pv-photo" style="background-image:url('{{ $imgUrl }}')"></div>
            @endif
            <div class="pv-overlay"></div>
            <i class="fas fa-paw paw-decor tr"></i>
            <i class="fas fa-paw paw-decor bl"></i>

            <div class="pv-content">
                {{-- Logo --}}
                <div class="shop-logo" style="flex-shrink:0">
                    @if ($hasImage)
                        <img src="{{ $imgUrl }}" alt="{{ $appName }}">
                    @else
                        <i class="fas fa-cat"></i>
                    @endif
                </div>

                {{-- Teks --}}
                <div style="flex:1; min-width:0;">
                    <div class="shop-name">{{ $appName }}</div>

                    <div class="panel-texts">
                        <div class="panel-text-item {{ $activeTab === 'login' ? 'active' : '' }}" id="ptLogin">
                            <h2>{{ $titleLogin }}</h2>
                            <p>{{ $subLogin }}</p>
                            <button class="btn-switch" id="btnSwitchToReg">
                                <i class="fas fa-user-plus" style="margin-right:4px"></i>Daftar Sekarang
                            </button>
                        </div>
                        <div class="panel-text-item {{ $activeTab === 'register' ? 'active' : '' }}" id="ptReg">
                            <h2>{{ $titleReg }}</h2>
                            <p>{{ $subReg }}</p>
                            <button class="btn-switch" id="btnSwitchToLogin">
                                <i class="fas fa-sign-in-alt" style="margin-right:4px"></i>Sudah Punya Akun?
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ PANEL KANAN ══ --}}
        <div class="panel-form">

            <div class="auth-tabs">
                <div class="auth-tab {{ $activeTab === 'login' ? 'active' : '' }}" id="tabLogin"
                    onclick="switchTab('login')">
                    <i class="fas fa-sign-in-alt" style="margin-right:4px"></i>Masuk
                </div>
                <div class="auth-tab {{ $activeTab === 'register' ? 'active' : '' }}" id="tabReg"
                    onclick="switchTab('register')">
                    <i class="fas fa-user-plus" style="margin-right:4px"></i>Daftar
                </div>
            </div>

            <div class="slides-wrapper">

                {{-- Form Login --}}
                <div class="form-slide {{ $activeTab === 'login' ? 'active' : '' }}" id="slideLogin">
                    <h1>Selamat Datang</h1>
                    <p class="subtitle">Masuk menggunakan email &amp; password Anda</p>

                    @if (session('success'))
                        <div class="alert-success-box">
                            <i class="fas fa-check-circle" style="margin-right:5px"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="input-wrap">
                            <i class="fas fa-envelope ico"></i>
                            <input type="email" name="email" placeholder="Alamat Email" value="{{ old('email') }}"
                                class="{{ $errors->has('email') && $activeTab === 'login' ? 'is-invalid' : '' }}"
                                autocomplete="email" autofocus>
                        </div>
                        @if ($activeTab === 'login')
                            @error('email')
                                <span class="error-text"><i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}</span>
                            @enderror
                        @endif

                        <div class="input-wrap">
                            <i class="fas fa-lock ico"></i>
                            <input type="password" name="password" placeholder="Password"
                                autocomplete="current-password">
                        </div>

                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-sign-in-alt" style="margin-right:7px"></i>Masuk
                        </button>
                    </form>
                </div>

                {{-- Form Register --}}
                <div class="form-slide {{ $activeTab === 'register' ? 'active' : '' }}" id="slideReg">
                    <h1>Buat Akun Baru</h1>
                    <p class="subtitle">Isi data diri Anda untuk mendaftar</p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="input-wrap">
                            <i class="fas fa-user ico"></i>
                            <input type="text" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}"
                                class="{{ $errors->has('name') ? 'is-invalid' : '' }}" autocomplete="name">
                        </div>
                        @error('name')
                            <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror

                        <div class="input-wrap">
                            <i class="fas fa-envelope ico"></i>
                            <input type="email" name="email" placeholder="Alamat Email" value="{{ old('email') }}"
                                autocomplete="email">
                        </div>

                        <div class="input-wrap">
                            <i class="fas fa-lock ico"></i>
                            <input type="password" name="password" placeholder="Password"
                                class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                                autocomplete="new-password">
                        </div>
                        @error('password')
                            <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror

                        <div class="input-wrap">
                            <i class="fas fa-lock ico"></i>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                                class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn-submit" style="margin-top:6px">
                            <i class="fas fa-user-plus" style="margin-right:7px"></i>Daftar Sekarang
                        </button>

                        {{-- Link masuk — di BAWAH tombol, selalu terlihat --}}
                        <p class="already-link">
                            Sudah punya akun?
                            <a href="#" onclick="switchTab('login'); return false;">Masuk di sini</a>
                        </p>
                    </form>
                </div>

            </div>{{-- /slides-wrapper --}}
        </div>{{-- /panel-form --}}
    </div>

    <script>
        function switchTab(tab) {
            const isLogin = tab === 'login';

            document.getElementById('tabLogin').classList.toggle('active', isLogin);
            document.getElementById('tabReg').classList.toggle('active', !isLogin);
            document.getElementById('slideLogin').classList.toggle('active', isLogin);
            document.getElementById('slideReg').classList.toggle('active', !isLogin);
            document.getElementById('ptLogin').classList.toggle('active', isLogin);
            document.getElementById('ptReg').classList.toggle('active', !isLogin);

            // Scroll ke atas saat switch di mobile
            if (window.innerWidth <= 700) {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }

        document.getElementById('btnSwitchToReg').addEventListener('click', () => switchTab('register'));
        document.getElementById('btnSwitchToLogin').addEventListener('click', () => switchTab('login'));
    </script>

</body>

</html>
