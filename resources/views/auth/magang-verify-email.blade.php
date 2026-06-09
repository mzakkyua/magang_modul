<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Sinakertrans</title>

    {{-- Favicon dengan Auto Cache-Busting --}}
    <link rel="icon" type="image/svg+xml"
        href="{{ asset('assets/images/favicon.svg') }}?v={{ file_exists(public_path('assets/images/favicon.svg')) ? filemtime(public_path('assets/images/favicon.svg')) : '1' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])
</head>

<body class="bg-blue-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full text-center">
        <div class="mb-6">
            <svg class="w-20 h-20 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                </path>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-2">Cek Kotak Masuk Anda!</h2>

        <p class="text-gray-600 mb-6 leading-relaxed">
            Hampir selesai! Kami telah mengirimkan sebuah link ke email Anda.
            Silakan klik link tersebut untuk membuktikan bahwa ini benar-benar email Anda.
        </p>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 text-sm text-left"
                role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="border-t border-gray-200 pt-6 mt-2">
            <p class="text-sm text-gray-500 mb-3">Belum menerima email dari kami?</p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-300">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>
        </div>

        <div class="mt-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-500 hover:text-red-700 hover:underline">
                    &larr; Keluar atau Ganti Akun
                </button>
            </form>
        </div>
    </div>

    <script>
        // Bertanya ke server setiap 5 detik (5000 milidetik)
        setInterval(function() {
            fetch('{{ route('verification.check') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Memberi tahu Laravel ini adalah request AJAX
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Jika server menjawab 'verified: true' (sudah diklik di HP)
                    if (data.verified) {
                        // Tarik user pindah ke Dashboard sekarang juga!
                        window.location.href = '{{ route('dashboard.index') }}';
                    }
                })
                .catch(error => console.log('Menunggu verifikasi...'));
        }, 5000); // Waktu diubah jadi 5 detik agar server lebih santai
    </script>
</body>

</html>
