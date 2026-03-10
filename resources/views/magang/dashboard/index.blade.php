@extends('layouts.layoutlanding')
@section('title', 'Dashboard Peserta')

@section('content')

    {{-- ================= HERO ================= --}}
    <section class="relative w-full h-80">
        <div class="absolute inset-0 opacity-70">
            <img src="https://image1.jdomni.in/banner/13062021/0A/52/CC/1AF5FC422867D96E06C4B7BD69_1623557926542.png"
                class="object-cover object-center w-full h-full" />
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

            <form action="{{ route('landing.index') }}" method="GET" class="flex gap-2 bg-white p-2 rounded-lg shadow">

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

                <button onclick="showTab('semua')" id="btn-semua" class="border-b-2 border-blue-600 pb-2 font-semibold">
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



            {{-- ================= TAB MAGANG ================= --}}
            <div id="tab-magang" class="hidden">

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach ($vacanciesMagang as $job)
                        <x-job-card :job="$job" />
                    @endforeach

                </div>

            </div>



            {{-- ================= TAB PENELITIAN ================= --}}
            <div id="tab-penelitian" class="hidden">

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach ($vacanciesPenelitian as $job)
                        <x-job-card :job="$job" />
                    @endforeach

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
