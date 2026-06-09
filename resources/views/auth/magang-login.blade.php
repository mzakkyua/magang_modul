<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SINAKERTRANS</title>

    {{-- Implementasi Favicon Auto Cache-Buster --}}
    <link rel="icon" type="image/svg+xml"
        href="{{ asset('assets/images/favicon.svg') }}?v={{ file_exists(public_path('assets/images/favicon.svg')) ? filemtime(public_path('assets/images/favicon.svg')) : '1' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-white min-h-screen">

    <div class="min-h-screen flex">

        {{-- ===================== PANEL KIRI ===================== --}}
        <div class="hidden lg:flex lg:w-[55%] flex-col justify-between p-12 bg-white">

            {{-- Logo --}}
            <a href="{{ route('landing.index') }}" class="flex items-center gap-2.5">
                <div
                    class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow-md shadow-blue-600/20 overflow-hidden">
                    {{-- Implementasi Favicon Auto Cache-Buster pada Logo Kiri --}}
                    <img src="{{ asset('assets/images/favicon.svg') }}?v={{ file_exists(public_path('assets/images/favicon.svg')) ? filemtime(public_path('assets/images/favicon.svg')) : '1' }}"
                        alt="Logo" class="w-6 h-6 object-contain" />
                </div>
                <span class="font-extrabold text-gray-900 text-xl tracking-tight">SINAKERTRANS</span>
            </a>

            {{-- Konten tengah --}}
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 leading-snug mb-3">
                    Selangkah Lebih Dekat <br>
                    <span class="text-blue-600">Menuju Karir Impianmu</span>
                </h2>
                <p class="text-gray-500 text-sm mb-10 max-w-sm leading-relaxed">
                    Platform magang resmi Dinas Tenaga Kerja Provinsi Jawa Timur. Daftar, lamar, dan mulai magang hari
                    ini.
                </p>

                {{-- Banner Card --}}
                <div class="rounded-2xl overflow-hidden shadow-md shadow-gray-200/80 border border-gray-100">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b2/New_office.jpg" alt="Ilustrasi Magang"
                        class="w-full h-60 object-cover object-center">
                    <div
                        class="bg-linear-to-r from-blue-700 to-blue-500 px-5 py-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-white font-bold text-sm">Program Magang Jawa Timur</p>
                            <p class="text-blue-200 text-xs mt-0.5">Dinas Tenaga Kerja & Transmigrasi</p>
                        </div>
                        <a href="{{ route('landing.index') }}"
                            class="shrink-0 bg-white text-blue-600 text-xs font-bold px-4 py-1.5 rounded-full hover:bg-blue-50 transition">
                            Lihat Lowongan →
                        </a>
                    </div>
                </div>
            </div>

            {{-- Footer panel kiri --}}
            <p class="text-gray-400 text-xs">© {{ date('Y') }} Disnakertrans Provinsi Jawa Timur</p>

        </div>

        {{-- Garis pemisah --}}
        <div class="hidden lg:block w-px bg-gray-100 self-stretch"></div>

        {{-- ===================== PANEL KANAN: FORM ===================== --}}
        <div class="flex-1 flex items-center justify-center px-8 py-12 bg-white">
            <div class="w-full max-w-sm">

                {{-- Header mobile (hanya muncul di layar kecil) --}}
                <div class="lg:hidden mb-8">
                    <a href="{{ route('landing.index') }}" class="flex items-center gap-2.5 mb-1">
                        <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center overflow-hidden">
                            {{-- Implementasi Favicon Auto Cache-Buster pada Logo Mobile --}}
                            <img src="{{ asset('assets/images/favicon.svg') }}?v={{ file_exists(public_path('assets/images/favicon.svg')) ? filemtime(public_path('assets/images/favicon.svg')) : '1' }}"
                                alt="Logo" class="w-6 h-6 object-contain" />
                        </div>
                        <span class="font-extrabold text-gray-900 text-xl tracking-tight">SINAKERTRANS</span>
                    </a>
                </div>

                {{-- Form heading --}}
                <div class="mb-7">
                    <h1 class="text-xl font-extrabold text-gray-900 mb-1">Masuk ke Akun</h1>
                    <p class="text-gray-500 text-sm">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Daftar
                            sekarang</a>
                    </p>
                </div>

                {{-- ================= ERROR ALERT ================= --}}
                @if ($errors->any())
                    <div class="flex items-start gap-3 bg-red-50 border border-red-100 rounded-xl p-3.5 mb-5">
                        <i class="bi bi-exclamation-circle-fill text-red-400 mt-0.5 shrink-0 text-sm"></i>
                        <div>
                            <p class="font-semibold text-red-700 text-sm">Login Gagal</p>
                            <p class="text-red-500 text-xs mt-0.5">{{ $errors->first() }}</p>
                        </div>
                    </div>
                @endif

                {{-- ================= FORM LOGIN ================= --}}
                <form action="{{ route('login.post') }}" method="POST" id="loginForm" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <x-input-field name="email" label="Email" type="email" placeholder="nama@email.com" required
                        autocomplete="email">
                        <x-slot:icon>
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                                <path
                                    d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                            </svg>
                        </x-slot:icon>
                    </x-input-field>

                    {{-- Password --}}
                    <div>
                        <x-input-field name="password" label="Password" type="password" placeholder="••••••••" required
                            autocomplete="current-password">
                            <x-slot:icon>
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </x-slot:icon>
                        </x-input-field>

                        {{-- Lupa Password --}}
                        <div class="flex items-center justify-between mt-2">
                            <a href="{{ route('admin.password.request') }}"
                                class="text-xs text-gray-400 hover:text-blue-600 hover:underline font-medium transition-colors">
                                Lupa Password Admin?
                            </a>
                            <a href="{{ route('password.request') }}"
                                class="text-xs text-blue-600 hover:underline font-medium">
                                Lupa Password?
                            </a>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="submitBtn"
                        class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 active:scale-95 transition-all text-sm shadow-sm shadow-blue-600/20">
                        Masuk
                    </button>

                </form>

                {{-- Kembali --}}
                <div class="mt-8 text-center">
                    <a href="{{ route('landing.index') }}"
                        class="text-xs text-gray-400 hover:text-gray-600 flex items-center justify-center gap-1.5 transition">
                        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>

            </div>
        </div>

    </div>

</body>

</html>
