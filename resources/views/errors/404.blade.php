<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Not Found | Ferdy Blog</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fc; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-code { font-size: 10rem; font-weight: 700; color: #4e73df; line-height: 1; }
        .error-msg { font-size: 1.5rem; color: #5a5c69; margin-bottom: 2rem; }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="error-code">404</div>
        <p class="error-msg">Aduh! Halamannya nggak ada di Anda PetSHop.</p>
        <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg shadow">Kembali ke Beranda</a>
    </div>
</body>
</html>