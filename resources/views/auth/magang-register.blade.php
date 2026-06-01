<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SINAKERTRANS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth.js'])

    {{-- Google Fonts --}}
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

<body class="bg-slate-50 min-h-screen m-0 p-0 overflow-x-hidden">

    {{-- ========================================================= --}}
    {{-- WRAPPER --}}
    {{-- ========================================================= --}}
    <div class="min-h-screen flex items-start">

        {{-- ========================================================= --}}
        {{-- LEFT PANEL --}}
        {{-- ========================================================= --}}
        <div
            class="hidden lg:flex lg:w-5/12 xl:w-[42%] bg-linear-to-br from-blue-900 via-blue-800 to-blue-700 sticky overflow-hidden flex-col justify-between p-12 top-0 h-screen">
            {{-- Decorative Circles --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3">
            </div>

            <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/5 rounded-full translate-y-1/3 -translate-x-1/4">
            </div>

            <div class="absolute top-1/2 right-0 w-48 h-48 bg-blue-400/20 rounded-full translate-x-1/2 blur-3xl">
            </div>

            {{-- Logo --}}
            <div class="relative z-10">
                <a href="{{ route('landing.index') }}" class="flex items-center gap-3 mb-3">
                    <div
                        class="w-11 h-11 bg-white/15 rounded-2xl flex items-center justify-center border border-white/20">
                        <i class="bi bi-briefcase-fill text-white text-lg"></i>
                    </div>

                    <span class="text-white font-extrabold text-2xl tracking-tight">
                        SINAKERTRANS
                    </span>
                </a>

                <p class="text-blue-200 text-sm ml-14">
                    Dinas Tenaga Kerja & Transmigrasi
                </p>
            </div>

            {{-- Middle Content --}}
            <div class="relative z-10">

                <span
                    class="inline-flex items-center gap-2 bg-white/10 border border-white/15 text-blue-100 text-xs font-semibold px-4 py-2 rounded-full mb-5">
                    <span class="w-2 h-2 bg-blue-300 rounded-full animate-pulse"></span>
                    Pendaftaran Gratis
                </span>

                <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">
                    Daftar & Mulai <br>
                    <span class="text-blue-300">Perjalananmu</span>
                </h1>

                <p class="text-blue-100 text-sm leading-relaxed max-w-sm mb-8">
                    Buat akun dalam hitungan menit dan akses ratusan peluang magang di
                    instansi pemerintah Jawa Timur.
                </p>

                {{-- Steps --}}
                <div class="space-y-4">

                    <div
                        class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4 backdrop-blur-sm">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-400/20 border border-blue-300/20 flex items-center justify-center shrink-0">
                            <span class="text-blue-100 font-bold">1</span>
                        </div>

                        <div>
                            <p class="text-white text-sm font-semibold">Buat Akun</p>
                            <p class="text-blue-200 text-xs">Isi data diri & password</p>
                        </div>
                    </div>

                    <div
                        class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4 backdrop-blur-sm">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-400/20 border border-blue-300/20 flex items-center justify-center shrink-0">
                            <span class="text-blue-100 font-bold">2</span>
                        </div>

                        <div>
                            <p class="text-white text-sm font-semibold">Lengkapi Profil</p>
                            <p class="text-blue-200 text-xs">Upload CV & foto diri</p>
                        </div>
                    </div>

                    <div
                        class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4 backdrop-blur-sm">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-400/20 border border-blue-300/20 flex items-center justify-center shrink-0">
                            <span class="text-blue-100 font-bold">3</span>
                        </div>

                        <div>
                            <p class="text-white text-sm font-semibold">Lamar Posisi</p>
                            <p class="text-blue-200 text-xs">Pilih divisi favoritmu</p>
                        </div>
                    </div>

                </div>

            </div>

            {{-- Footer --}}
            <div class="relative z-10">
                <p class="text-blue-300 text-xs">
                    Provinsi Jawa Timur — SINAKERTRANS
                </p>
            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- RIGHT PANEL --}}
        {{-- ========================================================= --}}
        <div class="flex-1 flex items-center justify-center px-6 py-10 bg-slate-50">

            <div class="w-full max-w-md">

                {{-- Mobile Header --}}
                <div class="lg:hidden mb-8">
                    <a href="{{ route('landing.index') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-2xl flex items-center justify-center">
                            <i class="bi bi-briefcase-fill text-white"></i>
                        </div>

                        <span class="font-extrabold text-xl text-gray-900">
                            SINAKERTRANS
                        </span>
                    </a>
                </div>

                {{-- Card --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8">

                    {{-- Heading --}}
                    <div class="mb-7">
                        <h2 class="text-2xl font-extrabold text-gray-900 mb-1">
                            Buat Akun Baru
                        </h2>

                        <p class="text-sm text-gray-500">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">
                                Masuk disini
                            </a>
                        </p>
                    </div>

                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-5">
                            <p class="text-red-600 text-sm font-semibold mb-2">
                                Terdapat kesalahan:
                            </p>

                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-500 text-xs">
                                        • {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORM --}}
                    <form action="{{ route('register.post') }}" method="POST" id="registerForm" class="space-y-4">

                        @csrf

                        <x-input-field name="nama_lengkap" label="Nama Lengkap" placeholder="Nama sesuai identitas" />

                        <x-input-field name="email" label="Email Aktif" placeholder="nama@email.com" />

                        <x-select-field name="education_level" label="Jenjang Pendidikan">

                            <option value="SMA">SMA</option>
                            <option value="SMK">SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>

                        </x-select-field>

                        <x-input-field name="password" type="password" label="Password"
                            placeholder="Minimal 8 karakter" />

                        <x-input-field name="password_confirmation" type="password" label="Ulangi Password"
                            placeholder="Ketik ulang password" />

                        {{-- Terms --}}
                        <label class="flex items-start gap-3 cursor-pointer pt-1">
                            <input type="checkbox" name="terms" required
                                class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                            <span class="text-sm text-gray-500 leading-relaxed">
                                Saya menyetujui
                                <a href="#" class="text-blue-600 font-semibold hover:underline">
                                    Syarat & Ketentuan
                                </a>
                                yang berlaku
                            </span>
                        </label>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-2xl transition-all shadow-lg shadow-blue-600/20 hover:-translate-y-0.5">
                            Buat Akun
                        </button>

                        {{-- Trust Badges --}}
                        <div class="grid grid-cols-3 gap-2 text-center pt-2 text-[11px] text-gray-400">

                            <div class="bg-slate-50 rounded-xl py-2">
                                ✓ Gratis
                            </div>

                            <div class="bg-slate-50 rounded-xl py-2">
                                ✓ Aman
                            </div>

                            <div class="bg-slate-50 rounded-xl py-2">
                                ✓ Cepat
                            </div>

                        </div>

                    </form>

                </div>

                {{-- Footer Link --}}
                <div class="mt-6 text-center">
                    <a href="{{ route('landing.index') }}"
                        class="text-xs text-gray-400 hover:text-gray-600 transition inline-flex items-center gap-1.5">
                        <i class="bi bi-arrow-left"></i>
                        Kembali ke Beranda
                    </a>
                </div>

            </div>

        </div>

    </div>

</body>

</html>
