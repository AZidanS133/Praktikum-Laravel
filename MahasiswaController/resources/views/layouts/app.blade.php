<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Web App</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('mahasiswa.index') }}">Daftar Mahasiswa</a>
        <a href="{{ route('kalkulator.segitiga') }}">Kalkulator Segitiga</a>
    </nav>
    <main class="container">
        @yield('content')
    </main>
</body>
</html>