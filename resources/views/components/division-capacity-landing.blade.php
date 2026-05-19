@if ($divisionCapacity->isNotEmpty())

    @php
        // Summary stats — tidak ada class Tailwind di sini, aman
        $totalDivisi = $divisionCapacity->count();
        $totalFilled = $divisionCapacity->sum('filled_slots');
        $totalAvailable = $divisionCapacity->whereNotNull('available_slots')->sum('available_slots');
        $totalFull = $divisionCapacity->where('is_full', true)->count();
    @endphp

    <section class="px-4 md:px-6 py-16 bg-linear-to-b from-gray-50 to-white border-t border-gray-100">
        <div class="max-w-6xl mx-auto">

            {{-- ===================================================== --}}
            {{-- HEADER --}}
            {{-- ===================================================== --}}
            <div class="text-center mb-10">
                <span
                    class="inline-block text-blue-600 font-semibold text-xs uppercase tracking-[0.2em] bg-blue-50 border border-blue-100 px-3 py-1 rounded-full mb-3">
                    Informasi Ketersediaan
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 mt-2">
                    Status Kapasitas per Divisi
                </h2>
                <p class="text-gray-500 mt-3 text-sm max-w-lg mx-auto leading-relaxed">
                    Pantau ketersediaan slot magang di setiap divisi secara real-time
                    dan lihat estimasi kapan lowongan kembali tersedia.
                </p>
            </div>

            {{-- ===================================================== --}}
            {{-- SUMMARY BAR --}}
            {{-- ===================================================== --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-10">

                <div class="bg-white border border-gray-100 rounded-2xl px-4 py-4 text-center shadow-sm">
                    <p class="text-2xl font-extrabold text-gray-800">{{ $totalDivisi }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5 font-medium">Total Divisi</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl px-4 py-4 text-center shadow-sm">
                    <p class="text-2xl font-extrabold text-emerald-600">{{ $totalAvailable }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5 font-medium">Slot Tersedia</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl px-4 py-4 text-center shadow-sm">
                    <p class="text-2xl font-extrabold text-blue-600">{{ $totalFilled }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5 font-medium">Slot Terisi</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl px-4 py-4 text-center shadow-sm">
                    <p @class([
                        'text-2xl font-extrabold',
                        'text-red-500' => $totalFull > 0,
                        'text-gray-800' => $totalFull === 0,
                    ])>{{ $totalFull }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5 font-medium">Divisi Penuh</p>
                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- CARDS --}}
            {{-- ===================================================== --}}
            <div class="flex flex-wrap justify-center gap-5">

                @foreach ($divisionCapacity as $item)
                    {{--
                        FIX: Semua kondisi warna ditulis langsung di atribut @class()
                        atau ternary di dalam string class — bukan di variabel @php.

                        Tailwind JIT scanner membaca template sebagai teks.
                        Selama class name muncul sebagai string utuh di template
                        (bukan dibangun via variabel PHP), class akan ter-deteksi
                        dan TIDAK di-purge saat production build.
                    --}}

                    <div @class([
                        'w-full sm:w-[300px] bg-white rounded-2xl shadow-sm p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300',
                        'border border-red-100 hover:border-red-200' => $item['is_full'],
                        'border border-gray-100 hover:border-blue-100' => !$item['is_full'],
                    ])>

                        {{-- Header card --}}
                        <div class="flex items-start justify-between mb-4">

                            <div class="flex items-center gap-3 min-w-0">

                                {{-- Icon container --}}
                                <div @class([
                                    'w-10 h-10 rounded-xl flex items-center justify-center shrink-0',
                                    'bg-red-50 border border-red-100' => $item['is_full'],
                                    'bg-blue-50 border border-blue-100' => !$item['is_full'],
                                ])>
                                    <i @class([
                                        'bi bi-building text-sm',
                                        'text-red-400' => $item['is_full'],
                                        'text-blue-500' => !$item['is_full'],
                                    ])></i>
                                </div>

                                <div class="min-w-0">
                                    <p class="font-bold text-gray-800 text-sm leading-tight truncate"
                                        title="{{ $item['division_name'] }}">
                                        {{ $item['division_name'] }}
                                    </p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Divisi Magang</p>
                                </div>

                            </div>

                            {{-- Status dot --}}
                            <span @class([
                                'w-2.5 h-2.5 rounded-full shrink-0 mt-1.5',
                                'bg-red-400' => $item['is_full'],
                                'bg-emerald-400 animate-pulse' => !$item['is_full'],
                            ])></span>

                        </div>

                        {{-- Progress --}}
                        @if ($item['max_slots'] !== null)
                            <div class="mb-4">

                                <div class="flex items-end justify-between mb-2">
                                    <span class="text-[11px] text-gray-400">
                                        {{ $item['filled_slots'] }} dari {{ $item['max_slots'] }} slot terisi
                                    </span>
                                    <span @class([
                                        'text-sm font-extrabold',
                                        'text-red-500' => $item['is_full'],
                                        'text-amber-500' => !$item['is_full'] && $item['fill_percentage'] >= 70,
                                        'text-emerald-600' => !$item['is_full'] && $item['fill_percentage'] < 70,
                                    ])>
                                        {{ $item['fill_percentage'] }}%
                                    </span>
                                </div>

                                {{-- Progress bar --}}
                                <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div @class([
                                        'h-full rounded-full transition-all duration-500',
                                        'bg-red-400' => $item['is_full'],
                                        'bg-amber-400' => !$item['is_full'] && $item['fill_percentage'] >= 70,
                                        'bg-emerald-400' => !$item['is_full'] && $item['fill_percentage'] < 70,
                                    ]) style="width: {{ $item['fill_percentage'] }}%">
                                    </div>
                                </div>

                            </div>
                        @else
                            <div class="mb-4 flex items-center gap-1.5 text-[11px] text-gray-400">
                                <i class="bi bi-infinity text-blue-400"></i>
                                {{ $item['filled_slots'] }} lowongan aktif · Tidak ada batas slot
                            </div>
                        @endif

                        {{-- Divider --}}
                        <div class="border-t border-gray-50 my-3"></div>

                        {{-- Status badge --}}
                        @if ($item['is_full'])
                            <div class="space-y-2.5">
                                <span
                                    class="inline-flex items-center gap-1.5 text-xs font-bold bg-red-50 text-red-600 border border-red-100 px-3 py-2 rounded-xl w-full justify-center">
                                    <i class="bi bi-x-circle-fill text-[10px]"></i>
                                    Slot Penuh — Tidak Menerima Pendaftar
                                </span>
                                @if ($item['estimated_open'])
                                    <div
                                        class="flex items-center justify-center gap-1.5 text-[11px] text-gray-500 bg-gray-50 rounded-xl px-3 py-2">
                                        <i class="bi bi-calendar-event text-gray-400"></i>
                                        Estimasi buka kembali:
                                        <strong class="text-gray-700 ml-0.5">{{ $item['estimated_open'] }}</strong>
                                    </div>
                                @endif
                            </div>
                        @elseif ($item['max_slots'] === null)
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 px-3 py-2 rounded-xl w-full justify-center">
                                <i class="bi bi-infinity text-[10px]"></i>
                                Terbuka · Tanpa Batas Slot
                            </span>
                        @else
                            <div
                                class="flex items-center justify-between bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2">
                                <span class="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                                    <i class="bi bi-check-circle-fill text-[10px]"></i>
                                    Menerima Pendaftar
                                </span>
                                <span
                                    class="text-xs font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                                    {{ $item['available_slots'] }} slot
                                </span>
                            </div>
                        @endif

                    </div>
                @endforeach

            </div>

            {{-- ===================================================== --}}
            {{-- FOOTER --}}
            {{-- ===================================================== --}}
            <div class="mt-10 text-center">
                <p class="text-[11px] text-gray-400">
                    <i class="bi bi-info-circle mr-1"></i>
                    Estimasi bulan dihitung berdasarkan tanggal berakhir program magang yang sedang berjalan.
                    Data diperbarui setiap 10 menit.
                </p>
            </div>

        </div>
    </section>

@endif
