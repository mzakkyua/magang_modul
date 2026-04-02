<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SINAKERTRANS - Cari Magang')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="bg-gray-50 font-sans text-gray-800">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('dashboard.index') }}"
                class="flex items-center text-gray-600 hover:text-blue-600 transition">
                <i class="bi bi-arrow-left mr-2"></i> Kembali
            </a>
            <span class="font-bold text-blue-600 text-xl">SINAKERTRANS</span>
        </div>
    </nav>

    @yield('content')
</body>

</html>
