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

<body class="bg-gray-50 min-h-screen">

    {{-- ===================== SPLIT LAYOUT ===================== --}}
    <div class="min-h-screen flex">

        {{-- ===================== PANEL KIRI: BRANDING ===================== --}}
        {{-- Panel ini hanya tampil di layar lg ke atas --}}
        <div
            class="hidden lg:flex lg:w-5/12 xl:w-[42%] bg-linear-to-br from-blue-900 via-blue-800 to-blue-700 relative overflow-hidden flex-col justify-between p-12 top-0 h-screen">

            {{-- Dekorasi background --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3">
            </div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/5 rounded-full translate-y-1/3 -translate-x-1/4">
            </div>
            <div class="absolute top-1/2 right-0 w-48 h-48 bg-blue-500/20 rounded-full translate-x-1/2 blur-2xl"></div>

            {{-- Logo --}}
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <div
                        class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center border border-white/20">
                        <i class="bi bi-briefcase-fill text-white text-lg"></i>
                    </div>
                    <span class="text-white font-extrabold text-2xl tracking-tight">SINAKERTRANS</span>
                </div>
                <p class="text-blue-200 text-sm ml-13">Dinas Tenaga Kerja & Transmigrasi</p>
            </div>

            {{-- Konten tengah --}}
            <div class="relative z-10 space-y-6">
                <div>
                    <span
                        class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-blue-200 text-xs font-semibold px-3 py-1.5 rounded-full mb-4">
                        <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
                        Pendaftaran Gratis
                    </span>
                    <h2 class="text-3xl font-extrabold text-white leading-tight mb-3">
                        Daftar & Mulai <br>
                        <span class="text-blue-300">Perjalananmu</span>
                    </h2>
                    <p class="text-blue-100 text-sm leading-relaxed max-w-xs">
                        Buat akun dalam hitungan menit dan langsung akses ratusan peluang magang di instansi pemerintah.
                    </p>
                </div>

                {{-- Step cards --}}
                <div class="space-y-3">
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4">
                        <div
                            class="w-9 h-9 bg-blue-500/30 rounded-xl flex items-center justify-center shrink-0 border border-blue-400/20">
                            <span class="text-blue-200 text-sm font-extrabold">1</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold">Buat Akun</p>
                            <p class="text-blue-200 text-xs">Isi data diri & buat password</p>
                        </div>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4">
                        <div
                            class="w-9 h-9 bg-blue-500/30 rounded-xl flex items-center justify-center shrink-0 border border-blue-400/20">
                            <span class="text-blue-200 text-sm font-extrabold">2</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold">Lengkapi Profil</p>
                            <p class="text-blue-200 text-xs">Upload CV & foto profil</p>
                        </div>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4">
                        <div
                            class="w-9 h-9 bg-blue-500/30 rounded-xl flex items-center justify-center shrink-0 border border-blue-400/20">
                            <span class="text-blue-200 text-sm font-extrabold">3</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold">Lamar Lowongan</p>
                            <p class="text-blue-200 text-xs">Pilih divisi & kirim lamaran</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer panel --}}
            <div class="relative z-10">
                <p class="text-blue-300 text-xs">Provinsi Jawa Timur &mdash; Prov. Jatim</p>
            </div>

        </div>

        {{-- ===================== PANEL KANAN: FORM ===================== --}}
        <div class="flex-1 flex items-start justify-center px-8 py-12 bg-white overflow-y-auto">
            <div class="w-full max-w-sm">

                {{-- Header mobile --}}
                <div class="lg:hidden mb-8">
                    <a href="{{ route('landing.index') }}" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center">
                            <i class="bi bi-briefcase-fill text-white text-sm"></i>
                        </div>
                        <span class="font-extrabold text-gray-900 text-xl tracking-tight">SINAKERTRANS</span>
                    </a>
                </div>

                {{-- Form heading --}}
                <div class="mb-7">
                    <h1 class="text-xl font-extrabold text-gray-900 mb-1">Buat Akun Baru</h1>
                    <p class="text-gray-500 text-sm">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Masuk
                            disini</a>
                    </p>
                </div>

                {{-- ================= GLOBAL ERROR ALERT ================= --}}
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-100 rounded-xl p-3.5 mb-5">
                        <div class="flex items-center gap-2 mb-1.5">
                            <i class="bi bi-exclamation-circle-fill text-red-400 text-sm"></i>
                            <p class="font-semibold text-red-700 text-sm">Terdapat kesalahan:</p>
                        </div>
                        <ul class="space-y-0.5 pl-5">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-500 text-xs">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ================= FORM REGISTER ================= --}}
                <form action="{{ route('register.post') }}" method="POST" id="registerForm" class="space-y-4">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <x-input-field name="nama_lengkap" label="Nama Lengkap" placeholder="Nama sesuai KTP">
                        <x-slot:icon>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M8.25 9a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0M12 6.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5"
                                    clip-rule="evenodd" />
                                <path fill-rule="evenodd"
                                    d="M1.25 12a10.75 10.75 0 1 1 21.5 0 10.75 10.75 0 0 1-21.5 0ZM12 2.75a9.25 9.25 0 0 0-6.56 15.77c.18-.97.54-1.89 1.25-2.62 1.06-1.1 2.76-1.65 5.31-1.65s4.25.54 5.31 1.65c.71.73 1.07 1.65 1.25 2.62A9.25 9.25 0 0 0 12 2.75m5.2 16.9c-.1-1.2-.37-2.1-.97-2.71-.65-.68-1.85-1.19-4.23-1.19s-3.58.51-4.23 1.19c-.6.62-.86 1.5-.96 2.72a9.2 9.2 0 0 0 10.38 0"
                                    clip-rule="evenodd" />
                            </svg>
                        </x-slot:icon>
                    </x-input-field>

                    {{-- Email --}}
                    <x-input-field name="email" label="Email Aktif" placeholder="nama@email.com">
                        <x-slot:icon>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                                <path
                                    d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                            </svg>
                        </x-slot:icon>
                    </x-input-field>

                    {{-- Jenjang Pendidikan --}}
                    <x-select-field name="education_level" label="Jenjang Pendidikan">
                        <x-slot:icon>
                            <svg class="w-11 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M36.078 7.173 19.876.346c-.865-.365-1.858-.365-2.728 0L.922 7.183C.362 7.42.009 7.944 0 8.554c-.009.607.329 1.143.881 1.394l1.709.782c-.052.079-.09.168-.09.27v9c0 .005.003.01.003.016-1.172.207-2.066 1.226-2.066 2.456 0 1.111.733 2.043 1.736 2.368L.375 29.004c-.271.629-.206 1.326.18 1.912C1.002 31.595 1.787 32 2.656 32l.289-.014c.813-.066 1.539-.481 1.941-1.111.344-.537.418-1.182.203-1.767L3.541 24.89c1.086-.272 1.896-1.248 1.896-2.418 0-1.188-.833-2.18-1.945-2.433.001-.014.008-.025.008-.039v-8.853l4 1.833V20h.01c.103 2.257 5.827 3.439 11.49 3.439S30.387 22.257 30.49 20h.01v-7.551l5.607-2.507c.557-.248.899-.782.893-1.393-.007-.611-.359-1.139-.922-1.376M4.045 30.336c-.235.368-.677.613-1.186.654l-.203.01c-.531 0-1.005-.236-1.266-.634-.2-.304-.234-.647-.097-.966l1.533-3.552 1.323 3.604c.11.294.072.609-.104.884m.393-7.864c0 .827-.673 1.5-1.5 1.5s-1.5-.673-1.5-1.5.673-1.5 1.5-1.5 1.5.673 1.5 1.5M29.5 18.419c-1.898-1.305-6.219-1.98-10.5-1.98s-8.602.675-10.5 1.98v-7.278c0-1.097 3.185-2.641 10.266-2.641 7.004 0 10.734 1.57 10.734 2.703zM19 22.439c-6.409 0-10.5-1.48-10.5-2.5s4.091-2.5 10.5-2.5 10.5 1.48 10.5 2.5-4.091 2.5-10.5 2.5M35.699 9.03 30.5 11.354v-.151c0-2.181-4.825-3.703-11.734-3.703C11.922 7.5 7.5 8.929 7.5 11.141v.741L1.297 9.038c-.28-.127-.298-.392-.297-.471s.027-.343.311-.463l16.227-6.837c.619-.263 1.331-.261 1.95 0L35.69 8.094c.285.12.31.386.311.464 0 .08-.02.345-.302.472" />
                            </svg>
                        </x-slot:icon>
                        <option value="SMA/SMK" {{ old('education_level') == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK
                        </option>
                        <option value="D3" {{ old('education_level') == 'D3' ? 'selected' : '' }}>D3</option>
                        <option value="S1" {{ old('education_level') == 'S1' ? 'selected' : '' }}>S1</option>
                        <option value="S2" {{ old('education_level') == 'S2' ? 'selected' : '' }}>S2</option>
                    </x-select-field>

                    {{-- Password --}}
                    <x-input-field name="password" label="Password" type="password" placeholder="Minimal 8 karakter"
                        minlength="8">
                        <x-slot:icon>
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </x-slot:icon>
                        <x-slot:append>
                            <button type="button" id="togglePassword"
                                class="text-gray-400 hover:text-blue-500 focus:outline-none transition-colors">
                                <svg id="eye-icon" class="hidden w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                    <path fill-rule="evenodd"
                                        d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <svg id="eye-slash-icon" class="block w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                    <path
                                        d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.243 4.243Z" />
                                    <path
                                        d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                                </svg>
                            </button>
                        </x-slot:append>
                    </x-input-field>

                    {{-- Password Strength Bar --}}
                    <div id="strengthBar" class="hidden -mt-2">
                        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div id="fill"
                                class="h-full transition-all duration-300 w-0 bg-red-500 rounded-full"></div>
                        </div>
                        <p id="strength" class="text-xs mt-1 text-red-600 font-medium"></p>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <x-input-field name="password_confirmation" label="Ulangi Password" type="password"
                        placeholder="Ketik ulang password" minlength="8" required>
                        <x-slot:icon>
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </x-slot:icon>
                        <x-slot:append>
                            <button type="button" id="togglePasswordConfirm"
                                class="text-gray-400 hover:text-blue-500 focus:outline-none transition-colors">
                                <svg id="eye-icon-confirm" class="hidden w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                    <path fill-rule="evenodd"
                                        d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <svg id="eye-slash-icon-confirm" class="block w-5 h-5"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                    <path
                                        d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.243 4.243Z" />
                                    <path
                                        d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                                </svg>
                            </button>
                        </x-slot:append>
                    </x-input-field>

                    {{-- Terms & Condition --}}
                    <div class="pt-1">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" name="terms"
                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 shrink-0 @error('terms') @enderror"
                                {{ old('terms') ? 'checked' : '' }} required>
                            <span class="text-sm text-gray-500 leading-relaxed group-hover:text-gray-700 transition">
                                Saya menyetujui
                                <a href="#" class="text-blue-600 font-semibold hover:underline">Syarat &
                                    Ketentuan</a>
                                yang berlaku
                            </span>
                        </label>
                        @error('terms')
                            <p class="text-red-500 text-xs mt-1.5 pl-7">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="submitBtn"
                        class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 active:scale-95 transition-all text-sm shadow-sm shadow-blue-600/20">
                        Buat Akun
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
