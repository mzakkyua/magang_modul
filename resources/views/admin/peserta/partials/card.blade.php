@php
    $profile = $member->user->profile ?? null;
    $name = $profile->full_name ?? ($member->user->name ?? '-');
    $email = $member->user->email ?? '-';
    $vacancy = optional($member->application)->vacancy;
    $divisi = $vacancy->division_name ?? '-';
    $title = $vacancy->title ?? '-';
    $status = optional($member->application)->status ?? '-';
    $assessment = $member->assessment;
    $cert = $member->certificate;
    $score = $assessment ? $assessment->final_score : null;
    $passed = $score !== null && $score >= 70;
    $initial = strtoupper(substr($name, 0, 1));

    $statusMap = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-gray-100 text-gray-600'],
        'verified' => ['label' => 'Terverifikasi', 'class' => 'bg-sky-100 text-sky-700'],
        'interview' => ['label' => 'Interview', 'class' => 'bg-violet-100 text-violet-700'],
        'accepted' => ['label' => 'Aktif Magang', 'class' => 'bg-blue-100 text-blue-700'],
        'completed' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700'],
        'resigned' => ['label' => 'Resign', 'class' => 'bg-orange-100 text-orange-700'],
    ];
    $sc = $statusMap[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-600'];
@endphp

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-3 flex items-center gap-3">
    {{-- Avatar --}}
    <div
        class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-100 to-blue-100 border border-indigo-200 flex items-center justify-center text-sm font-black text-indigo-700 shrink-0">
        {{ $initial }}
    </div>

    {{-- Info --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-0.5">
            <p class="text-xs font-bold text-gray-900 truncate">{{ $name }}</p>
            <span
                class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $sc['class'] }} shrink-0">{{ $sc['label'] }}</span>
        </div>
        <p class="text-[11px] text-gray-500 truncate">{{ $divisi }} &middot; {{ $title }}</p>
        <div class="flex items-center gap-3 mt-1">
            @if ($score !== null)
                <span class="text-[10px] font-bold {{ $passed ? 'text-emerald-600' : 'text-red-500' }}">
                    <i class="bi bi-bar-chart-fill"></i> {{ number_format($score, 1) }}
                </span>
            @endif
            @if ($cert)
                <span class="text-[10px] font-bold text-emerald-600"><i class="bi bi-award-fill"></i> Sertif</span>
            @elseif ($status === 'completed' && $assessment)
                <span class="text-[10px] font-bold text-rose-500"><i class="bi bi-exclamation-circle"></i> Belum
                    Sertif</span>
            @endif
        </div>
    </div>

    {{-- Aksi --}}
    <div class="flex items-center gap-1.5 shrink-0">
        <a href="{{ route('admin.peserta.show', $member->id) }}"
            class="w-8 h-8 rounded-lg flex items-center justify-center bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-sm">
            <i class="bi bi-eye-fill text-xs"></i>
        </a>
        @if (in_array($status, ['accepted', 'completed']))
            <a href="{{ route('admin.assessments.create', $member->id) }}"
                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all shadow-sm {{ $assessment ? 'bg-white border border-gray-300 text-gray-600' : 'bg-amber-500 text-white' }}">
                <i class="bi {{ $assessment ? 'bi-pencil-fill' : 'bi-clipboard2-check-fill' }} text-xs"></i>
            </a>
        @endif
    </div>
</div>
