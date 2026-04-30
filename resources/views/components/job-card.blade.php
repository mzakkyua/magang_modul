{{--
=====================================================================
BLADE COMPONENT: x-job-card
=====================================================================
CARA PAKAI: <x-job-card :job="$job" />

FITUR:
  - Badge "Baru" jika diterbitkan <= 7 hari yang lalu
  - Banner deadline merah jika tutup <= 3 hari
  - Banner deadline kuning jika tutup <= 7 hari
  - Jumlah pelamar (dari total_applications_count)
  - Info tanggal diterbitkan
  - State sold-out: card redup, tombol nonaktif, accent abu
  - Progress bar kuota dengan warna dinamis

DATA YANG DIBUTUHKAN DARI CONTROLLER:
  - active_applications_count  -> untuk getSisaKuota()
  - total_applications_count   -> untuk jumlah pelamar
  - created_at                 -> untuk badge "Baru" & tanggal terbit
  - end_date                   -> untuk hitung deadline
=====================================================================
--}}

@php
    // Kuota
    $sisa = $job->getSisaKuota();
    $filled = $job->quota_slots - $sisa;
    $percentage = $job->quota_slots > 0 ? round(($filled / $job->quota_slots) * 100) : 0;
    $isSoldOut = $sisa <= 0;

    // Tipe & warna
    $isMagang = $job->type === 'magang';
    $accentBar = $isSoldOut ? 'bg-gray-300' : ($isMagang ? 'bg-blue-500' : 'bg-violet-500');
    $typeBadgeBg = $isMagang
        ? 'bg-blue-50 text-blue-700 border-blue-200'
        : 'bg-violet-50 text-violet-700 border-violet-200';
    $btnClass = $isMagang
        ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-600/20 group-hover:shadow-blue-600/30'
        : 'bg-violet-600 hover:bg-violet-700 shadow-violet-600/20 group-hover:shadow-violet-600/30';

    // Progress bar warna
    $barColor = match (true) {
        $percentage >= 80 => 'bg-red-400',
        $percentage >= 50 => 'bg-amber-400',
        default => $isMagang ? 'bg-blue-500' : 'bg-violet-500',
    };

    // Badge Baru: diterbitkan <= 7 hari lalu
    $isNew = $job->created_at && $job->created_at->diffInDays(now()) <= 7;

    // Deadline urgency
    $daysUntilEnd = now()
        ->startOfDay()
        ->diffInDays($job->end_date->startOfDay(), false);
    $isUrgent = !$isSoldOut && $daysUntilEnd >= 0 && $daysUntilEnd <= 3;
    $isSoon = !$isSoldOut && !$isUrgent && $daysUntilEnd >= 0 && $daysUntilEnd <= 7;

    // Jumlah pelamar
    $totalPelamar = $job->total_applications_count ?? 0;

    // Mode pendaftaran
    $modeLabel = match ($job->registration_mode ?? '') {
        'individu' => 'Individu',
        'kelompok' => 'Kelompok',
        'hybrid' => 'Individu / Kelompok',
        default => null,
    };

    // Route detail
    $detailRoute = Auth::guard('magang')->check() ? route('dashboard.show', $job->id) : route('landing.show', $job->id);
@endphp

<div data-search-card data-title="{{ strtolower($job->title) }}" data-division="{{ strtolower($job->division_name) }}"
    data-type="{{ strtolower($job->type) }}"
    class="group bg-white rounded-2xl border border-gray-100 flex flex-col h-full overflow-hidden transition-all duration-300
        {{ $isSoldOut
            ? 'opacity-60 cursor-not-allowed'
            : 'shadow-sm hover:shadow-xl hover:border-blue-100 hover:-translate-y-1' }}">
    {{-- Accent bar --}}
    <div class="h-1 w-full {{ $accentBar }}"></div>

    <div class="p-5 flex flex-col h-full grow">

        {{-- Header: Judul + Badges --}}
        <div class="flex justify-between items-start gap-3 mb-3">
            <h3
                class="text-base font-bold text-gray-900 leading-snug line-clamp-2
                {{ !$isSoldOut ? 'group-hover:text-blue-700 transition-colors' : '' }}">
                {{ $job->title }}
            </h3>
            <div class="flex flex-col items-end gap-1.5 shrink-0">
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full tracking-wide border {{ $typeBadgeBg }}">
                    {{ strtoupper($job->type) }}
                </span>
                @if ($isSoldOut)
                    <span
                        class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200 whitespace-nowrap">
                        Kuota Penuh
                    </span>
                @elseif ($isNew)
                    <span
                        class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                        Baru
                    </span>
                @endif
            </div>
        </div>

        {{-- Banner Deadline --}}
        @if ($isUrgent)
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-red-50 border border-red-100 mb-3">
                <span class="w-2 h-2 rounded-full bg-red-500 shrink-0 animate-pulse"></span>
                <span class="text-xs font-semibold text-red-700">
                    Tutup dalam {{ $daysUntilEnd }} hari &mdash; {{ $job->end_date->translatedFormat('d M Y') }}
                </span>
            </div>
        @elseif ($isSoon)
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-50 border border-amber-100 mb-3">
                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                <span class="text-xs font-semibold text-amber-700">
                    Tutup dalam {{ $daysUntilEnd }} hari &mdash; {{ $job->end_date->translatedFormat('d M Y') }}
                </span>
            </div>
        @endif

        {{-- Tags: Divisi + Mode --}}
        <div class="flex flex-wrap gap-1.5 mb-4">
            <span
                class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-lg border border-gray-100 text-gray-600 bg-gray-50">
                <span class="w-1.5 h-1.5 rounded-full {{ $isMagang ? 'bg-blue-500' : 'bg-violet-500' }}"></span>
                {{ $job->division_name }}
            </span>
            @if ($modeLabel)
                <span
                    class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-lg border border-gray-100 text-gray-600 bg-gray-50">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    {{ $modeLabel }}
                    @if (in_array($job->registration_mode, ['kelompok', 'hybrid']) && $job->min_members && $job->max_members)
                        ({{ $job->min_members }}&ndash;{{ $job->max_members }} orang)
                    @endif
                </span>
            @endif
        </div>

        {{-- Divider --}}
        <div class="h-px bg-gray-100 mb-4 -mx-5"></div>

        {{-- Info: Periode + Ukuran Tim --}}
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Periode</p>
                <p class="text-xs font-semibold text-gray-700">
                    {{ $job->start_date->translatedFormat('d M') }}
                    <span class="text-gray-300 mx-0.5">&mdash;</span>
                    {{ $job->end_date->translatedFormat('d M Y') }}
                </p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Ukuran Tim</p>
                @if (in_array($job->registration_mode, ['kelompok', 'hybrid']) && $job->min_members && $job->max_members)
                    <p class="text-xs font-semibold text-gray-700">
                        {{ $job->min_members }}&ndash;{{ $job->max_members }} orang</p>
                @else
                    <p class="text-xs font-semibold text-gray-700">Individu</p>
                @endif
            </div>
        </div>

        {{-- Kuota dengan progress bar --}}
        <div class="mb-1">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sisa Kuota</span>
                <span class="text-xs">
                    <strong class="{{ $isSoldOut ? 'text-red-500' : 'text-gray-800' }}">{{ $sisa }}</strong>
                    <span class="text-gray-400">/ {{ $job->quota_slots }} slot</span>
                </span>
            </div>
            <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 {{ $isSoldOut ? 'bg-gray-300 w-full' : $barColor }}"
                    style="width: {{ $isSoldOut ? 100 : $percentage }}%">
                </div>
            </div>
        </div>

        {{-- Row: Diterbitkan + Jumlah Pelamar --}}
        <div class="flex items-center justify-between mt-3 mb-4">
            <div class="flex items-center gap-1.5 text-gray-400">
                <svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none">
                    <rect x="1" y="3" width="14" height="12" rx="2" stroke="currentColor"
                        stroke-width="1.3" />
                    <path d="M1 7h14" stroke="currentColor" stroke-width="1.3" />
                    <path d="M5 1v4M11 1v4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                </svg>
                <span class="text-[11px]">
                    {{ $job->created_at ? $job->created_at->translatedFormat('d M Y') : '-' }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 text-gray-400">
                <svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none">
                    <circle cx="6" cy="5" r="2.5" stroke="currentColor" stroke-width="1.3" />
                    <path d="M1 13c0-2.761 2.239-5 5-5" stroke="currentColor" stroke-width="1.3"
                        stroke-linecap="round" />
                    <circle cx="11.5" cy="6" r="2" stroke="currentColor" stroke-width="1.3" />
                    <path d="M9 13c0-2.209 1.343-4 3-4h1" stroke="currentColor" stroke-width="1.3"
                        stroke-linecap="round" />
                </svg>
                <span class="text-[11px]">
                    <span class="font-semibold text-gray-600">{{ $totalPelamar }}</span> pelamar
                </span>
            </div>
        </div>

        {{-- CTA Button --}}
        <div class="mt-auto">
            @if (!$isSoldOut)
                <a href="{{ $detailRoute }}"
                    class="flex items-center justify-center gap-2 w-full text-white py-2.5 px-4 rounded-xl transition-all duration-200 font-semibold text-sm shadow-sm group-hover:shadow-md {{ $btnClass }}">
                    Lihat Detail
                    <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <div
                    class="flex items-center justify-center gap-2 w-full bg-gray-100 text-gray-400 py-2.5 px-4 rounded-xl font-semibold text-sm border border-gray-200 select-none">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Kuota Penuh
                </div>
            @endif
        </div>

    </div>
</div>
