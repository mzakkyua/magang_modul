{{--
    Component: Penjelasan Divisi Magang (100% Dynamic Auto-Generator)
    File: resources/views/components/division-info-landing.blade.php
--}}
@props(['divisionStats' => collect()])

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;

    // 1. AMBIL SEMUA DIVISI AKTIF DARI DATABASE
    $activeDivisions = DB::table('divisions_magang')->where('is_active', true)->orderBy('name')->pluck('name');

    /**
     * ========================================================================
     * 2. KAMUS IKON SPESIFIK (HANYA UNTUK IKON SVG)
     * ========================================================================
     * Foto background sekarang OTOMATIS membaca dari folder:
     * public/assets/images/divisions/
     */
    $customIcons = [
        'hubungan industrial' =>
            'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
        'pelatihan dan produktivitas' =>
            'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z M12 14v7',
        'pengembangan sdm dan tik' =>
            'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    ];

    /**
     * ========================================================================
     * 3. FALLBACK ICONS (JIKA DIVISI TIDAK ADA DI KAMUS ATAS)
     * ========================================================================
     */
    $fallbackIcons = [
        'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', // Briefcase
        'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', // Building
        'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', // People
        'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', // Shield
    ];

    // ========================================================================
    // 4. PROSES PEMBUATAN KONTEN & PENCARIAN FOTO OTOMATIS
    // ========================================================================
    $finalDivisions = [];
    foreach ($activeDivisions as $name) {
        $nameLower = strtolower(trim($name));
        $slug = Str::slug($name); // Mengubah "Hubungan Industrial" jadi "hubungan-industrial"
        $seed = abs(crc32($name));

        // A. CEK FOTO LOKAL (Apakah ada file yg namanya sesuai dengan slug divisi?)
        $imageAssetPath = null;
        $extensions = ['jpg', 'jpeg', 'png'];

        foreach ($extensions as $ext) {
            $checkPath = "assets/images/divisions/{$slug}.{$ext}";
            if (file_exists(public_path($checkPath))) {
                $imageAssetPath = $checkPath;
                break; // Ketemu! Hentikan pencarian.
            }
        }

        // B. JIKA FOTO SPESIFIK TIDAK ADA, GUNAKAN FALLBACK (default-1 s/d default-4)
        if (!$imageAssetPath) {
            $fallbackNum = ($seed % 4) + 1; // Menghasilkan angka 1 sampai 4
            $imageAssetPath = "assets/images/divisions/default-{$fallbackNum}.jpg";
        }

        // C. BUAT URL DENGAN CACHE BUSTER
        $imageUrl =
            asset($imageAssetPath) .
            '?v=' .
            (file_exists(public_path($imageAssetPath)) ? filemtime(public_path($imageAssetPath)) : '1');

        // D. TENTUKAN IKON
        $iconSvg = $customIcons[$nameLower] ?? $fallbackIcons[$seed % count($fallbackIcons)];

        $finalDivisions[] = [
            'name' => $name,
            'category' => 'Bidang ' . Str::words($name, 2, ''),
            'short_desc' =>
                'Fokus pada pelayanan, pengelolaan, dan pelaksanaan program terkait ' . strtolower($name) . '.',
            'long_desc' =>
                'Divisi ' .
                $name .
                ' memiliki peran penting dalam menyelenggarakan fungsi pelayanan publik, perencanaan, dan eksekusi program kerja di sektor ' .
                strtolower($name) .
                ' guna mendukung visi dan misi instansi secara keseluruhan.',
            'image' => $imageUrl,
            'icon' => $iconSvg,
        ];
    }
@endphp

@if (count($finalDivisions) > 0)
    <section class="py-16 md:py-24 bg-white border-t border-gray-100" id="divisions">
        <div class="mx-auto max-w-6xl px-6">

            {{-- Header Section --}}
            <div class="text-center mb-16">
                <span
                    class="inline-block px-3 py-1 mb-3 text-[10px] font-bold tracking-widest text-blue-700 uppercase bg-blue-50 border border-blue-100 rounded-full">
                    Eksplorasi Peminatan
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
                    Kenali Bidang Kami
                </h2>
                <p class="text-gray-500 max-w-2xl mx-auto text-sm md:text-base leading-relaxed">
                    Temukan divisi yang sesuai dengan latar belakang dan minat Anda. Berikut adalah seluruh bidang yang
                    tersedia di instansi kami.
                </p>
            </div>

            {{-- Cards Grid --}}
            <div class="flex flex-wrap justify-center gap-6">
                @foreach ($finalDivisions as $index => $div)
                    <div
                        class="group flex flex-col w-full md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] max-w-md bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 text-left">

                        {{-- Image Header --}}
                        <div class="relative h-48 overflow-hidden bg-gray-100">
                            <img src="{{ $div['image'] }}" alt="{{ $div['name'] }}" loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div
                                class="absolute inset-0 bg-linear-to-t from-gray-900/80 via-gray-900/20 to-transparent">
                            </div>

                            {{-- Icon & Category Badge --}}
                            <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between">
                                <div
                                    class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white border border-white/30 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $div['icon'] }}">
                                        </path>
                                    </svg>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-white bg-blue-600/90 backdrop-blur-sm px-2.5 py-1 rounded-full uppercase tracking-wider max-w-37.5 truncate text-right">
                                    {{ $div['category'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Content Body --}}
                        <div class="p-6 flex flex-col flex-1">
                            <h3
                                class="text-lg font-bold text-gray-900 mb-2 leading-snug group-hover:text-blue-600 transition-colors">
                                {{ $div['name'] }}
                            </h3>
                            <p class="text-sm text-gray-500 leading-relaxed mb-6 line-clamp-2">
                                {{ $div['short_desc'] }}
                            </p>

                            {{-- Trigger Modal Button --}}
                            <button type="button" onclick="openDivisionModal({{ $index }})"
                                class="mt-auto flex items-center text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 ml-1.5 transition-transform group-hover:translate-x-1"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- MODAL CONTAINER --}}
        <div id="divisionModal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeDivisionModal()"></div>

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div id="divisionModalPanel"
                    class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full scale-95 opacity-0 duration-300">

                    <button onclick="closeDivisionModal()"
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-black/20 hover:bg-black/40 text-white rounded-full transition-colors z-10 backdrop-blur-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <div class="w-full h-56 sm:h-72 bg-gray-100 relative">
                        <img id="modDivImage" src="" alt="Divisi Image" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-linear-to-t from-gray-900/90 to-transparent"></div>
                        <div class="absolute bottom-6 left-6 right-6">
                            <span id="modDivCategory"
                                class="text-[10px] font-bold text-white bg-blue-600 px-3 py-1 rounded-full uppercase tracking-widest mb-3 inline-block"></span>
                            <h3 id="modDivTitle" class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
                            </h3>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 bg-white">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Deskripsi Tugas &
                            Tanggung Jawab</p>
                        <p id="modDivDesc" class="text-sm sm:text-base text-gray-600 leading-relaxed"></p>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button onclick="closeDivisionModal()"
                            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-bold rounded-xl transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @once
        @push('script')
            <script>
                const divisionData = @json($finalDivisions);
                const modalOverlay = document.getElementById('divisionModal');
                const modalPanel = document.getElementById('divisionModalPanel');

                function openDivisionModal(index) {
                    const data = divisionData[index];
                    if (!data) return;

                    document.getElementById('modDivImage').src = data.image;
                    document.getElementById('modDivImage').alt = data.name;
                    document.getElementById('modDivCategory').textContent = data.category;
                    document.getElementById('modDivTitle').textContent = data.name;
                    document.getElementById('modDivDesc').textContent = data.long_desc;

                    modalOverlay.classList.remove('hidden');
                    void modalOverlay.offsetWidth;
                    modalOverlay.classList.remove('opacity-0');
                    modalPanel.classList.remove('scale-95', 'opacity-0');
                    modalPanel.classList.add('scale-100', 'opacity-100');
                    document.body.style.overflow = 'hidden';
                }

                function closeDivisionModal() {
                    modalOverlay.classList.add('opacity-0');
                    modalPanel.classList.remove('scale-100', 'opacity-100');
                    modalPanel.classList.add('scale-95', 'opacity-0');

                    setTimeout(() => {
                        modalOverlay.classList.add('hidden');
                        document.body.style.overflow = 'auto';
                    }, 300);
                }

                document.addEventListener('keydown', function(event) {
                    if (event.key === "Escape" && !modalOverlay.classList.contains('hidden')) {
                        closeDivisionModal();
                    }
                });
            </script>
        @endpush
    @endonce
@endif
