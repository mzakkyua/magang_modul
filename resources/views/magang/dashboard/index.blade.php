@extends('layouts.layoutlanding')
@section('title', 'Homepage')

@section('content')
    <!-- hero section -->
    <div class="relative w-full h-80 md:h-95 overflow-hidden" id="home">
        <div class="absolute inset-0">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b2/New_office.jpg" alt="Background Image"
                class="object-cover object-center w-full h-full" />

            {{-- Lapisan Hitam Transparan (Overlay) --}}
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

    </div>
    <div class="absolute inset-0 flex items-center px-9">
        <div class="md:w-1/2 mb-4 md:mb-0">
            <h1 class="text-white font-black text-3xl md:text-5xl leading-tight mb-4 drop-shadow-lg">Temukan Tempat Magang
                Impianmu</h1>
            <p class="text-gray-100 text-lg md:text-xl font-medium max-w-xl leading-relaxed">Bergabunglah dengan Dinas
                Tenaga Kerja untuk pengalaman magang yang nyata</p>
        </div>
    </div>
    </div>

    <!-- service section -->
    <section>
        @include('program.section')
        @include('program.modal')
    </section>


    {{-- SEARCH --}}
    <div class="max-w-2xl mx-auto mb-10">

        <form action="{{ route('dashboard.index') }}" method="GET" class="flex gap-2 bg-white p-2 rounded-lg shadow">

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari posisi magang..."
                class="flex-1 px-4 py-2 outline-none rounded-md">

            <button class="bg-blue-700 text-white px-6 py-2 rounded-md">
                Cari
            </button>

        </form>

    </div>



    {{-- HASIL SEARCH --}}
    @if ($search)
        <div class="max-w-7xl mx-auto mb-6 text-gray-600">

            Hasil pencarian untuk :
            <span class="font-semibold text-blue-700">
                "{{ $search }}"
            </span>

            <a href="{{ route('dashboard.index') }}" class="ml-2 text-blue-600 text-sm">
                Reset
            </a>

        </div>
    @endif



    {{-- LOWONGAN --}}
    <div class="max-w-7xl mx-auto">

        <h2 class="text-3xl font-bold mb-6 border-l-4 border-blue-600 pl-4">
            Lowongan Tersedia
        </h2>



        {{-- FILTER TAB --}}
        <div class="flex gap-6 border-b mb-8">

            <button data-tab="semua" id="btn-semua" class="tab-btn border-b-2 border-blue-600 pb-2 font-semibold">

                Semua

            </button>

            <button data-tab="magang" id="btn-magang" class="tab-btn pb-2 text-gray-500">

                Magang

            </button>

            <button data-tab="penelitian" id="btn-penelitian" class="tab-btn pb-2 text-gray-500">

                Penelitian

            </button>

        </div>



        {{-- TAB SEMUA --}}
        <div id="tab-semua">

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse ($vacanciesMagang as $job)
                    <x-job-card :job="$job" />

                @empty
                @endforelse


                @forelse ($vacanciesPenelitian as $job)
                    <x-job-card :job="$job" />

                @empty
                @endforelse


                @if ($vacanciesMagang->isEmpty() && $vacanciesPenelitian->isEmpty())
                    <div class="col-span-3 text-center py-20 text-gray-500">

                        Maaf, lowongan tidak ditemukan

                    </div>
                @endif

            </div>

        </div>



        {{-- TAB MAGANG --}}
        <div id="tab-magang" class="hidden">

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse ($vacanciesMagang as $job)
                    <x-job-card :job="$job" />

                @empty

                    <div class="col-span-3 text-center py-20 text-gray-500">
                        Maaf, lowongan magang tidak ditemukan
                    </div>
                @endforelse

            </div>

        </div>



        {{-- TAB PENELITIAN --}}
        <div id="tab-penelitian" class="hidden">

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse ($vacanciesPenelitian as $job)
                    <x-job-card :job="$job" />

                @empty

                    <div class="col-span-3 text-center py-20 text-gray-500">
                        Maaf, lowongan penelitian tidak ditemukan
                    </div>
                @endforelse

            </div>

        </div>

    </div>

    </section>

    {{-- ================= TIMELINE & CALENDAR ================= --}}
    <section class="bg-gray-100 py-16">
        <div id="timeline-wrapper"
            class="container mx-auto grid md:grid-cols-2 gap-8 items-start transition-all duration-500 px-4">

            {{-- KOLOM KIRI: TEKS & VERTICAL TIMELINE --}}
            <div id="text-section" class="flex flex-col gap-8">

                {{-- Bagian Asli Buatan Temanmu --}}
                <div>
                    <h2 class="text-3xl font-bold mb-4 text-gray-800">Timeline Magang</h2>
                    <p class="text-gray-600 mb-6">
                        Peserta dapat melihat periode kegiatan magang, tanggal mulai hingga selesai pada kalender di
                        samping.
                    </p>

                    <div class="inline-flex bg-white p-1 rounded-xl shadow-sm border border-gray-200">
                        <button onclick="changeView('compact')" id="btn-compact"
                            class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white transition-all">
                            Simpel
                        </button>
                        <button onclick="changeView('detailed')" id="btn-detailed"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-all">
                            Zoom In
                        </button>
                    </div>
                </div>

                {{-- TAMBAHAN BARU: VERTICAL TIMELINE (JIKA DITERIMA) --}}
                @php
                    // Logika mencari lamaran yang 'accepted' untuk user yang sedang login (baik sebagai ketua maupun anggota)
                    $userId = Auth::guard('magang')->id();
                    $acceptedMember = \App\Models\ApplicationMemberMagang::where('user_id', $userId)
                        ->whereHas('application', function ($q) {
                            $q->where('status', 'accepted');
                        })
                        ->with('application.vacancy')
                        ->first();

                    $activeApplication = $acceptedMember ? $acceptedMember->application : null;
                @endphp

                @if ($activeApplication)
                    @php
                        $vacancy = $activeApplication->vacancy;
                        $now = \Carbon\Carbon::now();
                        $startDate = \Carbon\Carbon::parse($vacancy->start_date);
                        $endDate = \Carbon\Carbon::parse($vacancy->end_date);

                        $isStarted = $now->gte($startDate);
                        $isFinished = $now->gt($endDate);
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <h3
                            class="text-lg font-bold text-[#37517e] mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
                            <i class="bi bi-geo-alt-fill text-blue-500"></i> Status Perjalanan Anda
                        </h3>

                        <div class="relative border-l-2 border-blue-100 ml-3 md:ml-4 space-y-8">
                            {{-- STEP 1: DITERIMA --}}
                            <div class="relative pl-8">
                                <div
                                    class="absolute -left-2.25 top-0 w-4 h-4 rounded-full bg-green-500 ring-4 ring-green-50">
                                </div>
                                <h4 class="text-sm font-bold text-gray-900">Diterima di {{ $vacancy->division_name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">Selamat! Anda telah lolos seleksi.</p>
                            </div>

                            {{-- STEP 2: PERSIAPAN --}}
                            <div class="relative pl-8">
                                <div
                                    class="absolute -left-2.25top-0 w-4 h-4 rounded-full {{ $isStarted ? 'bg-green-500 ring-4 ring-green-50' : 'bg-blue-500 ring-4 ring-blue-50 animate-pulse' }}">
                                </div>
                                <h4 class="text-sm font-bold {{ $isStarted ? 'text-gray-900' : 'text-blue-700' }}">Persiapan
                                    Magang</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    @if ($isStarted)
                                        Masa persiapan selesai.
                                    @else
                                        Mulai pada <strong class="text-gray-700">{{ $startDate->format('d M Y') }}</strong>.
                                    @endif
                                </p>
                            </div>

                            {{-- STEP 3: PELAKSANAAN --}}
                            <div class="relative pl-8">
                                <div
                                    class="absolute -left-2.25 top-0 w-4 h-4 rounded-full {{ $isFinished ? 'bg-green-500 ring-4 ring-green-50' : ($isStarted ? 'bg-blue-500 ring-4 ring-blue-50 animate-pulse' : 'bg-gray-200 ring-4 ring-white') }}">
                                </div>
                                <h4
                                    class="text-sm font-bold {{ $isFinished ? 'text-gray-900' : ($isStarted ? 'text-blue-700' : 'text-gray-400') }}">
                                    Pelaksanaan Magang</h4>
                                <p
                                    class="text-xs {{ $isStarted && !$isFinished ? 'text-gray-600' : 'text-gray-400' }} mt-1">
                                    @if ($isFinished)
                                        Telah selesai.
                                    @elseif($isStarted)
                                        Sedang berlangsung hingga <strong
                                            class="text-gray-700">{{ $endDate->format('d M Y') }}</strong>.
                                    @else
                                        Menunggu waktu pelaksanaan.
                                    @endif
                                </p>
                            </div>

                            {{-- STEP 4: SELESAI --}}
                            <div class="relative pl-8">
                                <div
                                    class="absolute -left-2.25 top-0 w-4 h-4 rounded-full {{ $isFinished ? 'bg-blue-500 ring-4 ring-blue-50 animate-pulse' : 'bg-gray-200 ring-4 ring-white' }}">
                                </div>
                                <h4 class="text-sm font-bold {{ $isFinished ? 'text-blue-700' : 'text-gray-400' }}">Selesai
                                    & Penilaian</h4>
                                <p class="text-xs text-gray-400 mt-1">
                                    @if ($isFinished)
                                        Cek menu Penilaian untuk melihat hasil Anda.
                                    @else
                                        Terbuka setelah magang berakhir.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Jika Belum Diterima Sama Sekali --}}
                    <div class="bg-blue-50 rounded-2xl border border-blue-100 p-6 md:p-8 text-center">
                        <div
                            class="w-16 h-16 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <h3 class="text-sm font-bold text-[#37517e] mb-2">Belum Ada Kegiatan Aktif</h3>
                        <p class="text-xs text-gray-500">Anda belum memiliki jadwal magang yang sedang berjalan. Silakan
                            tunggu konfirmasi lamaran Anda.</p>
                    </div>
                @endif

            </div>

            {{-- KOLOM KANAN: CALENDAR BUATAN TEMANMU --}}
            <div id="calendar-container"
                class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 transition-all duration-500">
                @include('calendar')
            </div>

        </div>
    </section>

    <!-- gallery -->
    <section class="text-gray-700 body-font" id="gallery">
        <div class="flex justify-center text-3xl font-bold text-gray-800 text-center py-10">
            Dokumentasi Magang
        </div>

        <div class="grid grid-cols-1 place-items-center sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4">

            <div class="group relative">
                <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-1.jpeg" alt="Image 1"
                    class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105" />
            </div>

            <div class="group relative">
                <img src="https://www.suarasurabaya.net/wp-content/uploads/2023/01/Kegiatan-Uji-Kompetensi-Kejuruan-HMI-Berbasis-PLC-yang-dilakukan-UPT-Balai-Latihan-Kerja-Surabaya-735x493.jpg.webp"
                    alt="Image 1"
                    class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105" />
            </div>

            <div class="group relative">
                <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-2.jpeg"
                    alt="Image 1"
                    class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105" />
            </div>
            <div class="group relative">
                <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-3.jpeg"
                    alt="Image 1"
                    class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105" />
            </div>
        </div>
    @endsection
