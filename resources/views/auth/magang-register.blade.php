<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SINAKERTRANS</title>

<<<<<<< HEAD
    @vite(['resources/css/app.css', 'resources/js/app.js'])
=======
    {{-- Tailwind CDN (Production nanti sebaiknya pakai build Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
>>>>>>> feature/auth
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen py-10">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">

<<<<<<< HEAD
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-600">Buat Akun Baru</h2>
            <p class="text-gray-500 text-sm">Daftar sebagai peserta magang / penelitian.</p>
        </div>

        {{-- ERROR MESSAGE --}}
=======
        {{-- ================= HEADER ================= --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600 mb-2">SINAKERTRANS</h1>
            <h2 class="text-xl font-semibold text-gray-800">Buat Akun Baru</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar sebagai peserta magang / penelitian.</p>
        </div>

        {{-- ================= GLOBAL ERROR ALERT ================= --}}
>>>>>>> feature/auth
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================= FORM REGISTER ================= --}}
        <form action="{{ route('register.post') }}" method="POST" id="registerForm">
            @csrf

<<<<<<< HEAD
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
=======
            {{-- ================= NAMA LENGKAP ================= --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-person text-gray-400"></i>
                    </div>

                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                        class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('nama_lengkap') border-red-500 @enderror"
                        placeholder="Sesuai KTP/KTM" required autofocus>
                </div>

                @error('nama_lengkap')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ================= EMAIL ================= --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Aktif</label>

                <div class="relative">
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
            </div>

            {{-- ================= EDUCATION LEVEL (WAJIB SESUAI CONTROLLER) ================= --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Jenjang Pendidikan</label>

                <select name="education_level"
                    class="w-full py-2 px-3 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('education_level') border-red-500 @enderror"
                    required>

                    <option value="">-- Pilih Jenjang --</option>
                    <option value="SMA/SMK" {{ old('education_level') == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                    <option value="D3" {{ old('education_level') == 'D3' ? 'selected' : '' }}>D3</option>
                    <option value="S1" {{ old('education_level') == 'S1' ? 'selected' : '' }}>S1</option>
                    <option value="S2" {{ old('education_level') == 'S2' ? 'selected' : '' }}>S2</option>
                </select>

                @error('education_level')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ================= PASSWORD ================= --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-lock text-gray-400"></i>
                    </div>

                    <input type="password" id="password" name="password" minlength="8"
                        class="w-full pl-10 pr-10 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('password') border-red-500 @enderror"
                        placeholder="Minimal 8 karakter" required>

                    {{-- Toggle Show / Hide Password --}}
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i class="bi bi-eye-slash text-xl"></i>
                    </button>
                </div>

                @error('password')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ================= KONFIRMASI PASSWORD ================= --}}
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Ulangi Password</label>

                <input type="password" name="password_confirmation" minlength="8"
                    class="w-full py-2 px-3 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="Ketik ulang password" required>
            </div>

            {{-- ================= PASSWORD STRENGTH ================= --}}
>>>>>>> feature/auth
            <div id="strengthBar" class="mb-2 h-2 bg-gray-200 rounded overflow-hidden hidden">
                <div id="fill" class="w-0 h-full bg-red-500 transition-all duration-300"></div>
            </div>
            <p id="strength" class="text-xs text-gray-500 mb-4"></p>

<<<<<<< HEAD
            {{-- TERMS
            <div class="mb-6">
                <label class="flex items-center text-sm">
                    <input type="checkbox" name="terms" class="mr-2" required>
                    <span>Saya setuju dengan <a href="#" class="text-blue-600 font-semibold">Syarat &
                            Ketentuan</a></span>
                </label>
            </div> --}}

            {{-- SUBMIT --}}
=======
            {{-- ================= TERMS & CONDITION ================= --}}
            <div class="mb-6">
                <label class="flex items-center text-sm text-gray-700">
                    <input type="checkbox" name="terms" class="mr-2 rounded @error('terms') border-red-500 @enderror"
                        {{ old('terms') ? 'checked' : '' }} required>
                    <span>Saya setuju dengan <a href="#"
                            class="text-blue-600 hover:underline font-semibold">Syarat & Ketentuan</a></span>
                </label>

                @error('terms')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ================= SUBMIT BUTTON ================= --}}
>>>>>>> feature/auth
            <button type="submit" id="submitBtn"
                class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                Daftar
            </button>

        </form>

<<<<<<< HEAD
        <div class="mt-6 text-center text-sm">
            <p class="text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">
                    Masuk di sini
=======
        {{-- ================= FOOTER ================= --}}
        <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm">
            <p class="text-gray-600">Sudah punya akun?
                <a href="{{ route('login') }}"
                    class="text-blue-600 font-bold hover:text-blue-800 hover:underline transition">
                    Masuk disini
                </a>
            </p>

            <div class="mt-4">
                <a href="{{ route('landing.index') }}"
                    class="text-gray-400 hover:text-gray-600 text-xs flex items-center justify-center gap-1">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
>>>>>>> feature/auth
                </a>
            </p>
            <a href="{{ route('landing.index') }}" class="text-gray-400 text-xs hover:underline">
                ← Kembali ke Beranda
            </a>
        </div>

    </div>

    {{-- ================= SCRIPT AREA ================= --}}
    <script>
<<<<<<< HEAD
        /* =============================
            PASSWORD STRENGTH INDICATOR
           ============================= */
        const passwordField = document.querySelector('input[name="password"]');
=======
        // ================= PASSWORD TOGGLE =================
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

        // ================= PASSWORD STRENGTH CHECK =================
>>>>>>> feature/auth
        const strengthBar = document.getElementById('strengthBar');
        const fill = document.getElementById('fill');
        const strength = document.getElementById('strength');

        passwordInput?.addEventListener('input', function(e) {

            const pwd = e.target.value;
<<<<<<< HEAD
            if (!pwd.length) {
=======

            if (pwd.length === 0) {
>>>>>>> feature/auth
                strengthBar.classList.add('hidden');
                strength.textContent = '';
                return;
            }

            const result = calculateStrength(pwd);
            strengthBar.classList.remove('hidden');
<<<<<<< HEAD
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
=======

            let percent = 0;
            let text = 'Lemah';
            let color = 'red';

            if (pwd.length >= 8) percent = 25;
            if (pwd.length >= 12) percent = 50;
            if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) percent = 60;
            if (/[0-9]/.test(pwd)) percent = 75;
            if (/[@!#$%^&*]/.test(pwd)) percent = 100;

            if (percent >= 75) {
                text = 'Kuat';
                color = 'blue';
            }

            if (percent === 100) {
                text = 'Sangat Kuat';
                color = 'green';
            }

            fill.style.width = percent + '%';
            fill.className = `h-full transition-all duration-300 bg-${color}-500`;
            strength.textContent = text;
            strength.className = `text-xs text-${color}-600 mb-4`;
        });

        // ================= LOADING STATE =================
>>>>>>> feature/auth
        document.getElementById('registerForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Mendaftarkan...';
        });
    </script>

</body>

</html>
