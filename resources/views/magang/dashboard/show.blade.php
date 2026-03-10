<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vacancies->title }} - SINAKERTRANS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
</head>

<body class="bg-gray-50 font-sans text-gray-800">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('landing.index') }}" class="flex items-center text-gray-600 hover:text-blue-600 transition">
                <i class="bi bi-arrow-left mr-2"></i> Kembali ke Daftar
            </a>
            <span class="font-bold text-blue-600 text-xl">SINAKERTRANS</span>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-10">

        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $vacancies->title }}</h1>
                    <div class="flex gap-3 mt-2">
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">
                            Divisi {{ $vacancies->division_name }}
                        </span>
                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">
                            {{ strtoupper($vacancies->type) }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Batas Lamaran</p>
                    <p class="font-bold text-red-600">{{ \Carbon\Carbon::parse($vacancies->end_date)->format('d F Y') }}
                    </p>
                </div>
            </div>

            <div class="prose max-w-none text-gray-600 border-t pt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Deskripsi Pekerjaan</h3>
                <p class="mb-6 whitespace-pre-line">{{ $vacancies->description }}</p>

                <h3 class="text-lg font-bold text-gray-800 mb-2">Kriteria / Persyaratan</h3>
                <ul class="list-disc pl-5 mb-6">
                    <li>Mahasiswa aktif semester 5 ke atas.</li>
                    <li>Berkomitmen mengikuti magang selama periode yang ditentukan.</li>
                    <li>Memiliki laptop pribadi.</li>
                </ul>
            </div>

            <div class="mt-8 border-t pt-6">

                {{-- KONDISI 1: JIKA PENGUNJUNG ADALAH ADMIN --}}
                @if (Auth::guard('web')->check())
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-sm text-yellow-700">
                            Halo Admin! Anda sedang dalam mode pratinjau. Admin tidak bisa melamar.
                        </p>
                    </div>

                    {{-- KONDISI 2: JIKA SUDAH LOGIN SEBAGAI MAHASISWA --}}
                @elseif(Auth::guard('magang')->check())
                    <button type="button" data-modal-target="applicationModal" data-modal-toggle="applicationModal"
                        class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-1">
                        🚀 Lamar Posisi Ini Sekarang
                    </button>

                    {{-- KONDISI 3: JIKA BELUM LOGIN (TAMU) --}}
                @else
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 text-center">
                        <p class="text-gray-600 mb-3">Tertarik dengan posisi ini? Silakan login atau daftar akun
                            terlebih dahulu.</p>

                        <div class="flex justify-center gap-4">
                            <a href="{{ route('login') }}"
                                class="bg-white text-blue-600 border border-blue-600 font-bold py-2 px-6 rounded-lg hover:bg-blue-50 transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition shadow">
                                Daftar Akun
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (Auth::guard('magang')->check())
        <div id="applicationModal" tabindex="-1" aria-hidden="true"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-start justify-between p-4 border-b rounded-t">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Konfirmasi Lamaran
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                            data-modal-hide="applicationModal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="p-6 space-y-6">
                        <p class="text-base leading-relaxed text-gray-500">
                            Apakah Anda yakin ingin melamar untuk posisi <strong>{{ $vacancies->title }}</strong>?
                            Pastikan profil dan CV Anda sudah diperbarui.
                        </p>

                        <form action="{{ route('applications.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="vacancy_id" value="{{ $vacancies->id }}">

                            <div class="mb-4">
                                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Catatan
                                    Singkat
                                    (Opsional)</label>
                                <textarea id="notes" name="notes" rows="3"
                                    class="block w-full p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Kenapa Anda tertarik?"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                Kirim Lamaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

</body>

</html>
