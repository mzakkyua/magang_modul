@extends('layouts.landing')

@section('title', 'Homepage - SINAKERTRANS')

@section('content')

    {{-- ===================== HERO SECTION ===================== --}}
    <section class="relative w-full h-130 md:h-150 overflow-hidden" id="home">
        <div class="absolute inset-0">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b2/New_office.jpg" alt="Background Kantor"
                class="object-cover object-center w-full h-full" />
            <div class="absolute inset-0 bg-linear-to-r from-blue-900/90 via-blue-800/75 to-blue-700/30"></div>
        </div>

        <div class="relative z-10 h-full flex items-center">
            <div class="max-w-7xl mx-auto px-6 w-full">
                <div class="max-w-xl">
                    <span
                        class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-blue-200 text-xs font-semibold px-4 py-2 rounded-full mb-5 backdrop-blur-sm">
                        <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                        Dinas Tenaga Kerja & Transmigrasi Prov. Jawa Timur
                    </span>
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
                        {{-- FIX: Ganti "Tentang Program" → "Tentang Kami" --}}
                        <a href="#about"
                            class="border border-white/40 text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-white/10 transition backdrop-blur-sm flex items-center gap-2">
                            <i class="bi bi-people"></i> Tentang Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>

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
            <div class="text-center mb-10">
                <span class="text-blue-600 font-semibold text-xs uppercase tracking-widest">Kesempatan Terbuka</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mt-2">Lowongan Tersedia</h2>
                <p class="text-gray-500 mt-2 max-w-md mx-auto text-sm">
                    Temukan posisi magang atau penelitian yang sesuai dengan bidang dan minatmu.
                </p>
            </div>

            {{-- SEARCH --}}
            <div class="max-w-2xl mx-auto mb-8">
                <x-search-lowongan action="{{ route('landing.index') }}" search="{{ $search }}" />
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

    {{-- ===================== ABOUT SECTION — MODERN CLEAN REDESIGN ===================== --}}
    {{--
        KONSEP: Tipografi sebagai elemen visual utama.
        Layout: 2 kolom — kiri narasi + angka besar, kanan foto tunggal bersih + satu baris pilar.
        Warna: Putih bersih, aksen biru hanya pada border dan angka.
        Tidak ada badge melayang, tidak ada card bertumpuk.
    --}}
    <section class="bg-white py-24 border-t border-gray-100" id="about">
        <div class="max-w-7xl mx-auto px-6">

            {{-- Label kecil di atas --}}
            <div class="flex items-center gap-3 mb-16">
                <div class="h-px flex-1 bg-gray-100"></div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest px-3">Tentang Kami</span>
                <div class="h-px flex-1 bg-gray-100"></div>
            </div>

            <div class="grid lg:grid-cols-2 gap-20 items-start">

                {{-- ── KIRI: NARASI + ANGKA BESAR ── --}}
                <div>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-6">
                        Dinas Tenaga Kerja <br>
                        <span class="text-blue-600">Prov. Jawa Timur</span>
                    </h2>

                    <p class="text-gray-500 leading-relaxed text-base mb-12 max-w-md">
                        Berfokus pada peningkatan kompetensi tenaga kerja, pengembangan lapangan kerja,
                        perlindungan hak tenaga kerja, dan pengelolaan transmigrasi demi kesejahteraan
                        masyarakat Jawa Timur.
                    </p>

                    {{-- Angka besar dengan border kiri tipis — pengganti logo kosong --}}
                    <div class="space-y-0 divide-y divide-gray-100 border-y border-gray-100">

                        <div class="flex items-center justify-between py-5">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Alumni Magang</p>
                                <p class="text-sm text-gray-500 leading-relaxed">Peserta yang berhasil menyelesaikan
                                    program</p>
                            </div>
                            <p class="text-4xl font-extrabold text-blue-600 tabular-nums">500+</p>
                        </div>

                        <div class="flex items-center justify-between py-5">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Divisi Aktif</p>
                                <p class="text-sm text-gray-500 leading-relaxed">Pilihan bidang sesuai minat & jurusan</p>
                            </div>
                            <p class="text-4xl font-extrabold text-gray-900 tabular-nums">15+</p>
                        </div>

                        <div class="flex items-center justify-between py-5">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Peserta Aktif</p>
                                <p class="text-sm text-gray-500 leading-relaxed">Sedang menjalani program magang saat ini
                                </p>
                            </div>
                            <p class="text-4xl font-extrabold text-gray-900 tabular-nums">100+</p>
                        </div>

                        <div class="flex items-center justify-between py-5">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Rata-rata Durasi
                                </p>
                                <p class="text-sm text-gray-500 leading-relaxed">Program magang terstruktur & terarah</p>
                            </div>
                            <p class="text-4xl font-extrabold text-gray-900 tabular-nums">3 Bln</p>
                        </div>

                    </div>
                </div>

                {{-- ── KANAN: FOTO BERSIH + SATU BARIS PILAR ── --}}
                <div class="flex flex-col gap-8">

                    {{-- Foto tunggal — proporsional, tidak ramai --}}
                    <div class="relative overflow-hidden rounded-2xl aspect-4/3 bg-gray-100">
                        <img src="https://kilasjatim.com/wp-content/uploads/2025/04/100-e1744800110750.webp"
                            class="w-full h-full object-cover object-center transition-transform duration-700 hover:scale-105"
                            alt="Dinas Tenaga Kerja Jawa Timur" />

                        {{-- Caption strip bawah —  clean, tidak overlay penuh --}}
                        <div
                            class="absolute bottom-0 left-0 right-0 bg-white/90 backdrop-blur-sm border-t border-gray-200/50 px-5 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-800">Disnakertrans Jawa Timur</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Pemerintah Provinsi Jawa Timur</p>
                            </div>
                            <span
                                class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full">
                                Resmi
                            </span>
                        </div>
                    </div>

                    {{-- 4 pilar sebagai chip ringan dalam 1 baris --}}
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Fokus Program</p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ([['bi-people-fill', 'Kompetensi', 'Peningkatan skill tenaga kerja'], ['bi-shield-check-fill', 'Perlindungan', 'Hak-hak tenaga kerja terjamin'], ['bi-graph-up-arrow', 'Lapangan Kerja', 'Pengembangan peluang kerja'], ['bi-house-heart-fill', 'Transmigrasi', 'Pengelolaan transmigrasi terencana']] as $p)
                                <div
                                    class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:border-blue-100 hover:bg-blue-50/30 transition-all duration-200">
                                    <div
                                        class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="bi {{ $p[0] }} text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">{{ $p[1] }}</p>
                                        <p class="text-[10px] text-gray-400 leading-relaxed mt-0.5">{{ $p[2] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    {{-- ===================== GALLERY SECTION — MODERN CLEAN REDESIGN ===================== --}}
    {{--
        KONSEP: Bento grid — ukuran foto bervariasi dalam satu grid ketat.
        Hover: overlay minimalis, teks muncul dari bawah dengan efek smooth.
        Tidak ada caption permanen, tidak ada tag strip di bawah.
        Background: abu sangat terang agar foto "pop".
    --}}
    <section class="py-24 bg-gray-50" id="gallery">
        <div class="max-w-7xl mx-auto px-6">

            {{-- Header minimalis --}}
            <div class="mb-12">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Dokumentasi</span>
                <div class="flex items-end justify-between mt-2">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">Galeri Kegiatan</h2>
                    <p class="text-sm text-gray-400 hidden md:block">Momen dari kegiatan magang Disnakertrans Jatim</p>
                </div>
                <div class="h-px bg-gray-200 mt-6"></div>
            </div>

            {{-- BENTO GRID: asimetris 3 kolom, 2 baris --}}
            <div class="grid grid-cols-1 md:grid-cols-12 grid-rows-1 md:grid-rows-2 gap-3"
                style="height: auto; min-height: 480px;">

                {{-- [1] Foto besar kiri — baris 1+2, kolom 1–5 --}}
                <div class="md:col-span-5 md:row-span-2 group relative overflow-hidden rounded-2xl bg-gray-200 min-h-64">
                    <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-1.jpeg"
                        alt="Kegiatan Magang"
                        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" />
                    {{-- Overlay hover --}}
                    <div class="absolute inset-0 bg-gray-900/0 group-hover:bg-gray-900/40 transition-all duration-500">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 p-5 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-400">
                        <p class="text-white text-sm font-bold leading-tight">Aktivitas Divisi TIK</p>
                        <p class="text-white/70 text-xs mt-1">Disnakertrans Jawa Timur</p>
                    </div>
                    {{-- Nomor pojok --}}
                    <div
                        class="absolute top-4 left-4 w-7 h-7 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <span class="text-[10px] font-extrabold text-gray-800">01</span>
                    </div>
                </div>

                {{-- [2] Foto kanan atas — baris 1, kolom 6–9 --}}
                <div class="md:col-span-4 md:row-span-1 group relative overflow-hidden rounded-2xl bg-gray-200 min-h-48">
                    <img src="https://www.suarasurabaya.net/wp-content/uploads/2023/01/Kegiatan-Uji-Kompetensi-Kejuruan-HMI-Berbasis-PLC-yang-dilakukan-UPT-Balai-Latihan-Kerja-Surabaya-735x493.jpg.webp"
                        alt="Uji Kompetensi"
                        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gray-900/0 group-hover:bg-gray-900/40 transition-all duration-500">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 p-4 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-400">
                        <p class="text-white text-xs font-bold">Uji Kompetensi Kejuruan</p>
                        <p class="text-white/70 text-[10px] mt-0.5">Balai Latihan Kerja Surabaya</p>
                    </div>
                    <div
                        class="absolute top-4 left-4 w-7 h-7 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <span class="text-[10px] font-extrabold text-gray-800">02</span>
                    </div>
                </div>

                {{-- [3] Kotak kanan atas kecil — baris 1, kolom 10–12, info card --}}
                <div
                    class="md:col-span-3 md:row-span-1 rounded-2xl bg-blue-600 p-6 flex flex-col justify-between min-h-48">
                    <div>
                        <i class="bi bi-mortarboard-fill text-blue-200 text-2xl mb-3 block"></i>
                        <p class="text-white font-extrabold text-xl leading-tight">Program<br>Magang<br>Resmi</p>
                    </div>
                    <div>
                        <p class="text-blue-200 text-xs leading-relaxed">
                            Dikelola langsung oleh Pemprov Jawa Timur dengan pembimbingan terstruktur.
                        </p>
                        <div class="mt-3 h-px bg-blue-500"></div>
                        <p class="text-blue-300 text-[10px] mt-2 font-semibold uppercase tracking-wider">Sejak 2020</p>
                    </div>
                </div>

                {{-- [4] Foto bawah tengah — baris 2, kolom 6–9 --}}
                <div class="md:col-span-4 md:row-span-1 group relative overflow-hidden rounded-2xl bg-gray-200 min-h-48">
                    <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-2.jpeg"
                        alt="Workshop Magang"
                        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gray-900/0 group-hover:bg-gray-900/40 transition-all duration-500">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 p-4 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-400">
                        <p class="text-white text-xs font-bold">Workshop & Pelatihan</p>
                        <p class="text-white/70 text-[10px] mt-0.5">Divisi Pengembangan SDM</p>
                    </div>
                    <div
                        class="absolute top-4 left-4 w-7 h-7 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <span class="text-[10px] font-extrabold text-gray-800">03</span>
                    </div>
                </div>

                {{-- [5] Foto bawah kanan — baris 2, kolom 10–12 --}}
                <div class="md:col-span-3 md:row-span-1 group relative overflow-hidden rounded-2xl bg-gray-200 min-h-48">
                    <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-3.jpeg"
                        alt="Dokumentasi Magang"
                        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gray-900/0 group-hover:bg-gray-900/40 transition-all duration-500">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 p-4 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-400">
                        <p class="text-white text-xs font-bold">Dokumentasi Harian</p>
                        <p class="text-white/70 text-[10px] mt-0.5">Divisi Umum</p>
                    </div>
                    <div
                        class="absolute top-4 left-4 w-7 h-7 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <span class="text-[10px] font-extrabold text-gray-800">04</span>
                    </div>
                </div>

            </div>

        </div>
    </section>

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
