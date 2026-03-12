<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error @yield('code') - PetShop Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .error-card {
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .error-code {
            font-size: 80px;
            font-weight: bold;
            color: #ff6b6b;
        }
    </style>
</head>

<body>

    <div class="error-card">
        <div class="error-code">419</div>
        <h2 class="mb-4">Sesi Anda Telah Berakhir</h2>
        <p class="text-muted mb-5">Halaman ini sudah kadaluarsa karena terlalu lama tidak ada aktivitas. Silakan segarkan
            halaman.</p>

        <a href="/Dashboard" class="btn btn-primary btn-lg px-5 shadow">
            Kembali ke Dashboard
        </a>
    </div>

</body>

</html>
