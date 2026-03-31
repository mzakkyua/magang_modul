<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- BUGFIX: ditambahkan content="{{ csrf_token() }}" yang hilang --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - @yield('title', 'Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-100 antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- ===================== SIDEBAR ===================== --}}
        @include('partials.sidebar')

        {{-- ===================== MAIN AREA ===================== --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- ===================== HEADER / TOPBAR ===================== --}}
            <header
                class="h-16 flex items-center justify-between px-6 bg-white border-b border-slate-100 shadow-sm shrink-0">

                {{-- Kiri: Judul halaman + breadcrumb --}}
                <div>
                    <h2 class="text-base font-bold text-gray-900 leading-tight">
                        @yield('title', 'Dashboard')
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ Auth::user()->name }}
                        <span class="mx-1">•</span>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                {{-- Kanan: Aksi header (bisa di-push dari halaman child) --}}
                <div class="flex items-center gap-3">
                    @stack('header_actions')

                    {{-- Notifikasi (placeholder) --}}
                    <button
                        class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition text-slate-500 hover:text-slate-700">
                        <i class="bi bi-bell text-base"></i>
                    </button>

                    {{-- Avatar kecil di topbar --}}
                    <div
                        class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center border-2 border-blue-200 overflow-hidden shrink-0">
                        @if (Auth::user()->profile_photo_path)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                class="w-full h-full object-cover" alt="Avatar">
                        @else
                            <i class="bi bi-person-fill text-blue-500 text-sm"></i>
                        @endif
                    </div>
                </div>

            </header>

            {{-- ===================== MAIN CONTENT ===================== --}}
            <main class="flex-1 overflow-y-auto bg-slate-100 p-6">

                {{-- Flash message: Success --}}
                @if (session('success'))
                    <div
                        class="mb-5 flex items-start gap-3 p-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-2xl">
                        <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                        <div>
                            <span class="font-bold block">Berhasil!</span>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                {{-- Flash message: Error --}}
                @if (session('error'))
                    <div
                        class="mb-5 flex items-start gap-3 p-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-2xl">
                        <i class="bi bi-exclamation-circle-fill text-red-400 mt-0.5 shrink-0"></i>
                        <div>
                            <span class="font-bold block">Terjadi Kesalahan!</span>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @yield('content')

            </main>

        </div>
    </div>

    @stack('scripts')

</body>

</html>
