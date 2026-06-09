@extends('layouts.landing')
@section('title', 'Dashboard - SINAKERTRANS')

@section('content')

    {{-- ===================== HERO SECTION ===================== --}}
    <section class="relative w-full h-130 md:h-150 overflow-hidden" id="home">

        <div class="absolute inset-0">
            <img src="{{ asset('assets/images/hero-bg.png') }}?v={{ file_exists(public_path('assets/images/hero-bg.png')) ? filemtime(public_path('assets/images/hero-bg.png')) : '1' }}"
                alt="Background Kantor" class="object-cover object-center w-full h-full" />
            <div class="absolute inset-0 bg-linear-to-r from-blue-900/90 via-blue-800/75 to-blue-700/30"></div>
        </div>

        <div class="relative z-10 h-full flex items-center pt-3 md:pt-5">
            <div class="max-w-7xl mx-auto px-6 w-full">
                <div class="max-w-xl">

                    @if (Auth::guard('magang')->check())
                        <span
                            class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-blue-100 text-xs font-semibold px-4 py-2 rounded-full mb-5 backdrop-blur-sm">
                            <i class="bi bi-person-check-fill text-blue-300"></i>
                            Selamat datang,
                            {{ Auth::guard('magang')->user()->profile->full_name ?? Auth::guard('magang')->user()->name }}!
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-blue-200 text-xs font-semibold px-4 py-2 rounded-full mb-5 backdrop-blur-sm">
                            <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                            Dinas Tenaga Kerja & Transmigrasi Prov. Jawa Timur
                        </span>
                    @endif

                    <h1 class="text-white font-extrabold text-4xl md:text-5xl leading-tight mb-4 drop-shadow-lg">
                        Temukan Tempat <br>
                        <span class="text-blue-300">Magang Impianmu</span>
                    </h1>

                    <p class="text-blue-100 text-base md:text-lg font-medium max-w-md leading-relaxed mb-8">
                        Bergabunglah bersama Dinas Tenaga Kerja Jawa Timur dan dapatkan pengalaman magang yang nyata dan
                        bermakna.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <a href="#lowongan"
                            class="bg-white text-blue-700 px-6 py-3 rounded-full font-bold text-sm hover:bg-blue-50 transition shadow-xl shadow-black/20 flex items-center gap-2">
                            <i class="bi bi-search"></i> Lihat Lowongan
                        </a>
                        <a href="#timeline-wrapper"
                            class="border border-white/40 text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-white/10 transition backdrop-blur-sm flex items-center gap-2">
                            <i class="bi bi-calendar3"></i> Timeline Saya
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STATS BAR — Dinamis mengambil data dari Database             --}}
        {{-- ============================================================ --}}
        <div class="absolute bottom-0 left-0 z-20 w-full bg-white/10 backdrop-blur-md border-t border-white/20">
            <div class="max-w-6xl mx-auto px-6 py-4">
                <div class="grid grid-cols-3 divide-x divide-white/20">

                    <div class="text-center px-4">
                        <p class="text-white font-extrabold text-xl md:text-2xl tabular-nums">
                            {{ $globalStats['pesertaAktif'] }}+</p>
                        <p class="text-blue-200/80 text-[9px] md:text-[10px] font-semibold uppercase tracking-wider mt-0.5">
                            Peserta Aktif</p>
                    </div>

                    <div class="text-center px-4">
                        <p class="text-white font-extrabold text-xl md:text-2xl tabular-nums">
                            {{ $globalStats['jumlahDivisi'] }}+</p>
                        <p class="text-blue-200/80 text-[9px] md:text-[10px] font-semibold uppercase tracking-wider mt-0.5">
                            Divisi Tersedia</p>
                    </div>

                    <div class="text-center px-4">
                        <p class="text-white font-extrabold text-xl md:text-2xl tabular-nums">
                            {{ $globalStats['alumniMagang'] }}+</p>
                        <p class="text-blue-200/80 text-[9px] md:text-[10px] font-semibold uppercase tracking-wider mt-0.5">
                            Alumni Magang</p>
                    </div>

                </div>
            </div>
        </div>

    </section>



    {{-- ===================== SEARCH & LOWONGAN SECTION ===================== --}}
    <section class="px-4 md:px-6 py-16" id="lowongan">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10">
                <span class="text-blue-600 font-semibold text-xs uppercase tracking-widest">Kesempatan Terbuka</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mt-2">Lowongan Tersedia</h2>
                <p class="text-gray-500 mt-2 max-w-md mx-auto text-sm">
                    Temukan posisi magang atau penelitian yang sesuai dengan bidang dan minatmu.
                </p>
            </div>

            {{-- SEARCH --}}
            <div class="max-w-2xl mx-auto mb-8">
                <x-search-lowongan action="{{ route('landing.index') }}#lowongan" search="{{ $search }}" />
                {{-- Untuk dashboard, ganti route di atas:
         action="{{ route('dashboard.index') }}"
    --}}
            </div>

            {{-- TAB FILTER — lebih compact --}}
            <div class="flex gap-2 mb-8">
                <button data-tab="semua"
                    class="tab-btn active-tab px-4 py-1.5 rounded-full text-xs font-semibold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 8a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zm6-6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zm0 8a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Semua
                </button>
                <button data-tab="magang"
                    class="tab-btn inactive-tab px-4 py-1.5 rounded-full text-xs font-semibold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                        <path
                            d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                    </svg>
                    Magang
                </button>
                <button data-tab="penelitian"
                    class="tab-btn inactive-tab px-4 py-1.5 rounded-full text-xs font-semibold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                    </svg>
                    Penelitian
                </button>
            </div>

            {{-- TAB SEMUA --}}
            <div id="tab-semua" class="tab-content">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($vacanciesMagang as $job)
                        {{-- PENTING: tambahkan data-search-card + data attributes --}}
                        <div data-search-card data-title="{{ strtolower($job->title) }}"
                            data-division="{{ strtolower($job->division_name) }}" data-type="magang">
                            <x-job-card :job="$job" />
                        </div>
                    @empty
                    @endforelse
                    @forelse ($vacanciesPenelitian as $job)
                        <div data-search-card data-title="{{ strtolower($job->title) }}"
                            data-division="{{ strtolower($job->division_name) }}" data-type="penelitian">
                            <x-job-card :job="$job" />
                        </div>
                    @empty
                    @endforelse

                    {{-- Empty state — PENTING: tambahkan data-empty-state --}}
                    @if ($vacanciesMagang->isEmpty() && $vacanciesPenelitian->isEmpty())
                        <div data-empty-state class="col-span-3 text-center py-24">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="text-gray-500 font-semibold">Tidak ada lowongan ditemukan</p>
                            <p class="text-gray-400 text-sm mt-1">Coba ubah kata kunci pencarian</p>
                        </div>
                    @else
                        {{-- Empty state saat filter aktif (hidden by default) --}}
                        <div data-empty-state class="col-span-3 text-center py-24 hidden">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="text-gray-500 font-semibold">Tidak ada hasil untuk pencarian ini</p>
                            <p class="text-gray-400 text-sm mt-1">Coba kata kunci yang berbeda</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- TAB MAGANG --}}
            <div id="tab-magang" class="tab-content hidden">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($vacanciesMagang as $job)
                        <div data-search-card data-title="{{ strtolower($job->title) }}"
                            data-division="{{ strtolower($job->division_name) }}" data-type="magang">
                            <x-job-card :job="$job" />
                        </div>
                    @empty
                        <div data-empty-state class="col-span-3 text-center py-24">
                            <p class="text-gray-500 font-semibold">Tidak ada lowongan magang</p>
                        </div>
                    @endforelse
                    <div data-empty-state class="col-span-3 text-center py-24 hidden">
                        <p class="text-gray-500 font-semibold">Tidak ada hasil untuk pencarian ini</p>
                    </div>
                </div>
            </div>

            {{-- TAB PENELITIAN --}}
            <div id="tab-penelitian" class="tab-content hidden">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($vacanciesPenelitian as $job)
                        <div data-search-card data-title="{{ strtolower($job->title) }}"
                            data-division="{{ strtolower($job->division_name) }}" data-type="penelitian">
                            <x-job-card :job="$job" />
                        </div>
                    @empty
                        <div data-empty-state class="col-span-3 text-center py-24">
                            <p class="text-gray-500 font-semibold">Tidak ada lowongan penelitian</p>
                        </div>
                    @endforelse
                    <div data-empty-state class="col-span-3 text-center py-24 hidden">
                        <p class="text-gray-500 font-semibold">Tidak ada hasil untuk pencarian ini</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== TIMELINE & CALENDAR SECTION ===================== --}}
    <section class="bg-linear-to-br from-slate-50 via-blue-50/30 to-slate-100 py-20">
        <div id="timeline-wrapper"
            class="max-w-7xl mx-auto grid md:grid-cols-2 gap-8 items-start px-4 md:px-6 transition-all duration-500">

            <div id="text-section" class="flex flex-col gap-6">

                {{-- HEADER TEXT --}}
                <div>
                    <span class="text-blue-600 font-semibold text-xs uppercase tracking-widest">Jadwal Kegiatan</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mt-2 mb-3">Timeline Magang</h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">
                        Peserta dapat melihat periode kegiatan magang, tanggal mulai hingga selesai pada kalender di
                        samping.
                    </p>

                    <div class="inline-flex bg-white p-1 rounded-xl shadow-sm border border-gray-200">
                        <button onclick="tlChangeView('compact')" id="btn-compact"
                            class="px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white transition-all shadow-sm shadow-blue-600/30 flex items-center gap-1.5">
                            <i class="bi bi-layout-text-sidebar"></i> Simpel
                        </button>
                        <button onclick="tlChangeView('detailed')" id="btn-detailed"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-all flex items-center gap-1.5">
                            <i class="bi bi-zoom-in"></i> Zoom In
                        </button>
                    </div>
                </div>

                @php
                    // 1. Ambil Data Magang Peserta
                    $userId = Auth::guard('magang')->id();
                    $acceptedMember = \App\Models\ApplicationMemberMagang::where('user_id', $userId)
                        ->whereHas('application', fn($q) => $q->where('status', 'accepted'))
                        ->with('application.vacancy')
                        ->first();
                    $activeApplication = $acceptedMember?->application;

                    // 2. Ambil Data Pengumuman/Broadcast Global (Maksimal 3 terdekat)
                    $upcomingEvents = \App\Models\Event::where(function ($query) {
                        $query->whereDate('end_date', '>=', \Carbon\Carbon::today())->orWhere(function ($q) {
                            $q->whereNull('end_date')->whereDate('start_date', '>=', \Carbon\Carbon::today());
                        });
                    })
                        ->orderBy('start_date', 'asc')
                        ->take(3)
                        ->get();
                @endphp

                {{-- ======================================================== --}}
                {{-- KANVAS PUTIH UTAMA (Menyatukan Status & Pengumuman)      --}}
                {{-- ======================================================== --}}
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col gap-8">

                    {{-- BAGIAN ATAS: STATUS / TIMELINE --}}
                    @if ($activeApplication)
                        @php
                            $vacancy = $activeApplication->vacancy;
                            $now = \Carbon\Carbon::now();
                            $startDate = \Carbon\Carbon::parse($vacancy->start_date);
                            $endDate = \Carbon\Carbon::parse($vacancy->end_date);
                            $isStarted = $now->gte($startDate);
                            $isFinished = $now->gt($endDate);
                        @endphp

                        <div>
                            {{-- Banner Info Divisi --}}
                            <div
                                class="flex items-center gap-3 bg-linear-to-r from-blue-50 to-emerald-50 border border-blue-100 rounded-2xl px-4 py-3.5 mb-6">
                                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shrink-0">
                                    <i class="bi bi-layers-fill text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 truncate">Status Perjalanan Anda</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $vacancy->division_name }}</p>
                                </div>
                                <span class="text-xs font-bold text-blue-600 bg-blue-100 px-3 py-1 rounded-full shrink-0">
                                    {{ $isFinished ? '4/4' : ($isStarted ? '3/4' : '2/4') }} Selesai
                                </span>
                            </div>

                            {{-- Steps Timeline --}}
                            <div class="flex flex-col tl-steps-wrap">
                                {{-- Step 1 --}}
                                <div class="flex items-stretch gap-0">
                                    <div class="tl-gutter">
                                        <div class="tl-num tl-done"><i class="bi bi-check text-white"
                                                style="font-size:11px;font-weight:900;"></i></div>
                                        <div class="tl-line tl-line-done"></div>
                                    </div>
                                    <div class="tl-card tl-card-done">
                                        <div class="tl-card-header">
                                            <span class="tl-card-title">Diterima</span>
                                            <span class="tl-badge tl-badge-done">&#10003; Selesai</span>
                                        </div>
                                        <p class="tl-card-desc">Selamat! Anda telah lolos seleksi di <strong
                                                class="text-gray-700">{{ $vacancy->division_name }}</strong>.</p>
                                    </div>
                                </div>

                                {{-- Step 2 --}}
                                <div class="flex items-stretch gap-0">
                                    <div class="tl-gutter">
                                        <div class="tl-num {{ $isStarted ? 'tl-done' : 'tl-active' }}">
                                            @if ($isStarted)
                                                <i class="bi bi-check text-white"
                                                    style="font-size:11px;font-weight:900;"></i>
                                            @else
                                                2
                                            @endif
                                        </div>
                                        <div class="tl-line {{ $isStarted ? 'tl-line-active' : 'tl-line-empty' }}"></div>
                                    </div>
                                    <div class="tl-card {{ $isStarted ? 'tl-card-done' : 'tl-card-active' }}">
                                        <div class="tl-card-header">
                                            <span class="tl-card-title">Persiapan Magang</span>
                                            @if ($isStarted)
                                                <span class="tl-badge tl-badge-done">&#10003; Selesai</span>
                                            @else
                                                <span class="tl-badge tl-badge-active">&#9679; Sekarang</span>
                                            @endif
                                        </div>
                                        <p class="tl-card-desc">
                                            @if ($isStarted)
                                                Masa persiapan selesai.
                                            @else
                                                Mulai pada <strong
                                                    class="text-gray-700">{{ $startDate->translatedFormat('d F Y') }}</strong>.
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Step 3 --}}
                                <div class="flex items-stretch gap-0">
                                    <div class="tl-gutter">
                                        <div
                                            class="tl-num {{ $isFinished ? 'tl-done' : ($isStarted ? 'tl-active' : 'tl-pending') }}">
                                            @if ($isFinished)
                                                <i class="bi bi-check text-white"
                                                    style="font-size:11px;font-weight:900;"></i>
                                            @else
                                                3
                                            @endif
                                        </div>
                                        <div class="tl-line tl-line-empty"></div>
                                    </div>
                                    <div
                                        class="tl-card {{ $isFinished ? 'tl-card-done' : ($isStarted ? 'tl-card-active' : 'tl-card-pending') }}">
                                        <div class="tl-card-header">
                                            <span
                                                class="tl-card-title {{ !$isStarted && !$isFinished ? 'tl-muted' : '' }}">Pelaksanaan
                                                Magang</span>
                                            @if ($isFinished)
                                                <span class="tl-badge tl-badge-done">&#10003; Selesai</span>
                                            @elseif($isStarted)
                                                <span class="tl-badge tl-badge-active">&#9679; Berlangsung</span>
                                            @endif
                                        </div>
                                        <p class="tl-card-desc {{ !$isStarted && !$isFinished ? 'tl-muted-text' : '' }}">
                                            @if ($isFinished)
                                                Pelaksanaan magang selesai.
                                            @elseif($isStarted)
                                                Berlangsung hingga <strong
                                                    class="tl-highlight">{{ $endDate->translatedFormat('d F Y') }}</strong>.
                                            @else
                                                Menunggu waktu pelaksanaan.
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Step 4 --}}
                                <div class="flex items-stretch gap-0">
                                    <div class="tl-gutter">
                                        <div class="tl-num {{ $isFinished ? 'tl-active' : 'tl-pending' }}">
                                            @if ($isFinished)
                                                <i class="bi bi-star-fill text-white" style="font-size:9px;"></i>
                                            @else
                                                4
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tl-card {{ $isFinished ? 'tl-card-active' : 'tl-card-pending' }}">
                                        <div class="tl-card-header">
                                            <span class="tl-card-title {{ !$isFinished ? 'tl-muted' : '' }}">Selesai &amp;
                                                Penilaian</span>
                                            @if ($isFinished)
                                                <span class="tl-badge tl-badge-active">&#9679; Sekarang</span>
                                            @else
                                                <span class="tl-badge tl-badge-pending">Menunggu</span>
                                            @endif
                                        </div>
                                        <p class="tl-card-desc tl-muted-text">
                                            @if ($isFinished)
                                                Cek menu <strong class="text-blue-600">Penilaian</strong> untuk melihat
                                                hasil Anda.
                                            @else
                                                Terbuka setelah masa magang berakhir.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- TAMPILAN JIKA BELUM ADA KEGIATAN --}}
                        <div class="text-center py-4">
                            <div
                                class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                                <i class="bi bi-hourglass-split text-blue-400 text-xl"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-700 mb-2">Belum Ada Kegiatan Aktif</h3>
                            <p class="text-xs text-gray-400 leading-relaxed max-w-xs mx-auto mb-5">
                                Anda belum memiliki jadwal magang yang sedang berjalan. Silakan tunggu konfirmasi.
                            </p>
                            <a href="#lowongan"
                                class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl text-xs font-semibold hover:bg-blue-700 transition shadow-sm">
                                <i class="bi bi-search"></i> Lihat Lowongan
                            </a>
                        </div>
                    @endif


                    {{-- BAGIAN BAWAH: PENGUMUMAN GLOBAL (Tetap di dalam Kanvas Putih) --}}
                    @if ($upcomingEvents->count() > 0)
                        <div class="pt-6 border-t border-gray-100/80">
                            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="bi bi-megaphone-fill text-amber-500"></i> Pengumuman & Agenda Terdekat
                            </h3>

                            <div class="flex flex-col gap-3">
                                @foreach ($upcomingEvents as $event)
                                    @php
                                        $start = \Carbon\Carbon::parse($event->start_date);
                                        $end = $event->end_date
                                            ? \Carbon\Carbon::parse($event->end_date)
                                            : clone $start;

                                        $dateDisplay = $start->translatedFormat('d M Y');
                                        if ($start->notEqualTo($end)) {
                                            $dateDisplay .= ' - ' . $end->translatedFormat('d M Y');
                                        }

                                        $color = $event->color ?? '#3b82f6';
                                    @endphp

                                    <div class="bg-slate-50 flex gap-3 items-start p-3.5 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/40 hover:shadow-sm transition-all group cursor-pointer"
                                        onclick="typeof Swal !== 'undefined' ? Swal.fire({ title: '{{ addslashes($event->title) }}', html: '<div style=\'text-align:left; font-size:14px; margin-top:10px;\'><b>Tanggal:</b> {{ $dateDisplay }}<br><br><b>Keterangan:</b><br>{{ nl2br(addslashes($event->description ?? 'Tidak ada keterangan tambahan.')) }}</div>', icon: 'info', confirmButtonColor: '#3b82f6', confirmButtonText: 'Tutup' }) : alert('📌 {{ addslashes($event->title) }}\n📅 {{ $dateDisplay }}\n\n📝 Keterangan:\n{{ addslashes($event->description ?? 'Tidak ada keterangan.') }}')">

                                        <div class="w-3 h-3 mt-1 rounded-full shrink-0 shadow-sm"
                                            style="background-color: {{ $color }};"></div>

                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition truncate">
                                                {{ $event->title }}</h4>
                                            <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500 font-medium">
                                                <i class="bi bi-calendar2-event"></i> <span>{{ $dateDisplay }}</span>
                                            </div>
                                            @if ($event->description)
                                                <p class="text-[11px] text-slate-400 mt-1.5 truncate">
                                                    {{ $event->description }}</p>
                                            @endif
                                        </div>

                                        <div
                                            class="shrink-0 text-slate-300 group-hover:text-blue-500 transition-colors mt-1 self-center">
                                            <i class="bi bi-chevron-right text-sm"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- CALENDAR KANAN --}}
            <div id="calendar-container"
                class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 transition-all duration-500 sticky top-20">
                @include('partials.calendar')
            </div>

        </div>
    </section>


    </div>
    </section>

    {{-- ===================== GALLERY SECTION — MODERN CLEAN REDESIGN ===================== --}}
    <x-gallery-landing />


    {{-- ===================== SCRIPTS ===================== --}}
    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabBtns = document.querySelectorAll('.tab-btn');
                const tabContents = document.querySelectorAll('.tab-content');
                tabBtns.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        tabBtns.forEach(b => {
                            b.classList.remove('active-tab');
                            b.classList.add('inactive-tab');
                        });
                        btn.classList.add('active-tab');
                        btn.classList.remove('inactive-tab');
                        tabContents.forEach(c => c.classList.add('hidden'));
                        document.getElementById('tab-' + btn.getAttribute('data-tab')).classList.remove(
                            'hidden');
                    });
                });
            });

            function tlChangeView(mode) {
                const wrapper = document.getElementById('timeline-wrapper');
                const btnC = document.getElementById('btn-compact');
                const btnD = document.getElementById('btn-detailed');
                if (mode === 'detailed') {
                    wrapper.classList.remove('md:grid-cols-2');
                    wrapper.classList.add('grid-cols-1');
                    btnD.classList.add('bg-blue-600', 'text-white', 'shadow-sm', 'shadow-blue-600/30');
                    btnD.classList.remove('text-gray-500');
                    btnC.classList.remove('bg-blue-600', 'text-white', 'shadow-sm', 'shadow-blue-600/30');
                    btnC.classList.add('text-gray-500');
                } else {
                    wrapper.classList.add('md:grid-cols-2');
                    wrapper.classList.remove('grid-cols-1');
                    btnC.classList.add('bg-blue-600', 'text-white', 'shadow-sm', 'shadow-blue-600/30');
                    btnC.classList.remove('text-gray-500');
                    btnD.classList.remove('bg-blue-600', 'text-white', 'shadow-sm', 'shadow-blue-600/30');
                    btnD.classList.add('text-gray-500');
                }
            }
        </script>
    @endpush

    @push('style')
        <style>
            .active-tab {
                background-color: #2563eb;
                color: #fff;
                box-shadow: 0 4px 14px rgba(37, 99, 235, .35);
            }

            .inactive-tab {
                background-color: #f1f5f9;
                color: #64748b;
            }

            .inactive-tab:hover {
                background-color: #e2e8f0;
                color: #374151;
            }

            .tl-gutter {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 38px;
                flex-shrink: 0;
                margin-right: 14px;
            }

            .tl-num {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 11.5px;
                font-weight: 800;
                flex-shrink: 0;
                z-index: 1;
            }

            .tl-done {
                background: #10b981;
                color: #fff;
                box-shadow: 0 0 0 4px #d1fae5;
            }

            .tl-active {
                background: #2563eb;
                color: #fff;
                box-shadow: 0 0 0 4px #dbeafe;
            }

            .tl-pending {
                background: #f1f5f9;
                color: #94a3b8;
                border: 1.5px dashed #cbd5e1;
            }

            .tl-line {
                width: 2px;
                flex: 1;
                min-height: 12px;
                margin: 4px 0;
                border-radius: 2px;
            }

            .tl-line-done {
                background: #10b981;
            }

            .tl-line-active {
                background: repeating-linear-gradient(to bottom, #2563eb 0px, #2563eb 5px, transparent 5px, transparent 10px);
            }

            .tl-line-empty {
                background: #e2e8f0;
            }

            .tl-card {
                flex: 1;
                padding: 11px 15px;
                border-radius: 12px;
                margin-bottom: 10px;
                border: 1px solid transparent;
            }

            .tl-card-done {
                background: #f0fdf4;
                border-color: #bbf7d0;
            }

            .tl-card-active {
                background: #eff6ff;
                border-color: #bfdbfe;
            }

            .tl-card-pending {
                background: #fafafa;
                border-color: #f1f5f9;
            }

            .tl-card-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 4px;
            }

            .tl-card-title {
                font-size: 13px;
                font-weight: 800;
                color: #0f172a;
            }

            .tl-muted {
                color: #94a3b8 !important;
            }

            .tl-muted-text {
                color: #cbd5e1 !important;
            }

            .tl-highlight {
                color: #2563eb;
            }

            .tl-badge {
                font-size: 9.5px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 20px;
                margin-left: auto;
                white-space: nowrap;
            }

            .tl-badge-done {
                background: #dcfce7;
                color: #16a34a;
            }

            .tl-badge-active {
                background: #dbeafe;
                color: #2563eb;
            }

            .tl-badge-pending {
                background: #f1f5f9;
                color: #94a3b8;
            }

            .tl-card-desc {
                font-size: 11.5px;
                color: #64748b;
                line-height: 1.5;
            }
        </style>
    @endpush

@endsection
