{{--
    Section: Lowongan Aktif per Bidang
    File: resources/views/components/division-capacity-landing.blade.php
--}}

@props(['divisionStats' => collect()])

@if ($divisionStats->isNotEmpty())
    @php
        $totalOpenVacancies = $divisionStats->sum('open_vacancies');
        $totalAvailable = $divisionStats->sum('total_available');
        $totalActiveDivisions = $divisionStats->where('has_open', true)->count();
    @endphp

    <section class="px-4 py-10 bg-gray-50 md:px-6 border-t border-gray-100">
        <div class="mx-auto max-w-6xl">

            {{-- ===================================================== --}}
            {{-- HEADER SECTION (COMPACT)                              --}}
            {{-- ===================================================== --}}
            <div class="max-w-3xl mx-auto text-center mb-8">
                <span
                    class="inline-block px-3 py-1 mb-3 text-[10px] font-bold tracking-widest text-blue-700 uppercase bg-blue-100 border border-blue-200 rounded-full shadow-sm">
                    Status Ketersediaan Magang
                </span>
                <p class="text-gray-500 text-sm md:text-base font-medium leading-relaxed">
                    Lihat bidang yang sedang membuka penerimaan peserta magang dan ketersediaan kuota saat ini.
                </p>
            </div>

            {{-- ===================================================== --}}
            {{-- SUMMARY SECTION (COMPACT)                             --}}
            {{-- ===================================================== --}}
            <div class="grid grid-cols-2 gap-3 mb-10 md:grid-cols-4">

                {{-- Card 1: Bidang Membuka --}}
                <div
                    class="flex flex-col items-center justify-center p-4 bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <div class="w-10 h-10 flex items-center justify-center bg-blue-50 text-blue-600 rounded-full mb-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center mb-0.5">Bidang
                        Membuka</p>
                    <p class="text-2xl font-black text-gray-900">{{ $totalActiveDivisions }}</p>
                </div>

                {{-- Card 2: Lowongan Aktif --}}
                <div
                    class="flex flex-col items-center justify-center p-4 bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <div
                        class="w-10 h-10 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-full mb-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center mb-0.5">Lowongan
                        Aktif</p>
                    <p class="text-2xl font-black text-gray-900">{{ $totalOpenVacancies }}</p>
                </div>

                {{-- Card 3: Kuota Tersedia --}}
                <div
                    class="flex flex-col items-center justify-center p-4 bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <div
                        class="w-10 h-10 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-full mb-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center mb-0.5">Kuota
                        Tersedia</p>
                    <p class="text-2xl font-black text-emerald-600">{{ $totalAvailable }}</p>
                </div>

                {{-- Card 4: Update Data --}}
                <div
                    class="flex flex-col items-center justify-center p-4 bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <div
                        class="w-10 h-10 flex items-center justify-center bg-gray-100 text-gray-500 rounded-full mb-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center mb-0.5">Update
                        Data</p>
                    <p class="text-lg font-bold text-gray-700 mt-1">10 Menit</p>
                </div>
            </div>

            {{-- ===================================================== --}}
            {{-- DIVISION CARDS CONTAINER (FLEXBOX CENTERING)          --}}
            {{-- ===================================================== --}}
            <div class="flex flex-wrap justify-center gap-4">

                @foreach ($divisionStats as $item)
                    @php
                        $filledQuota = 0;
                        $percentFilled = 0;
                        $totalQuota = $item['total_quota'] ?? 0;
                        $availableQuota = $item['total_available'] ?? 0;

                        if ($totalQuota > 0) {
                            $filledQuota = $totalQuota - $availableQuota;
                            $percentFilled = round(($filledQuota / $totalQuota) * 100);
                            $percentFilled = min(100, max(0, $percentFilled));
                        }
                    @endphp

                    {{-- Rumus calc() untuk memastikan lebar presisi 3 kolom tapi bisa rata tengah --}}
                    <div
                        class="group flex flex-col w-full md:w-[calc(50%-8px)] lg:w-[calc(33.333%-11px)] max-w-md p-5 bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden text-left">

                        {{-- Decoration accent --}}
                        @if ($item['has_open'])
                            <div
                                class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-emerald-400 to-emerald-600">
                            </div>
                        @else
                            <div class="absolute top-0 left-0 w-full h-1 bg-gray-200"></div>
                        @endif

                        {{-- Header Card --}}
                        <div class="flex items-start gap-2.5 mb-3 mt-1.5">
                            <div class="mt-0.5 text-gray-400 group-hover:text-blue-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-sm leading-snug">
                                {{ $item['division_name'] }}
                            </h3>
                        </div>

                        <div class="flex-1">
                            @if ($item['has_open'])
                                {{-- 🟢 BADGE: BUKA --}}
                                <div
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 mb-3 text-[11px] font-bold text-emerald-800 bg-emerald-100/80 rounded-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Membuka Pendaftaran
                                </div>

                                {{-- Info Text --}}
                                <div class="space-y-1.5 mb-4 text-xs text-gray-600">
                                    <p class="flex items-center justify-between">
                                        <span>Lowongan Aktif</span>
                                        <strong class="text-gray-900">{{ $item['open_vacancies'] }}</strong>
                                    </p>
                                    @if ($item['has_unlimited'])
                                        <p class="flex items-center justify-between border-t border-gray-50 pt-1.5">
                                            <span>Kuota Peserta</span>
                                            <strong class="text-blue-600">Tidak Dibatasi</strong>
                                        </p>
                                    @else
                                        <p class="flex items-center justify-between border-t border-gray-50 pt-1.5">
                                            <span>Kuota Tersedia</span>
                                            <strong class="text-emerald-600">{{ $item['total_available'] }}</strong>
                                        </p>
                                    @endif
                                </div>

                                {{-- Progress Bar --}}
                                @if (!$item['has_unlimited'] && $totalQuota > 0)
                                    <div class="mb-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-[10px] font-semibold text-gray-500">
                                                {{ $filledQuota }} / {{ $totalQuota }} terisi
                                            </span>
                                            <span
                                                class="text-[10px] font-black {{ $percentFilled >= 80 ? 'text-amber-600' : 'text-blue-600' }}">
                                                {{ $percentFilled }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all duration-700 ease-out {{ $percentFilled >= 80 ? 'bg-amber-500' : 'bg-blue-600' }}"
                                                style="width: {{ $percentFilled }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{-- 🔴 BADGE: TUTUP --}}
                                <div
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 mb-3 text-[11px] font-bold text-red-800 bg-red-50 rounded-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Belum Ada Lowongan
                                </div>

                                {{-- Info Text (Faktual) --}}
                                <div
                                    class="p-3 bg-gray-50 rounded-xl border border-gray-100 text-xs text-gray-500 mb-3">
                                    @if ($item['last_batch_end'])
                                        <p class="text-[10px] font-medium mb-0.5">Program magang periode terakhir telah
                                            selesai pada:</p>
                                        <p class="font-bold text-gray-800">{{ $item['last_batch_end'] }}</p>
                                    @else
                                        <p class="text-[10px] leading-relaxed">
                                            Belum ada data periode magang sebelumnya untuk bidang ini.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-1 pt-3 border-t border-gray-100">
                            @if ($item['has_open'])
                                <button type="button"
                                    onclick="searchDivisionAndScroll('{{ addslashes($item['division_name']) }}')"
                                    class="flex items-center justify-center gap-1.5 w-full py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98] rounded-xl transition-all duration-200 shadow-sm hover:shadow-blue-200">
                                    Lihat Lowongan
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </button>
                            @else
                                <div
                                    class="flex items-center justify-center w-full py-2.5 text-xs font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                                    Pantau Gelombang Berikutnya
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </section>
@endif
