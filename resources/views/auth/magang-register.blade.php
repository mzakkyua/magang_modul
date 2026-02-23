<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SINAKERTRANS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen py-10">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-600">Buat Akun Baru</h2>
            <p class="text-gray-500 text-sm">Daftar sebagai peserta magang / penelitian.</p>
        </div>

        {{-- ERROR MESSAGE --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf

            {{-- USERNAME --}}
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="username" value="{{ old('username') }}"
                    class="w-full px-3 py-2 border rounded-lg focus:ring focus:border-blue-300"
                    placeholder="Sesuai KTP / KTM" required>
            </div>

            {{-- EMAIL --}}
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Email Aktif</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-3 py-2 border rounded-lg focus:ring focus:border-blue-300"
                    placeholder="nama@email.com" required>
            </div>

            {{-- PASSWORD --}}
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border rounded-lg focus:ring focus:border-blue-300"
                    placeholder="Minimal 8 karakter" required>
            </div>

            {{-- PASSWORD CONFIRM --}}
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Ulangi Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full px-3 py-2 border rounded-lg focus:ring focus:border-blue-300"
                    placeholder="Ketik ulang password" required>
            </div>

            {{-- PASSWORD STRENGTH --}}
            <div id="strengthBar" class="mb-2 h-2 bg-gray-200 rounded overflow-hidden hidden">
                <div id="fill" class="w-0 h-full bg-red-500 transition-all duration-300"></div>
            </div>
            <p id="strength" class="text-xs text-gray-500 mb-4"></p>

            {{-- TERMS
            <div class="mb-6">
                <label class="flex items-center text-sm">
                    <input type="checkbox" name="terms" class="mr-2" required>
                    <span>Saya setuju dengan <a href="#" class="text-blue-600 font-semibold">Syarat &
                            Ketentuan</a></span>
                </label>
            </div> --}}

            {{-- SUBMIT --}}
            <button type="submit" id="submitBtn"
                class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                Daftar
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <p class="text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">
                    Masuk di sini
                </a>
            </p>
            <a href="{{ route('landing.index') }}" class="text-gray-400 text-xs hover:underline">
                ← Kembali ke Beranda
            </a>
        </div>

    </div>

    <script>
        /* =============================
            PASSWORD STRENGTH INDICATOR
           ============================= */
        const passwordField = document.querySelector('input[name="password"]');
        const strengthBar = document.getElementById('strengthBar');
        const fill = document.getElementById('fill');
        const strength = document.getElementById('strength');

        passwordField?.addEventListener('input', function(e) {
            const pwd = e.target.value;
            if (!pwd.length) {
                strengthBar.classList.add('hidden');
                strength.textContent = '';
                return;
            }

            const result = calculateStrength(pwd);
            strengthBar.classList.remove('hidden');
            fill.style.width = result.percent + '%';
            fill.className = `h-full bg-${result.color}-500 transition-all`;
            strength.textContent = result.text;
            strength.className = `text-xs text-${result.color}-600 mb-4`;
        });

        function calculateStrength(pwd) {
            let percent = 25,
                color = 'red',
                text = 'Lemah';
            if (pwd.length >= 12) percent = 50, color = 'yellow', text = 'Sedang';
            if (/[A-Z]/.test(pwd) && /[0-9]/.test(pwd)) percent = 75, color = 'blue', text = 'Kuat';
            if (/[@!#$%^&*]/.test(pwd)) percent = 100, color = 'green', text = 'Sangat Kuat';
            return {
                percent,
                color,
                text
            };
        }

        /* =============================
            SUBMIT LOADING STATE
           ============================= */
        document.getElementById('registerForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Mendaftarkan...';
        });
    </script>

</body>

</html>
