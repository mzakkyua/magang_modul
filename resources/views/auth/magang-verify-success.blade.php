<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full text-center">
        <!-- Ikon Centang Hijau -->
        <div class="mb-6 flex justify-center">
            <svg class="w-24 h-24 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h2 class="text-3xl font-bold text-gray-800 mb-2">Verifikasi Berhasil! 🎉</h2>

        <p class="text-gray-600 mb-6 leading-relaxed">
            Terima kasih, email Anda telah divalidasi. Anda sudah bisa kembali ke halaman pendaftaran sebelumnya.
        </p>

        <!-- Tombol Tutup Manual -->
        <button onclick="window.close()"
            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg transition duration-300">
            Tutup Halaman Ini
        </button>
    </div>

    <!-- Script Tutup Otomatis -->
    <script>
        // Halaman ini akan mencoba menutup dirinya sendiri dalam 3 detik
        setTimeout(function() {
            window.close();
        }, 3000);
    </script>
</body>

</html>
