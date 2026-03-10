@extends('layouts.layoutlanding')

@section('title', 'Homepage')

@section('content')

    {{-- HERO --}}
    <section class="relative w-full h-80" id="home">
        <div class="absolute inset-0 opacity-70">
            <img src="https://image1.jdomni.in/banner/13062021/0A/52/CC/1AF5FC422867D96E06C4B7BD69_1623557926542.png"
                class="w-full h-full object-cover object-center">
        </div>

        <div class="absolute inset-9 flex items-center">
            <div class="md:w-1/2">
                <h1 class="text-3xl md:text-4xl font-semibold text-gray-800">
                    Temukan Tempat Magang Impianmu
                </h1>

                <p class="text-lg mt-4">
                    Bergabunglah dengan Dinas Tenaga Kerja untuk pengalaman magang yang nyata
                </p>
            </div>
        </div>
    </section>


    {{-- SEARCH --}}
    <section class="px-10 py-10" id="lowongan">

        <div class="max-w-xl mx-auto">
            <form action="{{ route('landing.index') }}" method="GET" class="flex gap-2 bg-white p-2 rounded-lg shadow-lg">

                <input type="text" name="search" placeholder="Cari posisi magang..."
                    class="flex-1 px-4 py-2 outline-none rounded-md">

                <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-2 rounded-md font-semibold">
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



    {{-- PROGRAM MAGANG --}}
    <section class="py-10">

        <div class="container mx-auto px-4">

            <h2 class="text-3xl font-bold text-center mb-8">
                Program Magang
            </h2>

            <div class="grid md:grid-cols-3 gap-8">

                @for ($i = 1; $i <= 3; $i++)
                    <div class="bg-white rounded-lg shadow overflow-hidden">

                        <img src="https://images.unsplash.com/photo-1606854428728-5fe3eea23475"
                            class="w-full h-64 object-cover">

                        <div class="p-6 text-center">

                            <h3 class="text-xl font-medium mb-2">
                                Program Magang {{ $i }}
                            </h3>

                            <p class="text-gray-600 text-sm">
                                Program pelatihan kerja untuk meningkatkan keterampilan peserta
                                magang agar siap menghadapi dunia kerja.
                            </p>

                        </div>

                    </div>
                @endfor

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



    {{-- TAB SCRIPT --}}
    <script>
        function showTab(tab) {

            document.getElementById('tab-semua').classList.add('hidden');
            document.getElementById('tab-magang').classList.add('hidden');
            document.getElementById('tab-penelitian').classList.add('hidden');

            document.getElementById('btn-semua').classList.remove('border-blue-600');
            document.getElementById('btn-magang').classList.remove('border-blue-600');
            document.getElementById('btn-penelitian').classList.remove('border-blue-600');

            document.getElementById('tab-' + tab).classList.remove('hidden');

            document.getElementById('btn-' + tab).classList.add('border-blue-600');

        }
    </script>

@endsection
