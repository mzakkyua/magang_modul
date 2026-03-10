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
    <section class="py-16 bg-gray-50" id="services">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-[#37517e] mb-4">Program Magang</h2>
                <p class="text-gray-600 max-w-2xl mx-auto italic">Pilih divisi yang sesuai dengan bidang keahlian dan minat
                    akademik Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div
                    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://www.sos.co.id/assets/images/news/1751275811.webp" alt="Administrasi"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 left-4">
                            <span
                                class="bg-blue-600 text-white text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-lg">Administrasi</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition">Administrasi
                            Umum & Perkantoran</h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">Mempelajari tata kelola dokumen dinas,
                            surat-menyurat formal, dan manajemen kearsipan digital di lingkungan pemerintahan.</p>
                        <div class="flex items-center gap-2 text-blue-600 font-bold text-xs uppercase tracking-wider">
                            <span>Detail Program</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://www.score.org/sites/default/files/styles/responsive_16_9_500w/public/d7_migration/42/AdobeStock_377657914%20%281%29.jpg?itok=32he_-io"
                            alt="Hubungan Industrial"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 left-4">
                            <span
                                class="bg-indigo-600 text-white text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-lg">Hubungan
                                Kerja</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-indigo-600 transition">Hubungan
                            Industrial</h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">Terlibat dalam proses mediasi hubungan kerja,
                            pemantauan kesejahteraan pekerja, dan sosialisasi peraturan ketenagakerjaan.</p>
                        <div class="flex items-center gap-2 text-indigo-600 font-bold text-xs uppercase tracking-wider">
                            <span>Detail Program</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-blue-100 ring-2 ring-blue-50">
                    <div class="relative h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600&auto=format&fit=crop"
                            alt="IT Support"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 left-4">
                            <span
                                class="bg-cyan-600 text-white text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-lg">IT
                                & Sistem Informasi</span>
                        </div>
                        <div class="absolute inset-0 bg-blue-600/10 group-hover:bg-transparent transition-colors"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition">Pengembangan
                            Web & IT Support</h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">Membantu pengelolaan website dinas,
                            pemeliharaan server, dan pengembangan aplikasi pelayanan publik digital (e-government).</p>
                        <div class="flex items-center gap-2 text-blue-600 font-bold text-xs uppercase tracking-wider">
                            <span>Detail Program</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
   


        {{-- SEARCH --}}
        <section class="px-10 py-10" id="lowongan">

            <div class="max-w-xl mx-auto">
                <form action="{{ route('landing.index') }}" method="GET"
                    class="flex gap-2 bg-white p-2 rounded-lg shadow-lg">

                    <input type="text" name="search" placeholder="Cari posisi magang..."
                        class="flex-1 px-4 py-2 outline-none rounded-md">

                    <button type="submit"
                        class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-2 rounded-md font-semibold">
                        Cari
                    </button>

                </form>
            </div>



            {{-- LOWONGAN --}}
            <div class="max-w-7xl mx-auto py-12">

                <h2 class="text-3xl font-bold mb-6 border-l-4 border-blue-600 pl-4">
                    Lowongan Terbaru
                </h2>


                {{-- TAB BUTTON --}}
                <div class="flex gap-6 border-b mb-8">

                    <button onclick="showTab('semua')" id="btn-semua"
                        class="tab-btn border-b-2 border-blue-600 pb-2 font-semibold">
                        Semua
                    </button>

                    <button onclick="showTab('magang')" id="btn-magang" class="tab-btn pb-2 text-gray-500">
                        Magang
                    </button>

                    <button onclick="showTab('penelitian')" id="btn-penelitian" class="tab-btn pb-2 text-gray-500">
                        Penelitian
                    </button>

                </div>



                {{-- TAB SEMUA --}}
                <div id="tab-semua">

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                        @foreach ($vacancies as $job)
                            <x-job-card :job="$job" />
                        @endforeach

                    </div>

                </div>



                {{-- TAB MAGANG --}}
                <div id="tab-magang" class="hidden">

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                        @foreach ($vacancies->where('type', 'magang') as $job)
                            <x-job-card :job="$job" />
                        @endforeach

                    </div>

                </div>



                {{-- TAB PENELITIAN --}}
                <div id="tab-penelitian" class="hidden">

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                        @foreach ($vacancies->where('type', 'penelitian') as $job)
                            <x-job-card :job="$job" />
                        @endforeach

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
            <img
      src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-1.jpeg"
      alt="Image 1"
      class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105"
    />
        </div>

        <div class="group relative">
            <img
      src="https://www.suarasurabaya.net/wp-content/uploads/2023/01/Kegiatan-Uji-Kompetensi-Kejuruan-HMI-Berbasis-PLC-yang-dilakukan-UPT-Balai-Latihan-Kerja-Surabaya-735x493.jpg.webp"
      alt="Image 1"
      class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105"
    />
        </div>

        <div class="group relative">
            <img
      src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-2.jpeg"
      alt="Image 1"
      class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105"
    />
        </div>
        <div class="group relative">
            <img
      src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-3.jpeg"
      alt="Image 1"
      class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105"
    />
        </div>

        
        <!-- Repeat this div for each image -->
    </div>
@endsection
