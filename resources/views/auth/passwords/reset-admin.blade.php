<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Admin - SINAKERTRANS</title>

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

        .input-wrap:focus-within .input-bottom-line {
            transform: scaleX(1);
        }

        .input-wrap:focus-within .input-icon {
            color: #3b82f6;
        }

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

        /* Password strength bar */
        #strength-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.4s ease, background-color 0.4s ease;
            width: 0%;
        }
    </style>
</head>

<body class="bg-white min-h-screen flex">

    {{-- ========================= PANEL KIRI: BRANDING ========================= --}}
    <div class="hidden lg:flex flex-col justify-between w-105 shrink-0 px-12 py-12 border-r border-gray-100">

        {{-- Logo --}}
        <a href="{{ route('login') }}" class="flex items-center gap-2.5 w-fit">
            <div class="logo-ring relative w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shrink-0"
                style="box-shadow: 0 0 0 3px rgba(37,99,235,0.15), 0 4px 12px rgba(37,99,235,0.3);">
                <i class="bi bi-briefcase-fill text-white text-sm"></i>
            </div>
            <span class="text-xl font-extrabold text-blue-600 tracking-tight">SINAKERTRANS</span>
        </a>

        {{-- Tengah --}}
        <div>
            {{-- Ikon besar --}}
            <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6"
                style="box-shadow: 0 4px 20px rgba(37,99,235,0.12);">
                <i class="bi bi-shield-lock text-blue-500 text-3xl"></i>
            </div>

            <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mb-3">
                Buat Password Baru Admin
            </h1>
            <p class="text-sm text-gray-400 leading-relaxed mb-8">
                Pastikan password baru Anda kuat dan aman untuk melindungi data kedinasan. Jangan gunakan password yang
                sama dengan akun lain.
            </p>

            {{-- Tips keamanan --}}
            <div class="space-y-3">
                @foreach ([['icon' => 'bi-check-circle', 'text' => 'Minimal 8 karakter'], ['icon' => 'bi-check-circle', 'text' => 'Kombinasi huruf & angka'], ['icon' => 'bi-check-circle', 'text' => 'Hindari informasi pribadi']] as $tip)
                    <div class="flex items-center gap-2.5">
                        <i class="bi {{ $tip['icon'] }} text-emerald-400 text-sm shrink-0"></i>
                        <p class="text-sm text-gray-500">{{ $tip['text'] }}</p>
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
        <a href="{{ route('login') }}" class="flex lg:hidden items-center gap-2 mb-8">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="bi bi-briefcase-fill text-white text-xs"></i>
            </div>
            <span class="text-lg font-extrabold text-blue-600 tracking-tight">SINAKERTRANS</span>
        </a>

        <div class="w-full max-w-sm">

            {{-- Heading --}}
            <div class="mb-8">
                <h2 class="text-2xl font-extrabold text-gray-900 mb-1">Password Baru Admin</h2>
                <p class="text-sm text-gray-400">
                    Masukkan password baru untuk akun Admin Anda.
                </p>
            </div>

            {{-- ERROR GLOBAL --}}
            @if ($errors->any())
                <div
                    class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-600
                            text-sm px-4 py-3 rounded-xl mb-6">
                    <i class="bi bi-exclamation-circle-fill text-red-400 mt-0.5 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- FORM (Perhatikan action-nya mengarah ke rute update admin) --}}
            <form method="POST" action="{{ route('admin.password.update') }}" id="resetForm" class="space-y-5">
                @csrf

                {{-- TOKEN (hidden) --}}
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- EMAIL (readonly) --}}
                <div class="input-wrap relative group">
                    <div
                        class="input-icon absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none
                                text-gray-300 z-10">
                        <i class="bi bi-envelope text-sm"></i>
                    </div>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" readonly placeholder=" "
                        class="peer w-full pt-5 pb-2 pl-10 pr-3.5 rounded-xl
                               border border-gray-100 bg-gray-50
                               text-sm font-medium text-gray-400 outline-none cursor-not-allowed">
                    <label
                        class="absolute pointer-events-none select-none left-10
                                  top-2.5 text-[10.5px] font-bold text-gray-300
                                  peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2
                                  peer-placeholder-shown:text-sm peer-placeholder-shown:font-normal
                                  transition-all duration-200 ease-out origin-left">
                        Email Admin
                    </label>
                </div>

                {{-- PASSWORD BARU --}}
                <div>
                    <div class="input-wrap relative group">
                        <div
                            class="input-icon absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none
                                    text-gray-400 transition-colors duration-200 z-10">
                            <i class="bi bi-lock text-sm"></i>
                        </div>
                        <input type="password" name="password" id="password" minlength="8" required placeholder=" "
                            class="peer w-full pt-5 pb-2 pl-10 pr-10 rounded-xl border bg-white
                                   text-sm font-medium text-gray-800 outline-none
                                   transition-all duration-200
                                   {{ $errors->has('password')
                                       ? 'border-red-300 focus:ring-2 focus:ring-red-100'
                                       : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50/20 focus:border-blue-400 focus:ring-2 focus:ring-blue-100' }}">
                        <label for="password"
                            class="absolute pointer-events-none select-none left-10
                                      transition-all duration-200 ease-out origin-left
                                      top-2.5 text-[10.5px] font-bold
                                      peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2
                                      peer-placeholder-shown:text-sm peer-placeholder-shown:font-normal
                                      peer-focus:top-2.5 peer-focus:translate-y-0
                                      peer-focus:text-[10.5px] peer-focus:font-bold
                                      {{ $errors->has('password') ? 'text-red-400 peer-focus:text-red-500' : 'text-gray-400 peer-focus:text-blue-500' }}">
                            Password Baru
                        </label>
                        {{-- Bottom slide --}}
                        <div
                            class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-xl overflow-hidden pointer-events-none">
                            <div
                                class="input-bottom-line h-full origin-left scale-x-0 transition-transform duration-300 ease-out
                                        {{ $errors->has('password') ? 'bg-red-500' : 'bg-blue-500' }}">
                            </div>
                        </div>
                        {{-- Toggle show/hide --}}
                        <button type="button" id="togglePassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500 transition-colors duration-200 z-10">
                            <i class="bi bi-eye-slash text-base" id="toggleIcon"></i>
                        </button>
                    </div>

                    {{-- Password strength bar --}}
                    <div class="mt-2 -mb-1">
                        <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                            <div id="strength-bar-fill"></div>
                        </div>
                        <p id="strength-text" class="text-[10.5px] font-semibold mt-1 text-gray-300"></p>
                    </div>

                    @error('password')
                        <p class="flex items-center gap-1.5 text-red-500 text-xs font-medium mt-1.5">
                            <i class="bi bi-exclamation-circle-fill text-[11px]"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- KONFIRMASI PASSWORD --}}
                <div>
                    <div class="input-wrap relative group">
                        <div
                            class="input-icon absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none
                                    text-gray-400 transition-colors duration-200 z-10">
                            <i class="bi bi-lock-fill text-sm"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            placeholder=" "
                            class="peer w-full pt-5 pb-2 pl-10 pr-3.5 rounded-xl border bg-white
                                   text-sm font-medium text-gray-800 outline-none
                                   transition-all duration-200
                                   border-gray-200 hover:border-blue-300 hover:bg-blue-50/20
                                   focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        <label for="password_confirmation"
                            class="absolute pointer-events-none select-none left-10
                                      transition-all duration-200 ease-out origin-left
                                      top-2.5 text-[10.5px] font-bold text-gray-400
                                      peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2
                                      peer-placeholder-shown:text-sm peer-placeholder-shown:font-normal
                                      peer-focus:top-2.5 peer-focus:translate-y-0
                                      peer-focus:text-[10.5px] peer-focus:font-bold peer-focus:text-blue-500">
                            Konfirmasi Password
                        </label>
                        {{-- Bottom slide --}}
                        <div
                            class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-xl overflow-hidden pointer-events-none">
                            <div
                                class="input-bottom-line h-full origin-left scale-x-0 bg-blue-500
                                        transition-transform duration-300 ease-out">
                            </div>
                        </div>
                    </div>
                    {{-- Match indicator --}}
                    <p id="match-text" class="text-[10.5px] font-semibold mt-1.5 hidden"></p>
                </div>

                {{-- SUBMIT --}}
                <button type="submit" id="submitBtn"
                    class="btn-shimmer w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-xl
                           text-sm hover:bg-blue-700 active:scale-[0.98]
                           transition-all duration-200
                           shadow-md shadow-blue-600/25 hover:shadow-blue-600/40 hover:-translate-y-0.5">
                    <span id="submitText" class="flex items-center justify-center gap-2">
                        <i class="bi bi-shield-check"></i> Reset Password
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

    {{-- ========================= SCRIPT ========================= --}}
    <script>
        // SECTION: Toggle show/hide password
        const toggleBtn = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('toggleIcon');
        const passInput = document.getElementById('password');

        toggleBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            toggleIcon.className = isPassword ?
                'bi bi-eye text-base' :
                'bi bi-eye-slash text-base';
        });

        // SECTION: Password strength meter
        const strengthFill = document.getElementById('strength-bar-fill');
        const strengthText = document.getElementById('strength-text');

        const strengthLevels = [{
                label: '',
                color: '',
                width: '0%'
            },
            {
                label: 'Lemah',
                color: '#ef4444',
                width: '25%'
            },
            {
                label: 'Cukup',
                color: '#f59e0b',
                width: '50%'
            },
            {
                label: 'Bagus',
                color: '#3b82f6',
                width: '75%'
            },
            {
                label: 'Kuat',
                color: '#10b981',
                width: '100%'
            },
        ];

        passInput?.addEventListener('input', function() {
            const val = this.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const level = val.length === 0 ? 0 : Math.min(score + (val.length >= 12 ? 1 : 0), 4);
            const s = strengthLevels[level];

            strengthFill.style.width = s.width;
            strengthFill.style.backgroundColor = s.color;
            strengthText.textContent = s.label;
            strengthText.style.color = s.color;
        });

        // SECTION: Konfirmasi password match indicator
        const confirmInput = document.getElementById('password_confirmation');
        const matchText = document.getElementById('match-text');

        confirmInput?.addEventListener('input', function() {
            if (this.value.length === 0) {
                matchText.classList.add('hidden');
                return;
            }
            matchText.classList.remove('hidden');
            const match = this.value === passInput.value;
            matchText.textContent = match ? '✓ Password cocok' : '✗ Password tidak cocok';
            matchText.style.color = match ? '#10b981' : '#ef4444';
        });

        // SECTION: Loading state saat submit
        document.getElementById('resetForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            const text = document.getElementById('submitText');
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            text.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
        });
    </script>

</body>

</html>
