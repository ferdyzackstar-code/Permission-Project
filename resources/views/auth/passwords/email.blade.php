<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | {{ \App\Models\SettingApp::get('app_name', 'Anda Petshop') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(to right, #e2e2e2, #c9d6ff);
        }

        .wrapper {
            display: flex;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            width: 640px;
            max-width: 100%;
            min-height: 360px;
        }

        /* Panel kiri: foto / gradient ungu */
        .panel-left {
            width: 45%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            padding: 30px;
            color: #fff;
            background: linear-gradient(to bottom, #5c6bc0, #512da8);
        }

        .panel-left.has-image {
            background-size: cover;
            background-position: center;
        }

        .panel-left .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(92, 107, 192, 0.83), rgba(81, 45, 168, 0.87));
        }

        .panel-left .content {
            position: relative;
            z-index: 1;
        }

        .panel-left h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .panel-left p {
            font-size: 13px;
            opacity: .9;
            line-height: 1.5;
        }

        /* Panel kanan: form */
        .panel-right {
            width: 55%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 35px;
            flex-direction: column;
        }

        .panel-right h1 {
            font-size: 22px;
            margin-bottom: 8px;
            color: #333;
        }

        .panel-right p {
            font-size: 13px;
            color: #888;
            margin-bottom: 20px;
        }

        input {
            background: #eee;
            border: none;
            padding: 11px 15px;
            font-size: 13px;
            border-radius: 8px;
            width: 100%;
            outline: none;
            margin-bottom: 10px;
        }

        button {
            background: #512da8;
            color: #fff;
            font-size: 12px;
            padding: 11px 0;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            width: 100%;
            cursor: pointer;
            margin-top: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 15px;
            width: 100%;
            text-align: center;
        }

        .back-link {
            display: block;
            margin-top: 18px;
            font-size: 13px;
            color: #512da8;
            text-decoration: none;
            font-weight: 600;
        }

        .error-msg {
            color: red;
            font-size: 11px;
            display: block;
            margin-bottom: 6px;
        }
    </style>
</head>

<body>

    @php
        $appImage = \App\Models\SettingApp::get('app_image');
        $appName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
        $hasImage = $appImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($appImage);
    @endphp

    <div class="wrapper">
        {{-- Panel kiri --}}
        <div class="panel-left {{ $hasImage ? 'has-image' : '' }}"
            @if ($hasImage) style="background-image:url('{{ Storage::url($appImage) }}')" @endif>
            @if ($hasImage)
                <div class="overlay"></div>
            @endif
            <div class="content">
                <i class="fas fa-paw fa-3x mb-3" style="opacity:.85"></i>
                <h2>{{ $appName }}</h2>
                <p>Masukkan email Anda dan kami akan mengirimkan link reset password.</p>
            </div>
        </div>

        {{-- Panel kanan: form --}}
        <div class="panel-right">
            <h1>Lupa Password?</h1>
            <p>Kami kirimkan link reset ke email Anda</p>

            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" style="width:100%">
                @csrf
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Alamat Email" required
                    autocomplete="email" autofocus class="@error('email') is-invalid @enderror">
                @error('email')
                    <span class="error-msg">{{ $message }}</span>
                @enderror

                <button type="submit">Kirim Link Reset</button>

                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Login
                </a>
            </form>
        </div>
    </div>

</body>

</html>
