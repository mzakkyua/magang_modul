<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SINAKERTRANS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen py-10">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md border border-gray-200">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600 mb-2">SINAKERTRANS</h1>
            <h2 class="text-xl font-semibold text-gray-800">Buat Akun Baru</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar sebagai peserta magang/penelitian.</p>
        </div>

        {{-- ALERT ERROR UMUM (JIKA ADA) --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Terjadi Kesalahan</p>
                <p class="text-sm">Mohon periksa kembali inputan Anda.</p>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            {{-- INPUT NAMA LENGKAP --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-person text-gray-400"></i>
                    </div>
                    {{-- PENTING: name="nama_lengkap" sesuai controller --}}
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                        class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('nama_lengkap') border-red-500 @enderror"
                        placeholder="Sesuai KTP/KTM" required autofocus>
                </div>
                @error('nama_lengkap')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- INPUT EMAIL --}}
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

            {{-- INPUT PASSWORD --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
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
            </div>

            {{-- INPUT KONFIRMASI PASSWORD --}}
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Ulangi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-shield-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password_confirmation"
                        class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                        placeholder="Ketik ulang password" required>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm">
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
        </div>
    </div>

</body>

</html>
