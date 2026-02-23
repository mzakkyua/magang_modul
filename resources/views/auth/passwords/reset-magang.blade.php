<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SINAKERTRANS</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md border border-gray-200">

        {{-- ================= HEADER ================= --}}
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-600">SINAKERTRANS</h2>
            <p class="text-gray-500 text-sm">
                Buat password baru untuk akun Anda.
            </p>
        </div>

        {{-- ================= ERROR GLOBAL ================= --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- ================= FORM RESET ================= --}}
        <form method="POST" action="{{ route('password.update') }}" id="resetForm">
            @csrf

            {{-- TOKEN WAJIB --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- EMAIL (readonly supaya tidak bisa diubah sembarangan) --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Email
                </label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" readonly
                    class="w-full px-3 py-2 border rounded-lg bg-gray-100">
            </div>

            {{-- PASSWORD BARU --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Password Baru
                </label>

                <div class="relative">
                    <input type="password" name="password" id="password" minlength="8" required
                        class="w-full pr-10 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('password') border-red-500 @enderror"
                        placeholder="Minimal 8 karakter">

                    <button type="button" id="togglePassword"
                        class="absolute right-3 top-2 text-gray-500 hover:text-gray-700">
                        <i class="bi bi-eye-slash text-xl"></i>
                    </button>
                </div>

                @error('password')
                    <p class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- KONFIRMASI PASSWORD --}}
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Konfirmasi Password
                </label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="Ulangi password">
            </div>

            {{-- SUBMIT --}}
            <button type="submit" id="submitBtn"
                class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition">
                Reset Password
            </button>
        </form>

        {{-- BACK TO LOGIN --}}
        <div class="mt-6 text-center text-sm">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                ← Kembali ke Login
            </a>
        </div>

    </div>

    {{-- ================= SCRIPT ================= --}}
    <script>
        // Toggle password visibility
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

        // Loading state
        document.getElementById('resetForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = 'Memproses...';
        });
    </script>

</body>

</html>
