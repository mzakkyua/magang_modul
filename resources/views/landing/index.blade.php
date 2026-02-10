<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SINAKERTRANS - Cari Magang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="bg-gray-50 font-sans text-gray-800">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-blue-600">SINAKERTRANS</span>
                </div>

                <div class="hidden md:flex items-center space-x-3">
                    {{-- CEK 1: APAKAH YANG LOGIN ITU ADMIN? --}}
                    @if (Auth::guard('web')->check())
                        <span class="text-sm text-gray-600 mr-2">Halo, <strong>Admin</strong></span>
                        <a href="{{ route('admin.dashboard') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Ke Dashboard Admin
                        </a>

                        {{-- CEK 2: APAKAH YANG LOGIN ITU ANAK MAGANG? --}}
                    @elseif(Auth::guard('magang')->check())
                        <span class="text-sm text-gray-600 mr-2">Halo,
                            <strong>{{ Auth::guard('magang')->user()->nama_lengkap ?? 'Peserta' }}</strong></span>
                        <a href="{{ route('dashboard') }}"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            Dashboard Magang
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700 ml-2">Logout</button>
                        </form>

                        {{-- CEK 3: JIKA BELUM LOGIN (TAMU) --}}
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 text-white px-5 py-2 rounded-full font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                            Daftar Sekarang
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="bg-blue-600 py-20 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Temukan Tempat Magang Impianmu</h1>
        <p class="text-lg md:text-xl text-blue-100 mb-8 max-w-2xl mx-auto">Bergabunglah dengan Dinas Tenaga Kerja untuk
            pengalaman magang yang nyata dan profesional.</p>

        <div class="max-w-xl mx-auto px-4">
            <form action="{{ route('landing.index') }}" method="GET"
                class="flex gap-2 bg-white p-2 rounded-lg shadow-lg">
                <input type="text" name="search" placeholder="Cari posisi magang..."
                    class="flex-1 px-4 py-2 text-gray-800 outline-none rounded-md">
                <button type="submit"
                    class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-2 rounded-md font-semibold transition">
                    Cari
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-l-4 border-blue-600 pl-4">Lowongan Terbaru</h2>

        @if ($vacancies->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($vacancies as $job)
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $job->title }}</h3>
                                    <span class="text-sm text-blue-600 font-semibold bg-blue-50 px-2 py-1 rounded">
                                        Divisi {{ $job->division_name }}
                                    </span>
                                </div>
                                <span
                                    class="text-xs font-bold px-2 py-1 rounded {{ $job->type == 'magang' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ strtoupper($job->type) }}
                                </span>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ Str::limit($job->description, 100) }}
                            </p>

                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-6">
                                <span class="flex items-center gap-1"><i class="bi bi-people"></i> Kuota:
                                    {{ $job->quota_slots }}</span>
                                <span class="flex items-center gap-1"><i class="bi bi-calendar"></i>
                                    {{ \Carbon\Carbon::parse($job->start_date)->format('d M') }} -
                                    {{ \Carbon\Carbon::parse($job->end_date)->format('d M Y') }}
                                </span>
                            </div>

                            <a href="{{ route('landing.show', $job->id) }}"
                                class="block w-full text-center bg-gray-900 hover:bg-gray-800 text-white py-2 rounded-lg font-medium transition">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $vacancies->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                <i class="bi bi-emoji-frown text-4xl text-gray-400 mb-3 block"></i>
                <h3 class="text-lg font-medium text-gray-900">Belum ada lowongan dibuka</h3>
                <p class="text-gray-500">Silahkan kembali lagi nanti untuk info terbaru.</p>
            </div>
        @endif
    </div>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} Dinas Tenaga Kerja. Sistem Manajemen Magang.</p>
        </div>
    </footer>

</body>

</html>
