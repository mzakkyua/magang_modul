<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SINAKERTRANS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth.js'])

    {{-- CATATAN: Link Bootstrap Icons sudah dihapus karena kita pakai SVG --}}
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md border border-gray-200">

        {{-- ================= HEADER ================= --}}
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-blue-600">SINAKERTRANS</h2>
            <p class="text-gray-500 text-sm">Masuk untuk melanjutkan aktivitas magang.</p>
        </div>

        {{-- ================= ERROR ALERT ================= --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p class="font-bold">Login Gagal</p>
                <p class="text-sm">{{ $errors->first() }}</p>
            </div>
        @endif

        {{-- ================= FORM LOGIN ================= --}}
        <form action="{{ route('login.post') }}" method="POST" id="loginForm">
            @csrf

            {{-- ================= EMAIL ================= --}}
            <x-input-field name="email" label="Email" type="email" placeholder="nama@email.com" required
                autocomplete="email">
                <x-slot:icon>
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                        <path
                            d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                    </svg>
                </x-slot:icon>
            </x-input-field>

            {{-- ================= PASSWORD ================= --}}
            {{-- Tambahkan margin bottom negatif sedikit agar link lupa password pas --}}
            <div class="mb-2">
                <x-input-field name="password" label="Password" type="password" placeholder="********" required
                    autocomplete="current-password">

                    {{-- Ikon Kiri (Gembok) --}}
                    <x-slot:icon>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z"
                                clip-rule="evenodd" />
                        </svg>
                    </x-slot:icon>

                    {{-- Ikon Kanan (Tombol Show/Hide) --}}
                    <x-slot:append>
                        <button type="button" onclick="togglePassword('password')"
                            class="text-gray-400 hover:text-blue-500 focus:outline-none transition-colors">
                            <svg id="eye-password" class="hidden w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                <path fill-rule="evenodd"
                                    d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg id="eye-slash-password" class="block w-5 h-5" xmlns="http://www.w3.org/2000/svg"
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
            </div>

            {{-- 🔐 LINK LUPA PASSWORD --}}
            <div class="text-right mb-6">
                <a href="{{ route('password.request') }}"
                    class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                    Lupa Password?
                </a>
            </div>

            {{-- ================= SUBMIT BUTTON ================= --}}
            <button type="submit" id="submitBtn"
                class="w-full bg-blue-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-blue-700 transition duration-200 shadow hover:shadow-md flex justify-center items-center">
                <span>Masuk</span>
            </button>
        </form>

        {{-- ================= FOOTER ================= --}}
        <div class="mt-8 text-center text-sm border-t pt-6 border-gray-100">
            <p class="text-gray-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">
                    Daftar Magang
                </a>
            </p>

            <p class="mt-3 text-gray-500 text-xs">
                <a href="{{ route('landing.index') }}"
                    class="hover:text-gray-800 flex items-center justify-center gap-1 transition-colors">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Kembali ke Beranda
                </a>
            </p>
        </div>
    </div>

</body>

</html>
