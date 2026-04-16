@extends('layouts.landing')
@section('title', 'Dashboard - SINAKERTRANS')

@section('content')

    {{-- ===================== HERO SECTION ===================== --}}
    {{-- BUGFIX: konten teks sekarang DI DALAM wrapper relative --}}
    <section class="relative w-full h-120 md:h-135 overflow-hidden" id="home">

        {{-- Background Image --}}
        <div class="absolute inset-0">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b2/New_office.jpg" alt="Background Kantor"
                class="object-cover object-center w-full h-full" />

            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-linear-to-r from-blue-900/90 via-blue-800/75 to-blue-700/30"></div>
        </div>

        {{-- Hero Content — sekarang DI DALAM wrapper sehingga posisi benar --}}
        <div class="relative z-10 h-full flex items-center">
            <div class="max-w-7xl mx-auto px-6 w-full">
                <div class="max-w-xl">

                    {{-- Greeting badge --}}
                    @if (Auth::guard('magang')->check())
                        <span
                            class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-blue-100 text-xs font-semibold px-4 py-2 rounded-full mb-5 backdrop-blur-sm">
                            <i class="bi bi-person-check-fill text-blue-300"></i>
                            Selamat datang, {{ Auth::guard('magang')->user()->nama_peserta }}!
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-blue-200 text-xs font-semibold px-4 py-2 rounded-full mb-5 backdrop-blur-sm">
                            <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                            Dinas Tenaga Kerja & Transmigrasi Prov. Jawa Timur
                        </span>
                    @endif

                    {{-- Heading --}}
                    <h1 class="text-white font-extrabold text-4xl md:text-5xl leading-tight mb-4 drop-shadow-lg">
                        Temukan Tempat <br>
                        <span class="text-blue-300">Magang Impianmu</span>
                    </h1>

                    {{-- Subheading --}}
                    <p class="text-blue-100 text-base md:text-lg font-medium max-w-md leading-relaxed mb-8">
                        Bergabunglah bersama Dinas Tenaga Kerja Jawa Timur dan dapatkan pengalaman magang yang nyata dan
                        bermakna.
                    </p>

                    {{-- CTA Buttons --}}
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

        {{-- Stats Bar --}}
        <div class="absolute bottom-0 left-0 right-0 bg-white/10 backdrop-blur-md border-t border-white/20">
            <div class="max-w-7xl mx-auto px-6 py-4 grid grid-cols-3 divide-x divide-white/20">
                <div class="text-center px-4">
                    <p class="text-white font-extrabold text-xl md:text-2xl">100+</p>
                    <p class="text-blue-200 text-xs mt-0.5">Peserta Aktif</p>
                </div>
                <div class="text-center px-4">
                    <p class="text-white font-extrabold text-xl md:text-2xl">15+</p>
                    <p class="text-blue-200 text-xs mt-0.5">Divisi Tersedia</p>
                </div>
                <div class="text-center px-4">
                    <p class="text-white font-extrabold text-xl md:text-2xl">500+</p>
                    <p class="text-blue-200 text-xs mt-0.5">Alumni Magang</p>
                </div>
            </div>
        </div>

    </section>

    {{-- ===================== SERVICE SECTION ===================== --}}
    <section>
        @include('program.section')
        @include('program.modal')
    </section>

    {{-- ===================== SEARCH & LOWONGAN SECTION ===================== --}}
    <section class="px-4 md:px-6 py-16" id="lowongan">
        <div class="max-w-7xl mx-auto">

            {{-- Section Header --}}
            <div class="text-center mb-10">
                <span class="text-blue-600 font-semibold text-xs uppercase tracking-widest">Kesempatan Terbuka</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mt-2">Lowongan Tersedia</h2>
                <p class="text-gray-500 mt-2 max-w-md mx-auto text-sm">
                    Temukan posisi magang atau penelitian yang sesuai dengan bidang dan minatmu.
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="max-w-2xl mx-auto mb-10">
                <form action="{{ route('dashboard.index') }}" method="GET">
                    <div class="flex gap-2 bg-white p-2 rounded-2xl shadow-lg shadow-gray-200/80 border border-gray-100">
                        <div class="flex items-center flex-1 gap-2 px-3">
                            <i class="bi bi-search text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari posisi magang..."
                                class="flex-1 py-2 outline-none text-sm text-gray-700 bg-transparent placeholder-gray-400">
                        </div>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition shadow-md shadow-blue-600/30 flex items-center gap-2">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>

            {{-- Search Result Info --}}
            @if ($search)
                <div class="max-w-7xl mx-auto mb-6 flex items-center gap-2 text-gray-600 text-sm">
                    <i class="bi bi-search text-blue-600"></i>
                    Hasil pencarian untuk:
                    <span class="font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-100">
                        "{{ $search }}"
                    </span>
                    <a href="{{ route('dashboard.index') }}"
                        class="ml-1 text-gray-500 hover:text-red-500 flex items-center gap-1 text-xs transition">
                        <i class="bi bi-x-circle-fill"></i> Reset
                    </a>
                </div>
            @endif

            {{-- Filter Tabs --}}
            <div class="flex gap-2 mb-8">
                <button data-tab="semua" class="tab-btn active-tab px-5 py-2 rounded-full text-sm font-semibold transition">
                    <i class="bi bi-grid mr-1"></i> Semua
                </button>
                <button data-tab="magang"
                    class="tab-btn inactive-tab px-5 py-2 rounded-full text-sm font-semibold transition">
                    <i class="bi bi-briefcase mr-1"></i> Magang
                </button>
                <button data-tab="penelitian"
                    class="tab-btn inactive-tab px-5 py-2 rounded-full text-sm font-semibold transition">
                    <i class="bi bi-journal-text mr-1"></i> Penelitian
                </button>
            </div>

            {{-- TAB SEMUA --}}
            <div id="tab-semua" class="tab-content">
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
                        <div class="col-span-3 text-center py-24">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-inbox text-4xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-semibold text-lg">Tidak ada lowongan ditemukan</p>
                            <p class="text-gray-400 text-sm mt-1">Coba ubah kata kunci pencarian kamu</p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- TAB MAGANG --}}
            <div id="tab-magang" class="tab-content hidden">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @forelse ($vacanciesMagang as $job)
                        <x-job-card :job="$job" />
                    @empty
                        <div class="col-span-3 text-center py-24">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-briefcase text-4xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-semibold text-lg">Tidak ada lowongan magang</p>
                            <p class="text-gray-400 text-sm mt-1">Belum ada lowongan magang yang tersedia saat ini</p>
                        </div>
                    @endforelse

                </div>
            </div>

            {{-- TAB PENELITIAN --}}
            <div id="tab-penelitian" class="tab-content hidden">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @forelse ($vacanciesPenelitian as $job)
                        <x-job-card :job="$job" />
                    @empty
                        <div class="col-span-3 text-center py-24">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-journal-text text-4xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-semibold text-lg">Tidak ada lowongan penelitian</p>
                            <p class="text-gray-400 text-sm mt-1">Belum ada lowongan penelitian yang tersedia saat ini</p>
                        </div>
                    @endforelse

                </div>
            </div>

        </div>
    </section>


    {{-- ===================== TIMELINE & CALENDAR SECTION ===================== --}}
    <section class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-slate-100 py-20">
        <div id="timeline-wrapper"
            class="max-w-7xl mx-auto grid md:grid-cols-2 gap-8 items-start px-4 md:px-6 transition-all duration-500">

            {{-- ============================================================
             KOLOM KIRI: TIMELINE STEPS
             ============================================================ --}}
            <div id="text-section">

                {{-- Header --}}
                <div class="mb-6">
                    <span class="text-blue-600 font-semibold text-xs uppercase tracking-widest">Jadwal Kegiatan</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mt-2 mb-3">Timeline Magang</h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">
                        Peserta dapat melihat periode kegiatan magang, tanggal mulai hingga selesai pada kalender di
                        samping.
                    </p>

                    {{-- View Toggle --}}
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

                {{-- ── PHP: Cari lamaran accepted ── --}}
                @php
                    $userId = Auth::guard('magang')->id();
                    $acceptedMember = \App\Models\ApplicationMemberMagang::where('user_id', $userId)
                        ->whereHas('application', fn($q) => $q->where('status', 'accepted'))
                        ->with('application.vacancy')
                        ->first();

                    $activeApplication = $acceptedMember?->application;
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

                    {{-- Journey header card --}}
                    <div
                        class="flex items-center gap-3 bg-gradient-to-r from-blue-50 to-emerald-50 border border-blue-100 rounded-2xl px-4 py-3.5 mb-5">
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

                    {{-- Steps --}}
                    <div class="flex flex-col tl-steps-wrap">

                        {{-- STEP 1: Diterima ── selalu done ── --}}
                        <div class="flex items-stretch gap-0">
                            <div class="tl-gutter">
                                <div class="tl-num tl-done">
                                    <i class="bi bi-check text-white" style="font-size:11px;font-weight:900;"></i>
                                </div>
                                <div class="tl-line tl-line-done"></div>
                            </div>
                            <div class="tl-card tl-card-done">
                                <div class="tl-card-header">
                                    <span class="tl-card-title">Diterima</span>
                                    <span class="tl-badge tl-badge-done">&#10003; Selesai</span>
                                </div>
                                <p class="tl-card-desc">
                                    Selamat! Anda telah lolos seleksi di <strong
                                        class="text-gray-700">{{ $vacancy->division_name }}</strong>.
                                </p>
                            </div>
                        </div>

                        {{-- STEP 2: Persiapan ── done jika sudah started ── --}}
                        <div class="flex items-stretch gap-0">
                            <div class="tl-gutter">
                                <div class="tl-num {{ $isStarted ? 'tl-done' : 'tl-active' }}">
                                    @if ($isStarted)
                                        <i class="bi bi-check text-white" style="font-size:11px;font-weight:900;"></i>
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
                                        Masa persiapan telah selesai.
                                    @else
                                        Mulai pada <strong
                                            class="text-gray-700">{{ $startDate->translatedFormat('d F Y') }}</strong>.
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- STEP 3: Pelaksanaan ── --}}
                        <div class="flex items-stretch gap-0">
                            <div class="tl-gutter">
                                <div
                                    class="tl-num {{ $isFinished ? 'tl-done' : ($isStarted ? 'tl-active' : 'tl-pending') }}">
                                    @if ($isFinished)
                                        <i class="bi bi-check text-white" style="font-size:11px;font-weight:900;"></i>
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
                                        Pelaksanaan magang telah selesai.
                                    @elseif($isStarted)
                                        Sedang berlangsung hingga <strong
                                            class="tl-highlight">{{ $endDate->translatedFormat('d F Y') }}</strong>.
                                    @else
                                        Menunggu waktu pelaksanaan dimulai.
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- STEP 4: Selesai & Penilaian ── --}}
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
                                        Cek menu <strong class="text-blue-600">Penilaian</strong> untuk melihat hasil Anda.
                                    @else
                                        Terbuka setelah masa magang berakhir.
                                    @endif
                                </p>
                            </div>
                        </div>

                    </div>{{-- /tl-steps-wrap --}}
                @else
                    {{-- Empty state ── belum ada lamaran accepted ── --}}
                    <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-8 text-center">
                        <div
                            class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                            <i class="bi bi-hourglass-split text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="text-sm font-bold text-gray-700 mb-2">Belum Ada Kegiatan Aktif</h3>
                        <p class="text-xs text-gray-400 leading-relaxed max-w-xs mx-auto">
                            Anda belum memiliki jadwal magang yang sedang berjalan. Silakan tunggu konfirmasi lamaran Anda.
                        </p>
                        <a href="#lowongan"
                            class="inline-flex items-center gap-2 mt-4 bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-blue-700 transition">
                            <i class="bi bi-search"></i> Lihat Lowongan
                        </a>
                    </div>
                @endif

            </div>{{-- /text-section --}}


            {{-- ============================================================
             KOLOM KANAN: CALENDAR (partial tetap terpisah)
             ============================================================ --}}
            <div id="calendar-container"
                class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 transition-all duration-500 sticky top-20">
                @include('partials.calendar')
            </div>

        </div>
    </section>





    {{-- ===================== GALLERY SECTION ===================== --}}
    <section class="py-20 bg-white" id="gallery">
        <div class="max-w-7xl mx-auto px-6">

            {{-- Section Header --}}
            <div class="text-center mb-12">
                <span class="text-blue-600 font-semibold text-xs uppercase tracking-widest">Galeri Kegiatan</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mt-2">Dokumentasi Magang</h2>
                <p class="text-gray-500 mt-2 text-sm max-w-sm mx-auto">
                    Momen berharga dari kegiatan magang di Dinas Tenaga Kerja Jawa Timur.
                </p>
            </div>

            {{-- Gallery Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <div class="group relative overflow-hidden rounded-2xl shadow-md">
                    <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-1.jpeg"
                        alt="Dokumentasi Magang 1"
                        class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div
                        class="absolute inset-0 bg-linear-to-t from-blue-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <i class="bi bi-zoom-in text-white text-xl"></i>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl shadow-md">
                    <img src="https://www.suarasurabaya.net/wp-content/uploads/2023/01/Kegiatan-Uji-Kompetensi-Kejuruan-HMI-Berbasis-PLC-yang-dilakukan-UPT-Balai-Latihan-Kerja-Surabaya-735x493.jpg.webp"
                        alt="Dokumentasi Magang 2"
                        class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div
                        class="absolute inset-0 bg-linear-to-t from-blue-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <i class="bi bi-zoom-in text-white text-xl"></i>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl shadow-md">
                    <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-2.jpeg"
                        alt="Dokumentasi Magang 3"
                        class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div
                        class="absolute inset-0 bg-linear-to-t from-blue-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <i class="bi bi-zoom-in text-white text-xl"></i>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl shadow-md">
                    <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-3.jpeg"
                        alt="Dokumentasi Magang 4"
                        class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div
                        class="absolute inset-0 bg-linear-to-t from-blue-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <i class="bi bi-zoom-in text-white text-xl"></i>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===================== SCRIPTS ===================== --}}
    @push('script')
        <script>
            // SECTION: Tab Lowongan
            document.addEventListener('DOMContentLoaded', function() {

                const tabBtns = document.querySelectorAll('.tab-btn');
                const tabContents = document.querySelectorAll('.tab-content');

                tabBtns.forEach(function(btn) {
                    btn.addEventListener('click', function() {

                        // STEP 1: Reset semua tombol ke state inactive
                        tabBtns.forEach(function(b) {
                            b.classList.remove('active-tab');
                            b.classList.add('inactive-tab');
                        });

                        // STEP 2: Set tombol yang diklik jadi active
                        btn.classList.add('active-tab');
                        btn.classList.remove('inactive-tab');

                        // STEP 3: Sembunyikan semua konten tab
                        tabContents.forEach(function(content) {
                            content.classList.add('hidden');
                        });

                        // STEP 4: Tampilkan konten tab yang dipilih
                        var target = btn.getAttribute('data-tab');
                        document.getElementById('tab-' + target).classList.remove('hidden');
                    });
                });

            });

            // JS Toggle View 

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

    {{-- ── CSS Timeline Steps ── --}}
    @push('style')
        <style>
            /* SECTION: Tab button state styling */
            .active-tab {
                background-color: #2563eb;
                color: #ffffff;
                box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            }

            .inactive-tab {
                background-color: #f1f5f9;
                color: #64748b;
            }

            .inactive-tab:hover {
                background-color: #e2e8f0;
                color: #374151;
            }
        </style>

        <style>
            /* Gutter kiri: nomor + konektor */
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

            /* Konektor */
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
                background: repeating-linear-gradient(to bottom,
                        #2563eb 0px, #2563eb 5px,
                        transparent 5px, transparent 10px);
            }

            .tl-line-empty {
                background: #e2e8f0;
            }

            /* Step card */
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

            /* Badge */
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
