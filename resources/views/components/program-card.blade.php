{{--
    PERUBAHAN DARI VERSI SEBELUMNYA:
    1. Overlay gradient gelap muncul saat hover — depth, tidak flat
    2. Tombol "Lihat Detail" glass morphism muncul di tengah gambar saat hover
    3. Card naik (-translate-y-1.5) + shadow biru saat hover
    4. Border berubah ke biru samar saat hover
    5. CTA arrow: background pill biru kecil, geser ke kanan saat hover
    6. Deskripsi: line-clamp 2 baris agar semua card seragam tingginya
    7. Badge warna: magang = biru, penelitian = ungu (sesuai design system job-card)

    YANG TIDAK BERUBAH:
    - onclick openModal() tetap sama persis
    - Semua prop ($title, $category, $image, $description, $longDescription) tidak berubah
--}}

<div onclick="openModal(
        '{{ $title }}',
        '{{ $category }}',
        '{{ $image }}',
        '{{ $longDescription }}'
    )"
    class="group cursor-pointer bg-white rounded-2xl overflow-hidden
           border border-gray-100 transition-all duration-300
           hover:-translate-y-1.5
           hover:shadow-[0_20px_40px_rgba(37,99,235,0.10),0_4px_12px_rgba(0,0,0,0.06)]
           hover:border-blue-100/80">

    {{-- ── IMAGE WRAPPER ── --}}
    <div class="relative h-52 overflow-hidden">

        <img src="{{ $image }}" alt="{{ $title }}"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.07]">

        {{-- Overlay gelap gradient muncul saat hover --}}
        <div
            class="absolute inset-0 bg-linear-to-t from-slate-900/50 to-transparent
                    opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        </div>

        {{-- Tombol CTA di tengah gambar, muncul saat hover --}}
        <div
            class="absolute inset-0 flex items-center justify-center
                    opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <span
                class="flex items-center gap-2 px-4 py-2 rounded-full
                         bg-white/15 backdrop-blur-sm border border-white/25
                         text-white text-xs font-bold tracking-wide
                         transition-transform duration-200 group-hover:scale-105">
                <i class="bi bi-eye text-sm"></i> Lihat Detail
            </span>
        </div>

        {{-- Badge kategori --}}
        <div class="absolute top-3 left-3">
            <span
                class="text-white text-[10px] font-extrabold uppercase tracking-wider
                         px-2.5 py-1 rounded-full shadow-lg
                         {{ str_contains(strtolower($category), 'penelitian')
                             ? 'bg-violet-600 shadow-violet-600/30'
                             : 'bg-blue-600 shadow-blue-600/30' }}">
                {{ $category }}
            </span>
        </div>

    </div>

    {{-- ── CARD BODY ── --}}
    <div class="p-5">

        <h3
            class="text-[15px] font-extrabold text-gray-800 mb-2 leading-snug
                   transition-colors duration-200 group-hover:text-blue-600
                   line-clamp-2">
            {{ $title }}
        </h3>

        <p class="text-gray-500 text-[13px] leading-relaxed mb-4 line-clamp-2">
            {{ $description }}
        </p>

        {{-- Footer CTA --}}
        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <span class="flex items-center gap-2 text-blue-600 font-extrabold text-[11px] uppercase tracking-wider">
                Detail Program
                {{-- Arrow pill: background muncul, geser kanan saat hover --}}
                <span
                    class="flex items-center justify-center w-5 h-5 rounded-full
                            bg-blue-50 text-blue-600 text-[10px]
                            transition-all duration-200
                            group-hover:bg-blue-600 group-hover:text-white group-hover:translate-x-0.5">
                    <i class="bi bi-arrow-right"></i>
                </span>
            </span>
        </div>

    </div>

</div>
