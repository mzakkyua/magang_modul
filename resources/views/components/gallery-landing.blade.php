{{--
    Component: Galeri Kegiatan (Bento Grid dengan Auto-Wrap)
    File: resources/views/components/gallery-landing.blade.php
--}}

@php
    /**
     * ========================================================================
     * PANDUAN MENAMBAH/MENGGANTI FOTO GALERI
     * ========================================================================
     * 1. Siapkan foto, beri nama (misal: kegiatan-1.jpg).
     * 2. Taruh file fotonya di folder: public/assets/images/gallery/
     * 3. Tambahkan ke dalam array $photos di bawah ini.
     * * JANGAN KHAWATIR HANCUR!
     * - 4 Foto pertama akan otomatis mengisi layout "Bento Grid" asimetris aslimu.
     * - Foto ke-5 dan seterusnya akan otomatis berbaris rapi di bawahnya (3 kolom rata).
     */
    $photos = [
        [
            'image' => 'assets/images/gallery/foto-1.jpg',
            'fallback' => 'https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-1.jpeg',
            'title' => 'Aktivitas Divisi TIK',
            'subtitle' => 'Disnakertrans Jawa Timur',
        ],
        [
            'image' => 'assets/images/gallery/foto-2.jpg',
            'fallback' =>
                'https://www.suarasurabaya.net/wp-content/uploads/2023/01/Kegiatan-Uji-Kompetensi-Kejuruan-HMI-Berbasis-PLC-yang-dilakukan-UPT-Balai-Latihan-Kerja-Surabaya-735x493.jpg.webp',
            'title' => 'Uji Kompetensi Kejuruan',
            'subtitle' => 'Balai Latihan Kerja Surabaya',
        ],
        [
            'image' => 'assets/images/gallery/foto-3.jpg',
            'fallback' => 'https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-2.jpeg',
            'title' => 'Workshop & Pelatihan',
            'subtitle' => 'Divisi Pengembangan SDM',
        ],
        [
            'image' => 'assets/images/gallery/foto-4.jpg',
            'fallback' => 'https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-3.jpeg',
            'title' => 'Dokumentasi Harian',
            'subtitle' => 'Divisi Umum',
        ],
        // CONTOH JIKA INGIN NAMBAH FOTO KE-5 (Buka komentarnya untuk mencoba):
        /*
        [
            'image' => 'assets/images/gallery/foto-5.jpg',
            'fallback' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=800',
            'title' => 'Rapat Koordinasi',
            'subtitle' => 'Gedung Serbaguna'
        ],
        */
    ];

    // Proses pengkondisian gambar (Lokal vs Fallback) dan pembagian grid (Pintar)
    $processedPhotos = [];
    foreach ($photos as $index => $photo) {
        // Cek apakah foto lokal ada, kalau ada pakai trik Anti-Pikun (Cache Buster)
        $imgUrl = file_exists(public_path($photo['image']))
            ? asset($photo['image']) . '?v=' . filemtime(public_path($photo['image']))
            : $photo['fallback'];

        // Mengatur Class Bento Grid secara dinamis berdasarkan urutan foto
        if ($index === 0) {
            $gridClass = 'md:col-span-5 md:row-span-2 min-h-64'; // Kiri Besar
        } elseif ($index === 1) {
            $gridClass = 'md:col-span-4 md:row-span-1 min-h-48'; // Atas Tengah
        } elseif ($index === 2) {
            $gridClass = 'md:col-span-4 md:row-span-1 min-h-48'; // Bawah Tengah
        } elseif ($index === 3) {
            $gridClass = 'md:col-span-3 md:row-span-1 min-h-48'; // Bawah Kanan
        } else {
            // Foto ke-5, ke-6, dst otomatis dibagi 3 kolom sejajar
            $gridClass = 'md:col-span-4 md:row-span-1 min-h-48 mt-3';
        }

        $processedPhotos[] = [
            'url' => $imgUrl,
            'title' => $photo['title'],
            'subtitle' => $photo['subtitle'],
            'class' => $gridClass,
            'number' => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
        ];
    }
@endphp

<section class="py-11 bg-gray-50" id="gallery">
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

        {{-- BENTO GRID CONTAINER --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3" style="height: auto; min-height: 480px;">

            {{-- 1. FOTO PERTAMA (Kiri Besar) --}}
            @if (count($processedPhotos) > 0)
                <div class="{{ $processedPhotos[0]['class'] }} group relative overflow-hidden rounded-2xl bg-gray-200">
                    <img src="{{ $processedPhotos[0]['url'] }}" alt="{{ $processedPhotos[0]['title'] }}" loading="lazy"
                        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gray-900/0 group-hover:bg-gray-900/40 transition-all duration-500">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 p-5 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-400">
                        <p class="text-white text-sm font-bold leading-tight">{{ $processedPhotos[0]['title'] }}</p>
                        <p class="text-white/70 text-xs mt-1">{{ $processedPhotos[0]['subtitle'] }}</p>
                    </div>
                    <div
                        class="absolute top-4 left-4 w-7 h-7 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <span
                            class="text-[10px] font-extrabold text-gray-800">{{ $processedPhotos[0]['number'] }}</span>
                    </div>
                </div>
            @endif

            {{-- 2. FOTO KEDUA (Atas Tengah) --}}
            @if (count($processedPhotos) > 1)
                <div class="{{ $processedPhotos[1]['class'] }} group relative overflow-hidden rounded-2xl bg-gray-200">
                    <img src="{{ $processedPhotos[1]['url'] }}" alt="{{ $processedPhotos[1]['title'] }}" loading="lazy"
                        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gray-900/0 group-hover:bg-gray-900/40 transition-all duration-500">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 p-4 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-400">
                        <p class="text-white text-xs font-bold">{{ $processedPhotos[1]['title'] }}</p>
                        <p class="text-white/70 text-[10px] mt-0.5">{{ $processedPhotos[1]['subtitle'] }}</p>
                    </div>
                    <div
                        class="absolute top-4 left-4 w-7 h-7 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <span
                            class="text-[10px] font-extrabold text-gray-800">{{ $processedPhotos[1]['number'] }}</span>
                    </div>
                </div>
            @endif

            {{-- 3. KOTAK TEKS INFO (SELALU BERADA DI KANAN ATAS AGAR SUSUNAN TIDAK HANCUR) --}}
            <div class="md:col-span-3 md:row-span-1 rounded-2xl bg-blue-600 p-6 flex flex-col justify-between min-h-48">
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

            {{-- 4. LOOPING SISA FOTO (FOTO KETIGA, KEEMPAT, KELIMA, DST) --}}
            @for ($i = 2; $i < count($processedPhotos); $i++)
                <div
                    class="{{ $processedPhotos[$i]['class'] }} group relative overflow-hidden rounded-2xl bg-gray-200">
                    <img src="{{ $processedPhotos[$i]['url'] }}" alt="{{ $processedPhotos[$i]['title'] }}"
                        loading="lazy"
                        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gray-900/0 group-hover:bg-gray-900/40 transition-all duration-500">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 p-4 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-400">
                        <p class="text-white text-xs font-bold">{{ $processedPhotos[$i]['title'] }}</p>
                        <p class="text-white/70 text-[10px] mt-0.5">{{ $processedPhotos[$i]['subtitle'] }}</p>
                    </div>
                    <div
                        class="absolute top-4 left-4 w-7 h-7 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <span
                            class="text-[10px] font-extrabold text-gray-800">{{ $processedPhotos[$i]['number'] }}</span>
                    </div>
                </div>
            @endfor

        </div>

    </div>
</section>
