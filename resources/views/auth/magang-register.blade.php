<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SINAKERTRANS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen py-10">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-600">Buat Akun Baru</h2>
            <p class="text-gray-500 text-sm">Daftar sebagai peserta magang/penelitian.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="username"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="Sesuai KTP/KTM" required>
            </div>

            {{-- INPUT EMAIL --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Aktif</label>
                <<<<<<< HEAD <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-envelope text-gray-400"></i>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('email') border-red-500 @enderror"
                        placeholder="nama@email.com" required>
            </div>
            @error('email')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
            =======
            <input type="email" name="email"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                placeholder="nama@email.com" required>
            >>>>>>> feature/dashboard-admin
    </div>

    {{-- INPUT PASSWORD --}}
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
        <<<<<<< HEAD <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="bi bi-lock text-gray-400"></i>
            </div>
            <input type="password" name="password"
                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('password') border-red-500 @enderror"
                placeholder="Minimal 8 karakter" required>
    </div>
    @error('password')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
    =======
    <input type="password" name="password"
        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
        placeholder="Minimal 8 karakter" required>
    >>>>>>> feature/dashboard-admin
    </div>

    {{-- INPUT KONFIRMASI PASSWORD --}}
    <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2">Ulangi Password</label>
        <<<<<<< HEAD <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="bi bi-shield-lock text-gray-400"></i>
            </div>
            <input type="password" name="password_confirmation"
                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                placeholder="Ketik ulang password" required>
    </div>
    </div>

    {{-- PASSWORD STRENGTH INDICATOR --}}
    <div id="strengthBar" class="mb-4 h-2 bg-gray-200 rounded overflow-hidden hidden">
        <div id="fill" class="w-0 h-full bg-red-500 transition-all duration-300"></div>
    </div>
    <p id="strength" class="text-xs text-gray-500 mb-4"></p>

    {{-- CHECKBOX T&C --}}
    <div class="mb-6">
        <label class="flex items-center text-sm text-gray-700">
            <input type="checkbox" name="terms" class="mr-2 rounded @error('terms') border-red-500 @enderror">
            <span>Saya setuju dengan <a href="#" class="text-blue-600 hover:underline font-semibold">Syarat &
                    Ketentuan</a></span>
        </label>
        @error('terms')
            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" id="submitBtn"
        class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
        =======
        <input type="password" name="password_confirmation"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
            placeholder="Ketik ulang password" required>
        </div>

        <button type="submit"
            class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-200">
            >>>>>>> feature/dashboard-admin
            Daftar Sekarang
        </button>
        </form>

        <<<<<<< HEAD <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm">
            <p class="text-gray-600">Sudah punya akun?
                <a href="{{ route('login') }}"
                    class="text-blue-600 font-bold hover:text-blue-800 hover:underline transition">Masuk disini</a>
            </p>
            <div class="mt-4">
                <a href="{{ route('landing.index') }}"
                    class="text-gray-400 hover:text-gray-600 text-xs flex items-center justify-center gap-1">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
            =======
            <div class="mt-6 text-center text-sm">
                <p class="text-gray-600">Sudah punya akun? <a href="{{ route('login') }}"
                        class="text-blue-600 font-bold hover:underline">Masuk disini</a></p>
                >>>>>>> feature/dashboard-admin
            </div>
            </div>

            <script>
                // ===== PASSWORD TOGGLE (Bug #6) =====
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');

                togglePassword?.addEventListener('click', function(e) {
                    e.preventDefault();
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ?
                        '<i class="bi bi-eye-slash text-xl"></i>' :
                        '<i class="bi bi-eye text-xl"></i>';
                });

                // ===== PASSWORD STRENGTH INDICATOR (Improvement #8) =====
                const passwordField = document.querySelector('input[name="password"]');
                const strengthBar = document.getElementById('strengthBar');
                const fill = document.getElementById('fill');
                const strength = document.getElementById('strength');

                passwordField?.addEventListener('input', function(e) {
                    const pwd = e.target.value;
                    const result = calculateStrength(pwd);

                    if (pwd.length === 0) {
                        strengthBar.classList.add('hidden');
                        strength.textContent = '';
                        return;
                    }

                    strengthBar.classList.remove('hidden');
                    fill.style.width = result.percent + '%';
                    fill.className = `h-full transition-all duration-300 bg-${result.color}-500`;
                    strength.textContent = result.text;
                    strength.className = `text-xs text-${result.color}-600 mb-4`;
                });

                function calculateStrength(pwd) {
                    let percent = 0;
                    let color = 'red';
                    let text = 'Lemah';

                    if (pwd.length >= 8) percent = 25, color = 'red';
                    if (pwd.length >= 12) percent = 50, color = 'yellow';
                    if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) percent = 60, color = 'yellow';
                    if (/[0-9]/.test(pwd)) percent = 75, color = 'blue', text = 'Kuat';
                    if (/[@!#$%^&*]/.test(pwd)) percent = 100, color = 'green', text = 'Sangat Kuat';

                    if (percent === 25) text = 'Lemah';
                    else if (percent <= 50) text = 'Sedang';

                    return {
                        percent,
                        color,
                        text
                    };
                }

                // ===== LOADING STATE (Improvement #7) =====
                document.getElementById('registerForm')?.addEventListener('submit', function(e) {
                    const btn = document.getElementById('submitBtn');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Mendaftarkan...';
                });
            </script>

</body>

</html>
