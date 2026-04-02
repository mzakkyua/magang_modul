@extends('layouts.landing')

@section('title', 'Homepage - SINAKERTRANS')

@section('content')

    {{-- ===================== HERO SECTION ===================== --}}
    {{-- BUGFIX: div konten dipindah ke DALAM wrapper relative --}}
    <section class="relative w-full h-130 md:h-150 overflow-hidden" id="home">

        {{-- Background Image --}}
        <div class="absolute inset-0">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b2/New_office.jpg" alt="Background Kantor"
                class="object-cover object-center w-full h-full" />

            {{-- Gradient Overlay: lebih hidup dari sekedar hitam transparan --}}
            <div class="absolute inset-0 bg-linear-to-r from-blue-900/90 via-blue-800/75 to-blue-700/30"></div>
        </div>

        {{-- Hero Content: sekarang di DALAM wrapper sehingga posisi benar --}}
        <div class="relative z-10 h-full flex items-center">
            <div class="max-w-7xl mx-auto px-6 w-full">
                <div class="max-w-xl">

                    {{-- Badge label --}}
                    <span
                        class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-blue-200 text-xs font-semibold px-4 py-2 rounded-full mb-5 backdrop-blur-sm">
                        <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                        Dinas Tenaga Kerja & Transmigrasi Prov. Jawa Timur
                    </span>

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
                        <a href="#about"
                            class="border border-white/40 text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-white/10 transition backdrop-blur-sm flex items-center gap-2">
                            <i class="bi bi-info-circle"></i> Tentang Program
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- Stats Bar di bawah hero --}}
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
                <form action="{{ route('landing.index') }}" method="GET">
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
                    <a href="{{ route('landing.index') }}"
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

    {{-- ===================== ABOUT SECTION ===================== --}}
    <section class="bg-linear-to-br from-blue-50 via-white to-slate-50 py-20" id="about">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12 items-center">

                {{-- Teks --}}
                <div>
                    <span class="text-blue-600 font-semibold text-xs uppercase tracking-widest">Siapa Kami</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mt-2 mb-6 leading-tight">
                        Tentang Dinas Tenaga Kerja
                        <span class="text-blue-600">Jawa Timur</span>
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-8">
                        Dinas Tenaga Kerja Provinsi Jawa Timur berfokus pada peningkatan kompetensi tenaga kerja,
                        pengembangan lapangan kerja, perlindungan hak tenaga kerja, dan pengelolaan transmigrasi
                        demi kesejahteraan masyarakat Jawa Timur.
                    </p>

                    {{-- Feature cards --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                                <i class="bi bi-people-fill text-blue-600 text-lg"></i>
                            </div>
                            <p class="font-bold text-gray-800 text-sm">Kompetensi</p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Peningkatan skill tenaga kerja
                                profesional</p>
                        </div>
                        <div
                            class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                                <i class="bi bi-shield-check-fill text-blue-600 text-lg"></i>
                            </div>
                            <p class="font-bold text-gray-800 text-sm">Perlindungan</p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Hak-hak tenaga kerja terjamin &
                                terlindungi</p>
                        </div>
                        <div
                            class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                                <i class="bi bi-graph-up-arrow text-blue-600 text-lg"></i>
                            </div>
                            <p class="font-bold text-gray-800 text-sm">Lapangan Kerja</p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Pengembangan peluang kerja yang luas</p>
                        </div>
                        <div
                            class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                                <i class="bi bi-house-heart-fill text-blue-600 text-lg"></i>
                            </div>
                            <p class="font-bold text-gray-800 text-sm">Transmigrasi</p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Pengelolaan transmigrasi yang terencana
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Gambar --}}
                <div class="relative">
                    {{-- Dekorasi background --}}
                    <div class="absolute -top-6 -left-6 w-32 h-32 bg-blue-100 rounded-3xl -z-10 opacity-60"></div>
                    <div class="absolute -bottom-6 -right-6 w-40 h-40 bg-blue-50 rounded-full -z-10"></div>

                    <img src="https://kilasjatim.com/wp-content/uploads/2025/04/100-e1744800110750.webp"
                        class="rounded-2xl shadow-2xl w-full object-cover border-4 border-white"
                        alt="Tentang Dinas Tenaga Kerja Jawa Timur">
                </div>

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
                        class="absolute inset-0 bg-linear-to-t from-blue-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <i class="bi bi-zoom-in text-white text-xl"></i>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl shadow-md">
                    <img src="https://www.suarasurabaya.net/wp-content/uploads/2023/01/Kegiatan-Uji-Kompetensi-Kejuruan-HMI-Berbasis-PLC-yang-dilakukan-UPT-Balai-Latihan-Kerja-Surabaya-735x493.jpg.webp"
                        alt="Dokumentasi Magang 2"
                        class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div
                        class="absolute inset-0 bg-linear-to-t from-blue-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <i class="bi bi-zoom-in text-white text-xl"></i>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl shadow-md">
                    <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-2.jpeg"
                        alt="Dokumentasi Magang 3"
                        class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div
                        class="absolute inset-0 bg-linear-to-t from-blue-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <i class="bi bi-zoom-in text-white text-xl"></i>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl shadow-md">
                    <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-3.jpeg"
                        alt="Dokumentasi Magang 4"
                        class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div
                        class="absolute inset-0 bg-linear-to-t from-blue-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <i class="bi bi-zoom-in text-white text-xl"></i>
                    </div>
                </div>

            </div>

        </div>
    </section>

    {{-- ===================== SCRIPTS ===================== --}}
    @push('script')
        <script>
            // SECTION: Tab Lowongan — switching antar tab Semua / Magang / Penelitian
            document.addEventListener('DOMContentLoaded', function() {

                const tabBtns = document.querySelectorAll('.tab-btn');
                const tabContents = document.querySelectorAll('.tab-content');

                tabBtns.forEach(function(btn) {
                    btn.addEventListener('click', function() {

                        // STEP 1: Reset semua tombol ke inactive
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

                        // STEP 4: Tampilkan konten tab yang sesuai
                        var target = btn.getAttribute('data-tab');
                        document.getElementById('tab-' + target).classList.remove('hidden');
                    });
                });

            });
        </script>

        <style>
            /* SECTION: Tab button active/inactive state */
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
    @endpush

@endsection
