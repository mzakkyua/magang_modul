<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SINAKERTRANS</title>

    {{-- Tailwind CDN (Production: gunakan build Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>

                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('email') border-red-500 @enderror"
                    placeholder="nama@email.com" required>

                @error('email')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ================= PASSWORD ================= --}}
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>

                <div class="relative">
                    <input type="password" id="password" name="password" minlength="8" autocomplete="current-password"
                        class="w-full pr-10 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('password') border-red-500 @enderror"
                        placeholder="********" required>

                    {{-- Toggle Show / Hide Password --}}
                    <button type="button" id="togglePassword"
                        class="absolute right-3 top-2 text-gray-500 hover:text-gray-700">
                        <i class="bi bi-eye-slash text-xl"></i>
                    </button>
                </div>

                @error('password')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🔐 LINK LUPA PASSWORD --}}
            <div class="text-right mb-4">
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                    Lupa Password?
                </a>
            </div>

            {{-- ================= SUBMIT BUTTON ================= --}}
            <button type="submit" id="submitBtn"
                class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-200 shadow hover:shadow-md">
                Masuk
            </button>
        </form>

        {{-- ================= FOOTER ================= --}}
        <div class="mt-6 text-center text-sm">
            <p class="text-gray-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">
                    Daftar Magang
                </a>
            </p>

            <p class="mt-2 text-gray-500 text-xs">
                Atau
                <a href="{{ route('landing.index') }}" class="hover:underline">
                    Kembali ke Beranda
                </a>
            </p>
        </div>
    </div>

    {{-- ================= SCRIPT ================= --}}
    <script>
        // ===== PASSWORD TOGGLE =====
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword?.addEventListener('click', function(e) {
            e.preventDefault();

            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;

            this.innerHTML = type === 'password' ?
                '<i class="bi bi-eye-slash text-xl"></i>' :
                '<i class="bi bi-eye text-xl"></i>';
        });

        // ===== LOADING STATE (ANTI DOUBLE CLICK) =====
        document.getElementById('loginForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Memproses...';
        });
    </script>

</body>

</html>
