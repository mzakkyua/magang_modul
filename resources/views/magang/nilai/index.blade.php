@extends('layouts.landing')

@section('title', 'Nilai Magang Saya - SINAKERTRANS')

@section('content')
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6">

        {{-- ── PAGE HEADER ── --}}
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-[#37517e] tracking-tight">Nilai Magang Saya</h1>
            <p class="text-sm text-gray-400 mt-1">
                Hasil evaluasi resmi dari pembimbing lapangan Disnakertrans
            </p>
        </div>

        @if ($member && $member->assessment)
            @php
                $assessment = $member->assessment;
                $vacancy = $member->application->vacancy;
                $passed = $assessment->isPassed();

                // Warna progress bar per skor
                $barColor = fn($s) => $s >= 80
                    ? 'bg-emerald-500'
                    : ($s >= 60
                        ? 'bg-blue-500'
                        : ($s >= 40
                            ? 'bg-amber-500'
                            : 'bg-red-500'));
            @endphp

            {{-- ── BANNER HASIL ── --}}
            <div class="rounded-2xl overflow-hidden mb-6 p-6 relative"
                style="background: linear-gradient(135deg, {{ $passed ? '#1e3a5f' : '#4c1130' }} 0%, {{ $passed ? '#2563eb' : '#dc2626' }} 100%);">

                {{-- Dekorasi --}}
                <div class="absolute top-0 right-0 w-48 h-48 pointer-events-none"
                    style="background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%); transform: translate(20%, -30%);">
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                     {{ $passed ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-red-500/20 text-red-300 border border-red-400/30' }}">
                                <i class="bi {{ $passed ? 'bi-patch-check-fill' : 'bi-x-octagon-fill' }} text-[11px]"></i>
                                {{ $passed ? 'LULUS' : 'TIDAK LULUS' }}
                            </span>
                        </div>
                        <p class="text-white/60 text-xs mb-1">Posisi</p>
                        <p class="text-white font-extrabold text-lg leading-tight">{{ $vacancy->title }}</p>
                        <p class="text-white/50 text-xs mt-1">
                            <i class="bi bi-building mr-1"></i>{{ $vacancy->division_name }}
                        </p>
                    </div>

                    {{-- Skor besar --}}
                    <div class="shrink-0 text-center bg-white/10 border border-white/20 rounded-2xl px-6 py-4">
                        <p class="text-white/50 text-[10px] uppercase tracking-widest font-bold mb-1">Skor Akhir</p>
                        <p class="text-5xl font-extrabold text-white leading-none">
                            {{ number_format($assessment->final_score, 0) }}
                        </p>
                        <p class="text-white/40 text-xs mt-1">dari 100</p>
                    </div>
                </div>
            </div>

            {{-- ── DETAIL SKOR 3 KOMPONEN ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-5">

                <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <i class="bi bi-bar-chart-fill text-blue-500 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-extrabold text-gray-900">Rincian Nilai Per Komponen</h2>
                </div>

                <div class="p-5 grid md:grid-cols-3 gap-5">

                    @php
                        $komponen = [
                            [
                                'label' => 'Perilaku',
                                'sub' => 'Etika, sopan santun, attitude',
                                'icon' => 'bi-person-check-fill',
                                'score' => $assessment->score_behavior,
                                'color' => 'blue',
                            ],
                            [
                                'label' => 'Kedisiplinan',
                                'sub' => 'Ketepatan waktu, kehadiran',
                                'icon' => 'bi-clock-fill',
                                'score' => $assessment->score_discipline,
                                'color' => 'indigo',
                            ],
                            [
                                'label' => 'Kinerja',
                                'sub' => 'Hasil kerja, inisiatif, skill',
                                'icon' => 'bi-lightning-charge-fill',
                                'score' => $assessment->score_performance,
                                'color' => 'violet',
                            ],
                        ];
                    @endphp

                    @foreach ($komponen as $k)
                        @php
                            $pct = $k['score'];
                            $barCls =
                                $pct >= 80
                                    ? 'bg-emerald-500'
                                    : ($pct >= 60
                                        ? 'bg-blue-500'
                                        : ($pct >= 40
                                            ? 'bg-amber-500'
                                            : 'bg-red-500'));
                        @endphp
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center gap-2 mb-3">
                                <div
                                    class="w-7 h-7 rounded-lg bg-{{ $k['color'] }}-50 flex items-center justify-center shrink-0">
                                    <i class="bi {{ $k['icon'] }} text-{{ $k['color'] }}-500 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-700">{{ $k['label'] }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $k['sub'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-end justify-between mb-2">
                                <p class="text-2xl font-extrabold text-gray-900">{{ $k['score'] }}</p>
                                <p class="text-xs text-gray-400 mb-1">/ 100</p>
                            </div>
                            {{-- Progress bar --}}
                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $barCls }} h-full rounded-full transition-all duration-700"
                                    style="width: {{ $k['score'] }}%"></div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            {{-- ── CATATAN EVALUASI ── --}}
            @if ($assessment->evaluation_notes || $assessment->additional_notes)
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-5">

                    <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                            <i class="bi bi-chat-quote-fill text-amber-500 text-sm"></i>
                        </div>
                        <h2 class="text-sm font-extrabold text-gray-900">Catatan dari Pembimbing</h2>
                    </div>

                    <div class="p-5 grid md:grid-cols-2 gap-5">

                        @if ($assessment->evaluation_notes)
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Catatan Evaluasi
                                </p>
                                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                        {{ $assessment->evaluation_notes }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($assessment->additional_notes)
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Saran & Kritik
                                </p>
                                <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                        {{ $assessment->additional_notes }}
                                    </p>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

            {{-- ── INFO PENILAI & WAKTU ── --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 bg-white rounded-2xl border border-gray-100 px-5 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                        <i class="bi bi-person-badge-fill text-gray-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dinilai oleh</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $assessment->assessor_name }}</p>
                    </div>
                </div>
                <div class="flex-1 bg-white rounded-2xl border border-gray-100 px-5 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                        <i class="bi bi-calendar-check-fill text-gray-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Penilaian</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">
                            {{ $assessment->updated_at->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex-1 bg-white rounded-2xl border border-gray-100 px-5 py-4 flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-xl {{ $passed ? 'bg-emerald-50' : 'bg-red-50' }} flex items-center justify-center shrink-0">
                        <i
                            class="bi {{ $passed ? 'bi-patch-check-fill text-emerald-500' : 'bi-x-octagon-fill text-red-400' }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status Kelulusan</p>
                        <p class="text-sm font-bold {{ $passed ? 'text-emerald-600' : 'text-red-500' }} mt-0.5">
                            {{ $passed ? 'Lulus (≥ 70)' : 'Tidak Lulus (< 70)' }}
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($member && !$member->assessment)
            {{-- ── SUDAH ACCEPTED TAPI BELUM DINILAI ── --}}
            <div class="bg-white rounded-2xl border border-amber-100 p-10 text-center">
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-hourglass-split text-amber-500 text-2xl"></i>
                </div>
                <p class="text-base font-extrabold text-gray-800">Penilaian Belum Tersedia</p>
                <p class="text-sm text-gray-400 mt-2 leading-relaxed max-w-sm mx-auto">
                    Kamu sudah diterima magang, namun pembimbing belum menginput nilai.
                    Nilai akan muncul di sini setelah proses evaluasi selesai.
                </p>
            </div>
        @else
            {{-- ── BELUM ADA LAMARAN ACCEPTED ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-clipboard-x text-gray-400 text-2xl"></i>
                </div>
                <p class="text-base font-extrabold text-gray-700">Belum Ada Data Nilai</p>
                <p class="text-sm text-gray-400 mt-2 leading-relaxed max-w-sm mx-auto">
                    Nilai akan muncul setelah lamaran magangmu diterima dan proses evaluasi selesai dilakukan.
                </p>
                <a href="{{ route('dashboard.index') }}"
                    class="inline-flex items-center gap-2 mt-5 bg-blue-600 text-white text-sm font-bold
                      px-5 py-2.5 rounded-xl hover:bg-blue-700 transition-colors shadow-md shadow-blue-600/25">
                    <i class="bi bi-arrow-left text-xs"></i> Kembali ke Dashboard
                </a>
            </div>
        @endif

    </div>
@endsection
