{{--
    Section: Lowongan Aktif per Bidang
    File: resources/views/components/division-capacity-landing.blade.php

    FIX: Semua akses key $item[] kini menggunakan null coalescing (?? fallback)
    agar tidak crash ketika divisi baru tidak memiliki key tertentu
    (contoh: last_batch_end, has_unlimited, open_vacancies, dll).
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
            {{-- DIVISI MEMBUKA PENDAFTARAN                            --}}
            {{-- ===================================================== --}}
            @php
                $openDivisions = $divisionStats->where('has_open', true)->values();
                $closedDivisions = $divisionStats->where('has_open', false)->values();
            @endphp

            @if ($openDivisions->isNotEmpty())
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">
                            Membuka Pendaftaran
                        </h3>
                        <span
                            class="text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            {{ $openDivisions->count() }} bidang
                        </span>
                    </div>

                    <div class="flex flex-wrap justify-center gap-4">
                        @foreach ($openDivisions as $item)
                            @php
                                $totalQuota = $item['total_quota'] ?? 0;
                                $availableQuota = $item['total_available'] ?? 0;
                                $hasUnlimited = $item['has_unlimited'] ?? false;
                                $openVacancies = $item['open_vacancies'] ?? 0;
                                $divisionName = $item['division_name'] ?? '-';

                                $filledQuota = 0;
                                $percentFilled = 0;
                                if ($totalQuota > 0) {
                                    $filledQuota = $totalQuota - $availableQuota;
                                    $percentFilled = min(100, max(0, round(($filledQuota / $totalQuota) * 100)));
                                }
                            @endphp

                            <div
                                class="group flex flex-col w-full md:w-[calc(50%-8px)] lg:w-[calc(33.333%-11px)] max-w-md p-5 bg-white border border-emerald-100 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden">
                                {{-- Accent top hijau --}}
                                <div
                                    class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-emerald-400 to-emerald-600">
                                </div>

                                {{-- Header --}}
                                <div class="flex items-start gap-2.5 mb-3 mt-1.5">
                                    <div class="mt-0.5 text-emerald-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-gray-900 text-sm leading-snug">{{ $divisionName }}</h3>
                                </div>

                                <div class="flex-1">
                                    <div
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 mb-3 text-[11px] font-bold text-emerald-800 bg-emerald-100/80 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Membuka Pendaftaran
                                    </div>

                                    <div class="space-y-1.5 mb-4 text-xs text-gray-600">
                                        <p class="flex items-center justify-between">
                                            <span>Lowongan Aktif</span>
                                            <strong class="text-gray-900">{{ $openVacancies }}</strong>
                                        </p>
                                        @if ($hasUnlimited)
                                            <p class="flex items-center justify-between border-t border-gray-50 pt-1.5">
                                                <span>Kuota Peserta</span>
                                                <strong class="text-blue-600">Tidak Dibatasi</strong>
                                            </p>
                                        @else
                                            <p class="flex items-center justify-between border-t border-gray-50 pt-1.5">
                                                <span>Kuota Tersedia</span>
                                                <strong class="text-emerald-600">{{ $availableQuota }}</strong>
                                            </p>
                                        @endif
                                    </div>

                                    @if (!$hasUnlimited && $totalQuota > 0)
                                        <div class="mb-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <span
                                                    class="text-[10px] font-semibold text-gray-500">{{ $filledQuota }}
                                                    / {{ $totalQuota }} terisi</span>
                                                <span
                                                    class="text-[10px] font-black {{ $percentFilled >= 80 ? 'text-amber-600' : 'text-blue-600' }}">{{ $percentFilled }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="h-2 rounded-full transition-all duration-700 ease-out {{ $percentFilled >= 80 ? 'bg-amber-500' : 'bg-blue-600' }}"
                                                    style="width: {{ $percentFilled }}%"></div>
                                            </div>
                                            @if ($percentFilled >= 80)
                                                <p
                                                    class="text-[10px] text-amber-600 font-semibold mt-1.5 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                                    </svg>
                                                    Kuota hampir penuh, segera daftar!
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-1 pt-3 border-t border-gray-100">
                                    <button type="button"
                                        onclick="searchDivisionAndScroll('{{ addslashes($divisionName) }}')"
                                        class="flex items-center justify-center gap-1.5 w-full py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98] rounded-xl transition-all duration-200 shadow-sm">
                                        Lihat Lowongan
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ===================================================== --}}
            {{-- DIVISI BELUM BUKA / TUTUP                            --}}
            {{-- ===================================================== --}}
            @if ($closedDivisions->isNotEmpty())
                <div class="mt-2">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                        <h3 class="text-sm font-extrabold text-gray-500 uppercase tracking-wider">
                            Belum Membuka Pendaftaran
                        </h3>
                        <span
                            class="text-xs font-bold text-gray-500 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded-full">
                            {{ $closedDivisions->count() }} bidang
                        </span>
                    </div>

                    <div class="flex flex-wrap justify-center gap-4">
                        @foreach ($closedDivisions as $item)
                            @php
                                $divisionName = $item['division_name'] ?? '-';
                                $estimatedOpen = $item['estimated_open'] ?? null;
                                $lastBatchEnd = $item['last_batch_end'] ?? null;
                            @endphp

                            <div
                                class="flex flex-col w-full md:w-[calc(50%-8px)] lg:w-[calc(33.333%-11px)] max-w-md p-5 bg-white border border-gray-200 rounded-2xl shadow-sm opacity-80 relative overflow-hidden">
                                {{-- Accent top abu --}}
                                <div class="absolute top-0 left-0 w-full h-1 bg-gray-200"></div>

                                {{-- Header --}}
                                <div class="flex items-start gap-2.5 mb-3 mt-1.5">
                                    <div class="mt-0.5 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-gray-600 text-sm leading-snug">{{ $divisionName }}</h3>
                                </div>

                                <div class="flex-1">
                                    <div
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 mb-3 text-[11px] font-bold text-gray-600 bg-gray-100 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Belum Ada Lowongan
                                    </div>

                                    <div
                                        class="p-3 bg-gray-50 rounded-xl border border-gray-100 text-xs text-gray-500 mb-3">

                                        {{-- Batch terakhir selesai (faktual) --}}
                                        @if ($lastBatchEnd)
                                            <p
                                                class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">
                                                Batch Terakhir Selesai
                                            </p>
                                            <div class="flex items-center gap-1.5 mb-2">
                                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none"
                                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12l2 2 4-4M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="font-bold text-gray-700">{{ $lastBatchEnd }}</p>
                                            </div>
                                        @endif

                                        {{-- Perkiraan batch berikutnya --}}
                                        @if ($estimatedOpen)
                                            <div class="{{ $lastBatchEnd ? 'border-t border-gray-100 pt-2' : '' }}">
                                                <p
                                                    class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">
                                                    Perkiraan Batch Berikutnya
                                                </p>
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none"
                                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <p class="font-bold text-blue-600">Sekitar {{ $estimatedOpen }}
                                                    </p>
                                                </div>
                                                <p class="text-[10px] text-gray-400 mt-1 leading-relaxed">
                                                    Estimasi berdasarkan batch terakhir. Pantau terus untuk info resmi.
                                                </p>
                                            </div>
                                        @elseif (!$lastBatchEnd)
                                            <p class="text-[10px] leading-relaxed">
                                                Belum ada data periode magang sebelumnya untuk bidang ini.
                                            </p>
                                        @endif

                                    </div>
                                </div>

                                <div class="mt-1 pt-3 border-t border-gray-100">
                                    <div
                                        class="flex items-center justify-center w-full py-2.5 text-xs font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        Pantau Gelombang Berikutnya
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endif
