<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SINAKERTRANS</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md border border-gray-200">

        {{-- HEADER --}}
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-600">SINAKERTRANS</h2>
            <p class="text-gray-500 text-sm">
                Masukkan email Anda untuk menerima link reset password.
            </p>
        </div>

        {{-- STATUS SUCCESS --}}
        @if (session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                {{ session('status') }}
            </div>
        @endif

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Email Terdaftar
                </label>

                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 @error('email') border-red-500 @enderror"
                    placeholder="nama@email.com">

                @error('email')
                    <p class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition">
                Kirim Link Reset
            </button>
        </form>

        {{-- BACK TO LOGIN --}}
        <div class="mt-6 text-center text-sm">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                ← Kembali ke Login
            </a>
        </div>

    </div>

</body>

</html>
