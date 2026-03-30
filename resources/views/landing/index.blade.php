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
    <section class="px-10 py-10">
        <div class="max-w-2xl mx-auto mb-10">

            <form action="{{ route('landing.index') }}" method="GET" class="flex gap-2 bg-white p-2 rounded-lg shadow">

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

                <a href="{{ route('landing.index') }}" class="ml-2 text-blue-600 text-sm">
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

    {{-- ABOUT --}}
    <section class="bg-gray-100 py-16">

        <div class="container mx-auto px-4">

            <div class="grid md:grid-cols-2 gap-8 items-center">

                <div>

                    <h2 class="text-3xl font-bold mb-6">
                        Tentang Kami
                    </h2>

                    <p class="text-gray-600">
                        Dinas Tenaga Kerja Provinsi Jawa Timur berfokus pada peningkatan
                        kompetensi tenaga kerja, pengembangan lapangan kerja,
                        perlindungan hak tenaga kerja, dan pengelolaan transmigrasi.
                    </p>

                </div>

                <div>

                    <img src="https://kilasjatim.com/wp-content/uploads/2025/04/100-e1744800110750.webp"
                        class="rounded-lg shadow">

                </div>

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
                <img src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-1.jpeg"
                    alt="Image 1"
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


            <!-- Repeat this div for each image -->
        </div>
    @endsection
