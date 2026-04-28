<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Password | {{ \App\Models\SettingApp::get('app_name', 'Anda Petshop') }}</title>
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

        .card-confirm {
            background: var(--white);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow);
            width: 460px;
            max-width: 100%;
            overflow: hidden;
        }

        .card-header {
            position: relative;
            padding: 36px 32px 32px;
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
            font-size: 50px;
            transform: rotate(-20deg);
        }

        .shop-logo {
            width: 58px;
            height: 58px;
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
            font-size: 22px;
            color: rgba(255, 255, 255, .9);
        }

        .shop-name {
            font-family: 'Nunito', sans-serif;
            font-size: 17px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
        }

        .icon-box i {
            font-size: 22px;
        }

        .card-header h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 20px;
            font-weight: 800;
        }

        .card-header p {
            font-size: 12.5px;
            opacity: .85;
            margin-top: 6px;
            line-height: 1.5;
        }

        .card-body {
            padding: 32px 36px 36px;
        }

        .info-box {
            background: #ede7f6;
            border-left: 4px solid var(--purple-main);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: var(--text-dark);
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 14px;
        }

        .input-wrap i.ico {
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
            border-radius: 12px;
            padding: 12px 38px 12px 40px;
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

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #b0aac8;
            cursor: pointer;
            font-size: 14px;
            transition: color .2s;
        }

        .toggle-pw:hover {
            color: var(--purple-main);
        }

        .error-text {
            font-size: 11px;
            color: #e53935;
            margin-top: -10px;
            margin-bottom: 10px;
            padding-left: 4px;
            display: block;
        }

        .btn-submit {
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
            margin-bottom: 14px;
        }

        .btn-submit:hover {
            opacity: .92;
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(81, 45, 168, .4);
        }

        .forgot-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12.5px;
            color: var(--purple-main);
            text-decoration: none;
            font-weight: 600;
            transition: opacity .2s;
        }

        .forgot-link:hover {
            opacity: .75;
            text-decoration: underline;
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

    <div class="card-confirm">

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

                <div class="icon-box">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1>Konfirmasi Password</h1>
                <p>Verifikasi identitas Anda sebelum melanjutkan</p>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">
            <div class="info-box">
                <i class="fas fa-lock" style="color:var(--purple-main);margin-right:6px"></i>
                Untuk keamanan akun, masukkan password Anda terlebih dahulu sebelum mengakses halaman ini.
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                <div class="input-wrap">
                    <i class="fas fa-lock ico"></i>
                    <input type="password" id="confirmPw" name="password" placeholder="Masukkan Password Anda" required
                        autocomplete="current-password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                    <i class="fas fa-eye toggle-pw" onclick="togglePw()"></i>
                </div>
                @error('password')
                    <span class="error-text"><i class="fas fa-exclamation-circle"
                            style="margin-right:3px"></i>{{ $message }}</span>
                @enderror

                <button type="submit" class="btn-submit">
                    <i class="fas fa-unlock-alt" style="margin-right:8px"></i>Konfirmasi & Lanjutkan
                </button>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        <i class="fas fa-question-circle fa-sm"></i> Lupa Password?
                    </a>
                @endif
            </form>
        </div>
    </div>

    <script>
        function togglePw() {
            const inp = document.getElementById('confirmPw');
            const icon = document.querySelector('.toggle-pw');
            const show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !show);
            icon.classList.toggle('fa-eye-slash', show);
        }
    </script>
</body>

</html>
