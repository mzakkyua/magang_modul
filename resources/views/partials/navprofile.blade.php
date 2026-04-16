<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SINAKERTRANS - Profil')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    {{-- BUGFIX: Plus Jakarta Sans ditambahkan agar font konsisten dengan seluruh sistem --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    {{-- ===================== NAVBAR ===================== --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex justify-between items-center">

            {{-- Tombol Kembali --}}
            <a href="{{ route('dashboard.index') }}"
                class="flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-blue-600 transition group">
                <span
                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 group-hover:bg-blue-50 transition">
                    <i class="bi bi-arrow-left text-sm"></i>
                </span>
                Kembali
            </a>

            {{-- Logo --}}
            <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2">
                <div
                    class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm shadow-blue-600/30">
                    <i class="bi bi-briefcase-fill text-white text-xs"></i>
                </div>
                <span class="font-extrabold text-blue-600 text-lg tracking-tight">SINAKERTRANS</span>
            </a>

        </div>
    </nav>

    {{-- ===================== CONTENT ===================== --}}
    @yield('content')

</body>

</html>
