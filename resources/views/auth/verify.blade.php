<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | {{ \App\Models\SettingApp::get('app_name', 'Anda Petshop') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@700;800&family=Poppins:wght@300;400;500;600&display=swap');

        :root {
            --purple-dark: #3b1f8c;
            --purple-main: #512da8;
            --purple-mid: #6a3fc4;
            --purple-light: #7c5cbf;
            --input-bg: #eeedf5;
            --text-dark: #2d2640;
            --text-muted: #7a7290;
            --white: #ffffff;
            --shadow: 0 20px 60px rgba(81, 45, 168, .18);
            --radius-card: 28px;
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

        .card-verify {
            background: var(--white);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow);
            width: 480px;
            max-width: 100%;
            overflow: hidden;
        }

        /* Header ungu */
        .card-header {
            position: relative;
            padding: 40px 32px 36px;
            text-align: center;
            color: var(--white);
            overflow: hidden;
        }

        .card-header .bg-layer {
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, var(--purple-mid), var(--purple-dark));
        }

        .card-header .bg-photo {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
        }

        .card-header .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(59, 31, 140, .78), rgba(81, 45, 168, .72));
        }

        .card-header .content {
            position: relative;
            z-index: 2;
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

        .shop-logo {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .15);
            border: 2px solid rgba(255, 255, 255, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            overflow: hidden;
        }

        .shop-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .shop-logo i {
            font-size: 24px;
            color: rgba(255, 255, 255, .9);
        }

        .shop-name {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .email-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
        }

        .email-icon i {
            font-size: 26px;
        }

        .card-header h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 22px;
            font-weight: 800;
        }

        .card-header p {
            font-size: 13px;
            opacity: .85;
            margin-top: 6px;
        }

        /* Body kartu */
        .card-body {
            padding: 36px 36px 40px;
        }

        .info-box {
            background: #ede7f6;
            border-left: 4px solid var(--purple-main);
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 13px;
            color: var(--text-dark);
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .alert-success-box {
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 3px solid #43a047;
        }

        .btn-resend {
            width: 100%;
            background: linear-gradient(135deg, var(--purple-mid), var(--purple-dark));
            color: var(--white);
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: opacity .25s, transform .2s, box-shadow .25s;
            box-shadow: 0 6px 20px rgba(81, 45, 168, .35);
            margin-bottom: 16px;
        }

        .btn-resend:hover {
            opacity: .92;
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(81, 45, 168, .4);
        }

        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: color .2s;
        }

        .logout-link:hover {
            color: #e53935;
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

    <div class="card-verify">

        {{-- Header --}}
        <div class="card-header">
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

                <div class="email-icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <h1>Verifikasi Email</h1>
                <p>Satu langkah lagi untuk masuk!</p>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">

            @if (session('resent'))
                <div class="alert-success-box">
                    <i class="fas fa-check-circle" style="margin-right:6px"></i>
                    Link verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            @endif

            <div class="info-box">
                <i class="fas fa-info-circle" style="color:var(--purple-main);margin-right:6px"></i>
                Sebelum melanjutkan, silakan cek email Anda untuk tautan verifikasi.
                Jika Anda belum menerima email tersebut, klik tombol di bawah untuk mengirim ulang.
            </div>

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn-resend">
                    <i class="fas fa-paper-plane" style="margin-right:8px"></i>Kirim Ulang Email Verifikasi
                </button>
            </form>

            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-link">
                <i class="fas fa-sign-out-alt fa-sm"></i> Keluar dari akun ini
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>

        </div>
    </div>

</body>

</html>
