@extends('layouts.landing')

@section('title', 'Nilai Magang Saya - SINAKERTRANS')

@section('content')
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">

            {{-- ── PAGE HEADER ── --}}
            <div class="mb-8">
                <p class="text-blue-600 font-bold uppercase tracking-widest text-xs mb-2 flex items-center gap-2">
                    <i class="bi bi-bar-chart-fill"></i> Nilai
                </p>
                <h1 class="text-2xl font-extrabold text-gray-900">Nilai Magang Saya</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Hasil evaluasi resmi dari pembimbing lapangan Disnakertrans — setiap periode magang ditampilkan
                    terpisah.
                </p>
            </div>

            {{-- ── LIST PER PERIODE ── --}}
            @forelse ($memberRecords as $member)
                @php
                    $assessment = $member->assessment;
                    $vacancy = $member->application->vacancy;
                    $app = $member->application;

                    $isTypeMagang = $vacancy->type === 'magang';
                    $accentBg = $isTypeMagang ? 'from-blue-600 to-blue-700' : 'from-violet-600 to-violet-700';

                    $statusLabel = match ($app->status) {
                        'accepted' => [
                            'label' => 'Sedang Berjalan',
                            'color' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        ],
                        'completed' => ['label' => 'Selesai', 'color' => 'bg-blue-100 text-blue-700 border-blue-200'],
                        default => [
                            'label' => ucfirst($app->status),
                            'color' => 'bg-gray-100 text-gray-600 border-gray-200',
                        ],
                    };
                @endphp

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">

                    {{-- ── HEADER PERIODE ── --}}
                    <div class="bg-gradient-to-r {{ $accentBg }} px-6 py-4 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10"
                            style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px;">
                        </div>
                        <div class="relative z-10 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest mb-0.5">
                                    {{ ucfirst($vacancy->type) }} • Periode {{ $loop->iteration }}
                                </p>
                                <h2 class="text-sm font-extrabold text-white leading-tight">{{ $vacancy->title }}</h2>
                                <p class="text-xs text-white/75 mt-0.5">{{ $vacancy->division_name }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span
                                    class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $statusLabel['color'] }} bg-white/90">
                                    {{ $statusLabel['label'] }}
                                </span>
                                <p class="text-white/60 text-[11px] mt-1.5">
                                    {{ \Carbon\Carbon::parse($vacancy->start_date)->format('d M Y') }}
                                    –
                                    {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ── BODY ── --}}
                    <div class="p-5">
                        @if ($assessment)
                            @php $passed = $assessment->isPassed(); @endphp

                            {{-- BANNER HASIL --}}
                            <div class="rounded-2xl overflow-hidden mb-5 p-5 relative"
                                style="background: linear-gradient(135deg, {{ $passed ? '#1e3a5f' : '#4c1130' }} 0%, {{ $passed ? '#2563eb' : '#dc2626' }} 100%);">
                                <div class="absolute top-0 right-0 w-40 h-40 pointer-events-none"
                                    style="background:radial-gradient(circle,rgba(255,255,255,0.07) 0%,transparent 70%);transform:translate(20%,-30%);">
                                </div>
                                <div
                                    class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold mb-3
                                            {{ $passed ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-red-500/20 text-red-300 border border-red-400/30' }}">
                                            <i
                                                class="bi {{ $passed ? 'bi-patch-check-fill' : 'bi-x-octagon-fill' }} text-[11px]"></i>
                                            {{ $passed ? 'LULUS' : 'TIDAK LULUS' }}
                                        </span>
                                        <p class="text-white/40 text-[10px] uppercase tracking-widest font-bold">Penilai</p>
                                        <p class="text-white font-bold text-sm mt-0.5">{{ $assessment->assessor_name }}</p>
                                        <p class="text-white/50 text-xs mt-1">
                                            <i class="bi bi-calendar3 mr-1"></i>
                                            {{ $assessment->updated_at->translatedFormat('d F Y') }}
                                        </p>
                                    </div>
                                    {{-- Skor besar --}}
                                    <div
                                        class="shrink-0 text-center bg-white/10 border border-white/20 rounded-2xl px-6 py-4">
                                        <p class="text-white/50 text-[10px] uppercase tracking-widest font-bold mb-1">Skor
                                            Akhir</p>
                                        <p class="text-5xl font-extrabold text-white leading-none">
                                            {{ number_format($assessment->final_score, 0) }}
                                        </p>
                                        <p class="text-white/40 text-xs mt-1">dari 100</p>
                                    </div>
                                </div>
                            </div>

                            {{-- RINCIAN 3 KOMPONEN --}}
                            <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 mb-4">
                                <p
                                    class="text-xs font-extrabold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <i class="bi bi-bar-chart-fill text-blue-400"></i>
                                    Rincian Nilai Per Komponen
                                </p>
                                <div class="grid md:grid-cols-3 gap-4">
                                    @foreach ([['label' => 'Perilaku', 'sub' => 'Etika, sopan santun, attitude', 'icon' => 'bi-person-check-fill', 'score' => $assessment->score_behavior, 'color' => 'blue'], ['label' => 'Kedisiplinan', 'sub' => 'Ketepatan waktu, kehadiran', 'icon' => 'bi-clock-fill', 'score' => $assessment->score_discipline, 'color' => 'indigo'], ['label' => 'Kinerja', 'sub' => 'Hasil kerja, inisiatif, skill', 'icon' => 'bi-lightning-charge-fill', 'score' => $assessment->score_performance, 'color' => 'violet']] as $k)
                                        @php
                                            $barCls =
                                                $k['score'] >= 80
                                                    ? 'bg-emerald-500'
                                                    : ($k['score'] >= 60
                                                        ? 'bg-blue-500'
                                                        : ($k['score'] >= 40
                                                            ? 'bg-amber-500'
                                                            : 'bg-red-500'));
                                        @endphp
                                        <div class="bg-white rounded-xl p-4 border border-gray-100">
                                            <div class="flex items-center gap-2 mb-3">
                                                <div
                                                    class="w-7 h-7 rounded-lg bg-{{ $k['color'] }}-50 flex items-center justify-center shrink-0">
                                                    <i
                                                        class="bi {{ $k['icon'] }} text-{{ $k['color'] }}-500 text-xs"></i>
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
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                <div class="{{ $barCls }} h-full rounded-full transition-all duration-700"
                                                    style="width: {{ $k['score'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- CATATAN EVALUASI --}}
                            @if ($assessment->evaluation_notes || $assessment->additional_notes)
                                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                                    <div class="px-5 py-3 border-b border-gray-50 flex items-center gap-2">
                                        <i class="bi bi-chat-quote-fill text-amber-500 text-sm"></i>
                                        <p class="text-xs font-extrabold text-gray-700 uppercase tracking-wider">Catatan
                                            dari Pembimbing</p>
                                    </div>
                                    <div class="p-4 grid md:grid-cols-2 gap-4">
                                        @if ($assessment->evaluation_notes)
                                            <div>
                                                <p
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    Catatan Evaluasi</p>
                                                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                                        {{ $assessment->evaluation_notes }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($assessment->additional_notes)
                                            <div>
                                                <p
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    Saran & Kritik</p>
                                                <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                                        {{ $assessment->additional_notes }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- BELUM DINILAI untuk periode ini --}}
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-3">
                                    <i class="bi bi-hourglass-split text-amber-500 text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-600">Penilaian Belum Tersedia</p>
                                <p class="text-xs text-gray-400 mt-1 max-w-xs leading-relaxed">
                                    Kamu sudah diterima magang, namun pembimbing belum menginput nilai untuk periode ini.
                                    Nilai akan muncul setelah proses evaluasi selesai.
                                </p>
                            </div>
                        @endif
                    </div>

                </div>
            @empty

                {{-- ── TIDAK ADA LAMARAN ACCEPTED SAMA SEKALI ── --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-20 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <i class="bi bi-clipboard-x text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-gray-600 font-bold text-lg mb-1">Belum Ada Data Nilai</h3>
                    <p class="text-gray-400 text-sm max-w-sm mx-auto leading-relaxed">
                        Nilai akan muncul setelah lamaran magangmu diterima dan proses evaluasi selesai dilakukan.
                    </p>
                    <a href="{{ route('dashboard.index') }}"
                        class="inline-flex items-center gap-2 mt-6 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-md shadow-blue-600/20">
                        <i class="bi bi-house text-xs"></i> Kembali ke Dashboard
                    </a>
                </div>

            @endforelse

        </div>
    </div>
@endsection
