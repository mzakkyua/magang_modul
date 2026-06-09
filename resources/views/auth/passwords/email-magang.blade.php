<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SINAKERTRANS</title>

    {{-- Favicon dengan Auto Cache-Busting --}}
    <link rel="icon" type="image/svg+xml"
        href="{{ asset('assets/images/favicon.svg') }}?v={{ file_exists(public_path('assets/images/favicon.svg')) ? filemtime(public_path('assets/images/favicon.svg')) : '1' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Pulse ring pada logo */
        .logo-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 14px;
            border: 1px solid rgba(37, 99, 235, 0.25);
            animation: logo-pulse 3s ease-in-out infinite;
        }

        @keyframes logo-pulse {

            0%,
            100% {
                opacity: 0.5;
                transform: scale(1);
            }

            50% {
                opacity: 0;
                transform: scale(1.18);
            }
        }

        /* Bottom slide pada input */
        .input-wrap:focus-within .input-bottom-line {
            transform: scaleX(1);
        }

        .input-wrap:focus-within .input-icon {
            color: #3b82f6;
        }

        /* Shimmer pada tombol */
        .btn-shimmer {
            position: relative;
            overflow: hidden;
        }

        .btn-shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transition: left 0.4s;
        }

        .btn-shimmer:hover::before {
            left: 100%;
        }
    </style>
</head>

<body class="bg-white min-h-screen flex">

    {{-- ========================= PANEL KIRI: BRANDING ========================= --}}
    <div class="hidden lg:flex flex-col justify-between w-105 shrink-0 px-12 py-12 border-r border-gray-100">

        {{-- Logo Desktop --}}
        <a href="{{ route('login') }}" class="flex items-center gap-2.5 w-fit hover:opacity-90 transition-opacity">
            <div class="logo-ring relative w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shrink-0 overflow-hidden"
                style="box-shadow: 0 0 0 3px rgba(37,99,235,0.15), 0 4px 12px rgba(37,99,235,0.3);">
                {{-- Implementasi Favicon Lokal --}}
                <img src="{{ asset('assets/images/favicon.svg') }}?v={{ file_exists(public_path('assets/images/favicon.svg')) ? filemtime(public_path('assets/images/favicon.svg')) : '1' }}"
                    alt="Logo" class="w-5 h-5 object-contain" />
            </div>
            <span class="text-xl font-extrabold text-blue-600 tracking-tight">SINAKERTRANS</span>
        </a>

        {{-- Tengah: ilustrasi & teks --}}
        <div>
            {{-- Ikon besar --}}
            <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mb-6"
                style="box-shadow: 0 4px 20px rgba(251,191,36,0.2);">
                <i class="bi bi-lock text-amber-500 text-3xl"></i>
            </div>

            <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mb-3">
                Lupa Password?
            </h1>
            <p class="text-sm text-gray-400 leading-relaxed mb-8">
                Tidak masalah. Masukkan email yang terdaftar dan kami akan mengirimkan link untuk membuat password baru.
            </p>

            {{-- Step hints --}}
            <div class="space-y-4">
                @foreach ([['icon' => 'bi-envelope', 'color' => 'blue', 'title' => 'Masukkan Email', 'desc' => 'Email yang digunakan saat mendaftar'], ['icon' => 'bi-send', 'color' => 'indigo', 'title' => 'Cek Inbox', 'desc' => 'Link reset dikirim dalam 1-2 menit'], ['icon' => 'bi-shield-lock', 'color' => 'emerald', 'title' => 'Reset Password', 'desc' => 'Buat password baru yang aman']] as $step)
                    <div class="flex items-start gap-3">
                        <div
                            class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center
                                    bg-{{ $step['color'] }}-50">
                            <i class="bi {{ $step['icon'] }} text-{{ $step['color'] }}-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-700">{{ $step['title'] }}</p>
                            <p class="text-xs text-gray-400">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer kiri --}}
        <p class="text-xs text-gray-300">
            &copy; {{ date('Y') }} Disnakertrans Jawa Timur
        </p>

    </div>

    {{-- Garis pemisah tipis --}}
    <div class="hidden lg:block w-px bg-gray-100 shrink-0"></div>

    {{-- ========================= PANEL KANAN: FORM ========================= --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-12">

        {{-- Logo mobile --}}
        <a href="{{ route('login') }}"
            class="flex lg:hidden items-center gap-2 mb-8 hover:opacity-90 transition-opacity">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center overflow-hidden">
                {{-- Implementasi Favicon Lokal --}}
                <img src="{{ asset('assets/images/favicon.svg') }}?v={{ file_exists(public_path('assets/images/favicon.svg')) ? filemtime(public_path('assets/images/favicon.svg')) : '1' }}"
                    alt="Logo" class="w-5 h-5 object-contain" />
            </div>
            <span class="text-lg font-extrabold text-blue-600 tracking-tight">SINAKERTRANS</span>
        </a>

        <div class="w-full max-w-sm">

            {{-- Heading --}}
            <div class="mb-8">
                <h2 class="text-2xl font-extrabold text-gray-900 mb-1">Reset Password</h2>
                <p class="text-sm text-gray-400">
                    Kami akan mengirim link reset ke email Anda.
                </p>
            </div>

            {{-- STATUS SUCCESS --}}
            @if (session('status'))
                <div
                    class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700
                            text-sm px-4 py-3 rounded-xl mb-6">
                    <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- ERROR --}}
            @if ($errors->any())
                <div
                    class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-600
                            text-sm px-4 py-3 rounded-xl mb-6">
                    <i class="bi bi-exclamation-circle-fill text-red-400 mt-0.5 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- FORM --}}
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <div class="input-wrap relative group">
                        <div
                            class="input-icon absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none
                                    text-gray-400 transition-colors duration-200 z-10">
                            <i class="bi bi-envelope text-sm"></i>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            autocomplete="email" placeholder=" "
                            class="peer w-full pt-5 pb-2 pl-10 pr-3.5 rounded-xl border bg-white
                                   text-sm font-medium text-gray-800 outline-none
                                   transition-all duration-200
                                   {{ $errors->has('email')
                                       ? 'border-red-300 focus:ring-2 focus:ring-red-100'
                                       : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50/20 focus:border-blue-400 focus:ring-2 focus:ring-blue-100' }}">
                        <label for="email"
                            class="absolute pointer-events-none select-none left-10
                                      transition-all duration-200 ease-out origin-left
                                      top-2.5 text-[10.5px] font-bold
                                      peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2
                                      peer-placeholder-shown:text-sm peer-placeholder-shown:font-normal
                                      peer-focus:top-2.5 peer-focus:translate-y-0
                                      peer-focus:text-[10.5px] peer-focus:font-bold
                                      {{ $errors->has('email') ? 'text-red-400 peer-focus:text-red-500' : 'text-gray-400 peer-focus:text-blue-500' }}">
                            Email Terdaftar
                        </label>
                        {{-- Bottom slide line --}}
                        <div
                            class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-xl overflow-hidden pointer-events-none">
                            <div
                                class="input-bottom-line h-full origin-left scale-x-0 transition-transform duration-300 ease-out
                                        {{ $errors->has('email') ? 'bg-red-500' : 'bg-blue-500' }}">
                            </div>
                        </div>
                    </div>
                    @error('email')
                        <p class="flex items-center gap-1.5 text-red-500 text-xs font-medium mt-1.5">
                            <i class="bi bi-exclamation-circle-fill text-[11px]"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" id="submitBtn"
                    class="btn-shimmer w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-xl
                           text-sm hover:bg-blue-700 active:scale-[0.98]
                           transition-all duration-200
                           shadow-md shadow-blue-600/25 hover:shadow-blue-600/40 hover:-translate-y-0.5">
                    <span id="submitText" class="flex items-center justify-center gap-2">
                        <i class="bi bi-send"></i> Kirim Link Reset
                    </span>
                </button>
            </form>

            {{-- Back to login --}}
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-blue-600 transition-colors duration-200 font-medium">
                    <i class="bi bi-arrow-left text-xs"></i> Kembali ke Login
                </a>
            </div>

        </div>
    </div>

    <script>
        // SECTION: Loading state saat submit
        document.querySelector('form')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            const text = document.getElementById('submitText');
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            text.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Mengirim...';
        });
    </script>

</body>

</html>
