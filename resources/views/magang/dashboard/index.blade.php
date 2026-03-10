@extends('layouts.layoutlanding')
@section('title', 'Dashboard Peserta')

@section('content')

    <!-- hero seciton -->
    <div class="relative w-full h-80" id="home">
        <div class="absolute inset-0 opacity-70">
            <img src="https://www.freepik.com/free-vector/gradient-smooth-blue-lines-background_14063419.htm#fromView=keyword&page=1&position=14&uuid=443f2888-c7b6-4c82-b02d-7c5308791890&query=Blue+background"
                alt="Background Image" class="object-cover object-center w-full h-full" />

        </div>
        <div class="absolute inset-9 flex flex-col md:flex-row items-center justify-between">
            <div class="md:w-1/2 mb-4 md:mb-0">
                <h1 class="text-grey-700 font-medium text-3xl md:text-4xl leading-tight mb-2">Temukan Tempat Magang Impianmu
                </h1>
                <p class="font-regular text-xl mb-8 mt-4">Bergabunglah dengan Dinas Tenaga Kerja untuk pengalaman magang yang
                    nyata</p>

            </div>

            <div class="absolute inset-9 flex items-center">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold">
                        Dashboard Magang
                    </h1>

                    <p class="mt-3 text-lg">
                        Selamat datang {{ Auth::guard('magang')->user()->nama_peserta }}
                    </p>
                </div>
            </div>
            </section>



            {{-- ================= SEARCH ================= --}}
            <section class="px-10 py-10">

                <div class="max-w-xl mx-auto">

                    <form action="{{ route('landing.index') }}" method="GET"
                        class="flex gap-2 bg-white p-2 rounded-lg shadow">

                        <input type="text" name="search" placeholder="Cari posisi magang..."
                            class="flex-1 px-4 py-2 outline-none rounded-md">

                        <button type="submit" class="bg-blue-800 text-white px-6 py-2 rounded-md">

                            Cari
                        </button>

                    </form>

                </div>



                {{-- ================= LOWONGAN ================= --}}
                <div class="max-w-7xl mx-auto py-12">

                    <h2 class="text-3xl font-bold mb-6 border-l-4 border-blue-600 pl-4">
                        Lowongan Tersedia
                    </h2>


                    {{-- ================= TAB NAVIGATION ================= --}}
                    {{-- 
Tab ini menggunakan JavaScript sederhana.
Jika nanti ingin upgrade:
- AlpineJS
- Livewire
--}}
                    <div class="flex gap-6 border-b mb-8">

                        <button onclick="showTab('semua')" id="btn-semua"
                            class="border-b-2 border-blue-600 pb-2 font-semibold">
                            Semua
                        </button>

                        <button onclick="showTab('magang')" id="btn-magang" class="text-gray-500 pb-2">
                            Magang
                        </button>

                        <button onclick="showTab('penelitian')" id="btn-penelitian" class="text-gray-500 pb-2">
                            Penelitian
                        </button>

                    </div>



                    {{-- ================= TAB SEMUA ================= --}}
                    <div id="tab-semua">

                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                            {{-- tampilkan semua magang --}}
                            @foreach ($vacanciesMagang as $job)
                                <x-job-card :job="$job" />
                            @endforeach


                            {{-- tampilkan semua penelitian --}}
                            @foreach ($vacanciesPenelitian as $job)
                                <x-job-card :job="$job" />
                            @endforeach

                        </div>

                    </div>

                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="https://media.istockphoto.com/id/1265641298/photo/fried-papad.jpg?s=612x612&w=0&k=20&c=e_iEy4CTvU6Thn02zGgKt_TiSYAheCKmgfTF5j52ovU="
                            alt="papad" class="w-full h-64 object-cover">
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-medium text-gray-800 mb-2">Rice Papad</h3>
                            <p class="text-gray-700 text-base">Our company produces high-quality rice papad that is made
                                with
                                the finest ingredients. We use traditional methods to make our papad, which gives it a
                                unique
                                flavor and texture. Our papad is also gluten-free and vegan.
                            <details>
                                <summary>Read More</summary>
                                <p> We offer a variety of rice papad flavors, including plain, salted, spicy, and flavored.
                                    We
                                    also
                                    offer a variety of sizes and shapes to choose from. Our papad is available in bulk or in
                                    individual packages.</p>
                            </details>
                            </p>
                        </div>
                    </div>

                </div>
        </div>
        </section>

        <!-- about us -->
        <section class="bg-gray-100" id="aboutus">
            <div class="container mx-auto py-16 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-8">
                    <div class="max-w-lg">
                        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Tentang Kami</h2>
                        <p class="mt-4 text-gray-600 text-lg">
                            Dinas Tenaga Kerja dan Provinsi Jawa Timur berfokus pada peningkatan kompetensi tenaga kerja,
                            pengembangan lapangan kerja,
                            perlindungan hak tenaga kerja, dan pengelolaan transmigrasi berkelanjutan untuk kesejahteraan
                            masyarakat.</p>
                    </div>
                    <div class="mt-12 md:mt-0">
                        <img src="https://kilasjatim.com/wp-content/uploads/2025/04/100-e1744800110750.webp"
                            alt="About Us Image" class="object-cover rounded-lg shadow-md">
                    </div>
                </div>
            </div>
        </section>

        <!-- why us  -->
        <section class="text-gray-700 body-font mt-10">
            <div class="flex justify-center text-3xl font-bold text-gray-800 text-center">
                Why Us?
            </div>
            <div class="container px-5 py-12 mx-auto">
                <div class="flex flex-wrap text-center justify-center">
                    <div class="p-4 md:w-1/4 sm:w-1/2">
                        <div class="px-4 py-6 transform transition duration-500 hover:scale-110">
                            <div class="flex justify-center">
                                <img src="https://image3.jdomni.in/banner/13062021/58/97/7C/E53960D1295621EFCB5B13F335_1623567851299.png?output-format=webp"
                                    class="w-32 mb-3">
                            </div>
                            <h2 class="title-font font-regular text-2xl text-gray-900">Latest Milling Machinery</h2>
                        </div>
                    </div>

                    <div class="p-4 md:w-1/4 sm:w-1/2">
                        <div class="px-4 py-6 transform transition duration-500 hover:scale-110">
                            <div class="flex justify-center">
                                <img src="https://image2.jdomni.in/banner/13062021/3E/57/E8/1D6E23DD7E12571705CAC761E7_1623567977295.png?output-format=webp"
                                    class="w-32 mb-3">
                            </div>
                            <h2 class="title-font font-regular text-2xl text-gray-900">Reasonable Rates</h2>
                        </div>
                    </div>

                    <div class="p-4 md:w-1/4 sm:w-1/2">
                        <div class="px-4 py-6 transform transition duration-500 hover:scale-110">
                            <div class="flex justify-center">
                                <img src="https://image3.jdomni.in/banner/13062021/16/7E/7E/5A9920439E52EF309F27B43EEB_1623568010437.png?output-format=webp"
                                    class="w-32 mb-3">
                            </div>
                            <h2 class="title-font font-regular text-2xl text-gray-900">Time Efficiency</h2>
                        </div>
                    </div>

                    <div class="p-4 md:w-1/4 sm:w-1/2">
                        <div class="px-4 py-6 transform transition duration-500 hover:scale-110">
                            <div class="flex justify-center">
                                <img src="https://image3.jdomni.in/banner/13062021/EB/99/EE/8B46027500E987A5142ECC1CE1_1623567959360.png?output-format=webp"
                                    class="w-32 mb-3">
                            </div>
                            <h2 class="title-font font-regular text-2xl text-gray-900">Expertise in Industry</h2>
                        </div>
                    </div>

                </div>
        </section>



        {{-- ================= TIMELINE ================= --}}
        <section class="bg-gray-100 py-16">

            <div class="container mx-auto grid md:grid-cols-2 gap-8">

                <div>

                    <h2 class="text-3xl font-bold mb-4">
                        Timeline Magang
                    </h2>

                    <p class="text-gray-600">
                        Peserta dapat melihat periode kegiatan magang,
                        tanggal mulai hingga selesai.
                    </p>

                </div>

                <div class="bg-white p-4 rounded-xl shadow">

                    @include('calendar')

                </div>

            </div>

        </section>



        {{-- ================= SCRIPT TAB ================= --}}
        <script>
            function showTab(tab) {

                // sembunyikan semua tab
                document.getElementById('tab-semua').classList.add('hidden');
                document.getElementById('tab-magang').classList.add('hidden');
                document.getElementById('tab-penelitian').classList.add('hidden');

                // reset style tombol
                document.getElementById('btn-semua').classList.remove('border-blue-600');
                document.getElementById('btn-magang').classList.remove('border-blue-600');
                document.getElementById('btn-penelitian').classList.remove('border-blue-600');

                // tampilkan tab aktif
                document.getElementById('tab-' + tab).classList.remove('hidden');

                // highlight tombol aktif
                document.getElementById('btn-' + tab).classList.add('border-blue-600');

            }
        </script>

    @endsection
