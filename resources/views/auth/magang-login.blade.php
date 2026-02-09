<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SINAKERTRANS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-blue-600">SINAKERTRANS</h2>
            <p class="text-gray-500 text-sm">Masuk untuk melanjutkan aktivitas magang.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Login Gagal!</strong>
                <span class="block sm:inline">{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="nama@email.com" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="********" required>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-200">
                Masuk
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <p class="text-gray-600">Belum punya akun? <a href="{{ route('register') }}"
                    class="text-blue-600 font-bold hover:underline">Daftar Magang</a></p>
            <p class="mt-2 text-gray-500 text-xs">Atau <a href="{{ route('landing.index') }}"
                    class="hover:underline">Kembali ke Beranda</a></p>
        </div>
    </div>

</body>

</html>
