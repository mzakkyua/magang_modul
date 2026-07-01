@extends('layouts.landing')

@section('title', 'Homepage - SINAKERTRANS')

@section('content')


    {{-- ===================== HERO SECTION (COMPACT VERSION) ===================== --}}
    @php
        $heroOpenCount = $vacanciesMagang->count() + $vacanciesPenelitian->count();
        $heroMagangCount = $vacanciesMagang->count();
        $heroPenelitianCount = $vacanciesPenelitian->count();
        $heroActiveDivisions = $divisionStats->where('has_open', true)->count();
        $heroTotalAvailable = $divisionStats->sum('total_available');
        $heroIsOpen = $heroOpenCount > 0;
    @endphp

    <section class="relative w-full min-h-130 lg:min-h-145 flex flex-col justify-between overflow-hidden" id="home">

        {{-- ============================================================ --}}
        {{-- BACKGROUND                                                     --}}
        {{-- ============================================================ --}}
        <div class="absolute inset-0 z-0">
            {{-- Gambar Background dari Asset Lokal --}}
            <img src="{{ asset('assets/images/hero-bg.png') }}?v={{ file_exists(public_path('assets/images/hero-bg.png')) ? filemtime(public_path('assets/images/hero-bg.png')) : '1' }}"
                alt="Kantor Disnakertrans" class="w-full h-full object-cover object-center" loading="eager" />
            {{-- Gradient biru — konsisten dengan palet existing --}}
            <div class="absolute inset-0 bg-linear-to-r from-blue-900/95 via-blue-800/88 to-blue-700/65"></div>
        </div>

        {{-- ============================================================ --}}
        {{-- KONTEN UTAMA — 2 kolom di desktop                             --}}
        {{-- ============================================================ --}}
        <div class="relative z-10 flex-1 w-full max-w-6xl mx-auto px-6">
            <div
                class="grid lg:grid-cols-[1fr_300px] xl:grid-cols-[1fr_320px] gap-8 xl:gap-12 items-center py-20 md:py-24 lg:py-28">

                {{-- ─────────────────────────── --}}
                {{-- KIRI: Teks                  --}}
                {{-- ─────────────────────────── --}}
                <div>

                    {{-- Badge --}}
                    <span
                        class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-blue-100 text-[10px] md:text-xs font-semibold px-4 py-2 rounded-full mb-5 backdrop-blur-sm">
                        <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
                        Disnakertrans Prov. Jawa Timur
                    </span>

                    {{-- Heading --}}
                    <h1 class="text-white font-extrabold text-4xl md:text-5xl leading-tight mb-4 drop-shadow-md">
                        Temukan Tempat <br>
                        <span class="text-blue-300">Magang Impianmu</span>
                    </h1>

                    {{-- Deskripsi --}}
                    <p class="text-blue-100/80 text-sm md:text-base leading-relaxed max-w-md mb-8">
                        Dapatkan pengalaman kerja nyata, tingkatkan kompetensi, dan bangun
                        karier profesionalmu bersama Dinas Tenaga Kerja Jawa Timur sejak dini.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-wrap gap-3">
                        <a href="#lowongan"
                            class="inline-flex items-center gap-2 bg-white text-blue-700 px-6 py-2.5 rounded-full font-extrabold text-sm hover:bg-blue-50 active:scale-95 transition-all duration-200 shadow-lg shadow-black/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari Lowongan
                        </a>
                        <a href="#about"
                            class="inline-flex items-center gap-2 border border-white/40 text-white px-6 py-2.5 rounded-full font-bold text-sm hover:bg-white/10 hover:border-white/60 transition-all duration-200 backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Kenali Kami
                        </a>
                    </div>

                </div>

                {{-- ─────────────────────────── --}}
                {{-- KANAN: Live Card (desktop)  --}}
                {{-- ─────────────────────────── --}}
                <div class="hidden lg:block">
                    <div
                        class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-5 shadow-xl shadow-black/20">

                        {{-- Header card --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span
                                    class="w-2 h-2 rounded-full animate-pulse
                                {{ $heroIsOpen ? 'bg-emerald-400' : 'bg-white/40' }}">
                                </span>
                                <span class="text-white/80 text-[10px] font-bold uppercase tracking-widest">
                                    Status Lowongan
                                </span>
                            </div>
                            <span class="text-white/30 text-[9px]">Live · 10 mnt</span>
                        </div>

                        {{-- Status banner --}}
                        @if ($heroIsOpen)
                            <div
                                class="flex items-center gap-2 bg-emerald-400/15 border border-emerald-400/25 rounded-xl px-3 py-2.5 mb-4">
                                <svg class="w-3.5 h-3.5 text-emerald-300 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-emerald-200 text-xs font-semibold">Sedang Menerima Pendaftar</p>
                            </div>
                        @else
                            <div
                                class="flex items-center gap-2 bg-white/10 border border-white/15 rounded-xl px-3 py-2.5 mb-4">
                                <svg class="w-3.5 h-3.5 text-blue-200 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-blue-100 text-xs font-semibold">Belum Ada Lowongan Aktif</p>
                            </div>
                        @endif

                        {{-- Data stats --}}
                        <div class="space-y-2 mb-4">

                            <div
                                class="flex items-center justify-between bg-white/10 hover:bg-white/15 transition-colors rounded-xl px-4 py-2.5">
                                <span class="text-blue-100 text-xs">Lowongan Magang</span>
                                <span class="text-white font-extrabold text-lg leading-none tabular-nums">
                                    {{ $heroMagangCount }}
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between bg-white/10 hover:bg-white/15 transition-colors rounded-xl px-4 py-2.5">
                                <span class="text-blue-100 text-xs">Lowongan Penelitian</span>
                                <span class="text-white font-extrabold text-lg leading-none tabular-nums">
                                    {{ $heroPenelitianCount }}
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between bg-white/10 hover:bg-white/15 transition-colors rounded-xl px-4 py-2.5">
                                <span class="text-blue-100 text-xs">Bidang Menerima</span>
                                <span class="text-blue-200 font-extrabold text-lg leading-none tabular-nums">
                                    {{ $heroActiveDivisions }}
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between bg-white/10 hover:bg-white/15 transition-colors rounded-xl px-4 py-2.5">
                                <span class="text-blue-100 text-xs">Tempat Tersedia</span>
                                <span class="text-emerald-300 font-extrabold text-lg leading-none tabular-nums">
                                    {{ $heroTotalAvailable }}
                                </span>
                            </div>

                        </div>

                        {{-- CTA --}}
                        <a href="#lowongan"
                            class="flex items-center justify-center gap-2 w-full bg-white text-blue-700 text-xs font-extrabold py-2.5 rounded-xl hover:bg-blue-50 active:scale-[0.98] transition-all duration-200 shadow-md">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Lihat Semua Lowongan
                        </a>

                    </div>
                </div>
                {{-- /KANAN --}}

            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STATS BAR — Dinamis mengambil data dari Database             --}}
        {{-- ============================================================ --}}
        <div class="relative z-10 w-full bg-white/10 backdrop-blur-md border-t border-white/20">
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

    {{-- ===================== DIVISION SECTION ===================== --}}
    <x-division-info-landing :divisionStats="$divisionStats" />


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

    {{-- ===================== DIVISION CAPACITY SECTION ===================== --}}
    <x-division-capacity-landing :divisionStats="$divisionStats" />

    {{-- ===================== ABOUT SECTION — MODERN CLEAN & COMPACT ===================== --}}
    <section class="bg-white py-12 md:py-16 border-t border-gray-100" id="about">
        <div class="max-w-6xl mx-auto px-6">

            {{-- Label kecil di atas --}}
            <div class="flex items-center gap-3 mb-10 md:mb-12">
                <div class="h-px flex-1 bg-gray-100"></div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest px-3">Tentang Kami</span>
                <div class="h-px flex-1 bg-gray-100"></div>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-start">

                {{-- ── KIRI: NARASI + ANGKA BESAR ── --}}
                <div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight mb-5">
                        Dinas Tenaga Kerja <br>
                        <span class="text-blue-600">Prov. Jawa Timur</span>
                    </h2>

                    <p class="text-gray-500 leading-relaxed text-sm md:text-base mb-10 max-w-md">
                        Berfokus pada peningkatan kompetensi tenaga kerja, pengembangan lapangan kerja,
                        perlindungan hak tenaga kerja, dan pengelolaan transmigrasi demi kesejahteraan
                        masyarakat Jawa Timur.
                    </p>

                    {{-- Angka besar dengan border tipis --}}
                    <div class="space-y-0 divide-y divide-gray-100 border-y border-gray-100">

                        <div class="flex items-center justify-between py-4 group">
                            <div>
                                <p
                                    class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-0.5 group-hover:text-blue-500 transition-colors">
                                    Alumni Magang</p>
                                <p class="text-xs text-gray-500">Peserta yang menyelesaikan program</p>
                            </div>
                            <p class="text-3xl font-extrabold text-blue-600 tabular-nums">500+</p>
                        </div>

                        <div class="flex items-center justify-between py-4 group">
                            <div>
                                <p
                                    class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-0.5 group-hover:text-blue-500 transition-colors">
                                    Divisi Aktif</p>
                                <p class="text-xs text-gray-500">Pilihan bidang sesuai peminatan</p>
                            </div>
                            <p class="text-3xl font-extrabold text-gray-900 tabular-nums">15+</p>
                        </div>

                        <div class="flex items-center justify-between py-4 group">
                            <div>
                                <p
                                    class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-0.5 group-hover:text-blue-500 transition-colors">
                                    Peserta Aktif</p>
                                <p class="text-xs text-gray-500">Sedang menjalani program magang</p>
                            </div>
                            <p class="text-3xl font-extrabold text-gray-900 tabular-nums">100+</p>
                        </div>

                        <div class="flex items-center justify-between py-4 group">
                            <div>
                                <p
                                    class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-0.5 group-hover:text-blue-500 transition-colors">
                                    Rata-rata Durasi</p>
                                <p class="text-xs text-gray-500">Program terstruktur & terarah</p>
                            </div>
                            <p class="text-3xl font-extrabold text-gray-900 tabular-nums">3 Bln</p>
                        </div>

                    </div>
                </div>


                {{-- ── KANAN: FOTO + 4 PILAR (DIBUNGKUS SATU DIV AGAR TIDAK BOCOR KE KIRI) ── --}}
                <div class="flex flex-col gap-8">

                    {{-- Foto (Otomatis menyesuaikan ukuran, tidak kepotong, tidak raksasa) --}}
                    <div
                        class="relative overflow-hidden rounded-2xl bg-gray-50 border border-gray-100 shadow-sm flex items-center justify-center w-full max-h-96">
                        <img src="{{ asset('assets/images/kantor-disnaker.jpg') }}?v={{ file_exists(public_path('assets/images/kantor-disnaker.jpg')) ? filemtime(public_path('assets/images/kantor-disnaker.jpg')) : '1' }}"
                            class="w-auto h-auto max-w-full max-h-96 object-contain transition-transform duration-700 hover:scale-105"
                            alt="Dinas Tenaga Kerja Jawa Timur" loading="lazy" />

                        {{-- Caption --}}
                        <div
                            class="absolute bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-white/50 px-4 py-2.5 flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-gray-800">Disnakertrans Jatim</p>
                                <p class="text-[9px] text-gray-500">Pemerintah Provinsi Jawa Timur</p>
                            </div>
                            <span
                                class="text-[9px] font-extrabold text-blue-700 bg-blue-100 border border-blue-200 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                Resmi
                            </span>
                        </div>
                    </div>

                </div>

                {{-- 4 Pilar Program (Menggunakan Inline SVG) --}}
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Fokus Utama Program
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        {{-- Item 1: Kompetensi --}}
                        <div
                            class="flex items-start gap-2.5 p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:border-blue-200 hover:bg-blue-50 transition-all duration-200">
                            <div
                                class="w-7 h-7 bg-white border border-gray-200 rounded-md flex items-center justify-center shrink-0 text-blue-600 shadow-sm mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Kompetensi</p>
                                <p class="text-[10px] text-gray-500 mt-0.5 leading-snug">Peningkatan skill tenaga kerja
                                </p>
                            </div>
                        </div>

                        {{-- Item 2: Perlindungan --}}
                        <div
                            class="flex items-start gap-2.5 p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:border-blue-200 hover:bg-blue-50 transition-all duration-200">
                            <div
                                class="w-7 h-7 bg-white border border-gray-200 rounded-md flex items-center justify-center shrink-0 text-blue-600 shadow-sm mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Perlindungan</p>
                                <p class="text-[10px] text-gray-500 mt-0.5 leading-snug">Hak-hak tenaga kerja terjamin
                                </p>
                            </div>
                        </div>

                        {{-- Item 3: Lapangan Kerja --}}
                        <div
                            class="flex items-start gap-2.5 p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:border-blue-200 hover:bg-blue-50 transition-all duration-200">
                            <div
                                class="w-7 h-7 bg-white border border-gray-200 rounded-md flex items-center justify-center shrink-0 text-blue-600 shadow-sm mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Lapangan Kerja</p>
                                <p class="text-[10px] text-gray-500 mt-0.5 leading-snug">Pengembangan peluang kerja</p>
                            </div>
                        </div>

                        {{-- Item 4: Transmigrasi --}}
                        <div
                            class="flex items-start gap-2.5 p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:border-blue-200 hover:bg-blue-50 transition-all duration-200">
                            <div
                                class="w-7 h-7 bg-white border border-gray-200 rounded-md flex items-center justify-center shrink-0 text-blue-600 shadow-sm mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Transmigrasi</p>
                                <p class="text-[10px] text-gray-500 mt-0.5 leading-snug">Pengelolaan secara terencana
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        </div>
    </section>



    {{-- ===================== GALLERY SECTION — MODERN CLEAN REDESIGN ===================== --}}
    <x-gallery-landing />

    {{-- Menampilkan tombol panduan khusus untuk pendaftar baru di halaman depan --}}
    <x-floating-guide title="Panduan Peserta" :path="asset('assets/docs/guidebook-user.pdf')" />

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
        </script>
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
        </style>
    @endpush

@endsection
