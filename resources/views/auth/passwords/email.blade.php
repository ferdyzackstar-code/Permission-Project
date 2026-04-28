<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | {{ \App\Models\SettingApp::get('app_name', 'Anda Petshop') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@700;800&family=Poppins:wght@300;400;500;600&display=swap');

        :root {
            --purple-dark: #3b1f8c;
            --purple-main: #512da8;
            --purple-mid: #6a3fc4;
            --purple-light: #7c5cbf;
            --purple-soft: #ede7f6;
            --input-bg: #eeedf5;
            --text-dark: #2d2640;
            --text-muted: #7a7290;
            --white: #ffffff;
            --shadow: 0 20px 60px rgba(81, 45, 168, .18);
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
            background: #f4f4f8;
            background-image:
                radial-gradient(circle at 15% 50%, rgba(81, 45, 168, .08) 0%, transparent 50%),
                radial-gradient(circle at 85% 20%, rgba(255, 143, 0, .06) 0%, transparent 40%);
            padding: 20px;
        }

        .card-auth {
            display: flex;
            background: var(--white);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow);
            width: 700px;
            max-width: 100%;
            min-height: 420px;
            overflow: hidden;
        }

        /* Panel kiri */
        .panel-left {
            width: 42%;
            flex-shrink: 0;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 36px 28px;
            text-align: center;
            color: var(--white);
            overflow: hidden;
        }

        .panel-left .bg-layer {
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, var(--purple-mid), var(--purple-dark));
        }

        .panel-left .bg-photo {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
        }

        .panel-left .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(59, 31, 140, .78), rgba(81, 45, 168, .72));
        }

        .panel-left .content {
            position: relative;
            z-index: 2;
        }

        .shop-logo {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .15);
            border: 2px solid rgba(255, 255, 255, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            overflow: hidden;
        }

        .shop-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .shop-logo i {
            font-size: 26px;
            color: rgba(255, 255, 255, .9);
        }

        .shop-name {
            font-family: 'Nunito', sans-serif;
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .paw-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
        }

        .paw-icon i {
            font-size: 22px;
        }

        .panel-left h2 {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .panel-left p {
            font-size: 12.5px;
            opacity: .85;
            line-height: 1.6;
        }

        .paw-decor {
            position: absolute;
            opacity: .07;
            font-size: 70px;
            color: var(--white);
            pointer-events: none;
        }

        .paw-decor.tr {
            top: -15px;
            right: -15px;
            transform: rotate(25deg);
        }

        .paw-decor.bl {
            bottom: -15px;
            left: -15px;
            transform: rotate(-20deg);
            font-size: 50px;
        }

        /* Panel kanan */
        .panel-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 44px 40px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 24px;
            font-weight: 500;
            transition: color .2s;
        }

        .back-link:hover {
            color: var(--purple-main);
        }

        .panel-right h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .panel-right .subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 14px;
        }

        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--purple-light);
            font-size: 14px;
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
            margin-top: -10px;
            margin-bottom: 10px;
            padding-left: 4px;
            display: block;
        }

        .alert-success-box {
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 3px solid #43a047;
        }

        .btn-submit {
            width: 100%;
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
            transition: opacity .25s, transform .2s, box-shadow .25s;
            box-shadow: 0 6px 20px rgba(81, 45, 168, .35);
            margin-top: 4px;
        }

        .btn-submit:hover {
            opacity: .92;
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(81, 45, 168, .4);
        }

        @media (max-width: 600px) {
            .card-auth {
                flex-direction: column;
            }

            .panel-left {
                width: 100%;
                min-height: 180px;
            }

            .panel-right {
                padding: 28px 24px;
            }
        }
    </style>
</head>

<body>

    @php
        $appImage = \App\Models\SettingApp::get('app_image');
        $appName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
        $hasImage = $appImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($appImage);
        $imgUrl = $hasImage ? Storage::url($appImage) : null;
    @endphp

    <div class="card-auth">

        {{-- Panel kiri --}}
        <div class="panel-left">
            <div class="bg-layer"></div>
            @if ($hasImage)
                <div class="bg-photo" style="background-image:url('{{ $imgUrl }}')"></div>
            @endif
            <div class="overlay"></div>
            <i class="fas fa-paw paw-decor tr"></i>
            <i class="fas fa-paw paw-decor bl"></i>

            <div class="content">
                <div class="shop-logo">
                    @if ($hasImage)
                        <img src="{{ $imgUrl }}" alt="{{ $appName }}">
                    @else
                        <i class="fas fa-cat"></i>
                    @endif
                </div>
                <div class="shop-name">{{ $appName }}</div>

                <div class="paw-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Reset Password</h2>
                <p>Masukkan email terdaftar Anda, kami akan mengirimkan link untuk reset password.</p>
            </div>
        </div>

        {{-- Panel kanan --}}
        <div class="panel-right">
            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Halaman Login
            </a>

            <h1>Lupa Password?</h1>
            <p class="subtitle">Tenang, kami bantu reset password Anda</p>

            @if (session('status'))
                <div class="alert-success-box">
                    <i class="fas fa-check-circle" style="margin-right:6px"></i>{{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="Masukkan alamat email Anda" class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        required autocomplete="email" autofocus>
                </div>
                @error('email')
                    <span class="error-text"><i class="fas fa-exclamation-circle"
                            style="margin-right:3px"></i>{{ $message }}</span>
                @enderror

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane" style="margin-right:8px"></i>Kirim Link Reset
                </button>
            </form>
        </div>
    </div>

</body>

</html>
