{{-- Logika PHP sama dengan file row.blade.php, tapi layout berupa Card --}}
@php
    $name = $member->user->profile->full_name ?? ($member->user->name ?? '-');
    $email = $member->user->email ?? '-';
    $vacancy = optional($member->application)->vacancy;
    $divisi = $vacancy->division_name ?? '-';
    $title = $vacancy->title ?? '-';
    $status = optional($member->application)->status ?? '-';
    $assessment = $member->assessment;
    $cert = $member->certificate;

    $periode = '-';
    if ($vacancy && $vacancy->start_date && $vacancy->end_date) {
        $periode =
            \Carbon\Carbon::parse($vacancy->start_date)->format('M Y') .
            ' – ' .
            \Carbon\Carbon::parse($vacancy->end_date)->format('M Y');
    }

    $statusConfig = match ($status) {
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-gray-100 text-gray-700', 'icon' => 'bi-clock-history'],
        'verified' => [
            'label' => 'Terverifikasi',
            'class' => 'bg-sky-50 text-sky-700 border border-sky-200',
            'icon' => 'bi-check-circle',
        ],
        'interview' => [
            'label' => 'Interview',
            'class' => 'bg-violet-50 text-violet-700 border border-violet-200',
            'icon' => 'bi-camera-video',
        ],
        'accepted' => [
            'label' => 'Aktif Magang',
            'class' => 'bg-blue-50 text-blue-700 border border-blue-200',
            'icon' => 'bi-person-workspace',
        ],
        'completed' => [
            'label' => 'Selesai',
            'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'icon' => 'bi-patch-check-fill',
        ],
        'rejected' => [
            'label' => 'Ditolak',
            'class' => 'bg-red-50 text-red-700 border border-red-200',
            'icon' => 'bi-x-circle',
        ],
        'resigned' => [
            'label' => 'Resign',
            'class' => 'bg-orange-50 text-orange-700 border border-orange-200',
            'icon' => 'bi-box-arrow-right',
        ],
        default => [
            'label' => ucfirst($status),
            'class' => 'bg-gray-100 text-gray-600',
            'icon' => 'bi-question-circle',
        ],
    };

    $initial = strtoupper(substr($name, 0, 1));
    $score = $assessment ? $assessment->final_score : 0;
    $passed = $score >= 70;
@endphp

<div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col relative overflow-hidden">
    <div class="flex items-start gap-3 mb-3">
        <div
            class="w-10 h-10 rounded-xl bg-linear-to-br from-indigo-100 to-blue-100 flex items-center justify-center border border-indigo-200 shrink-0 font-black text-indigo-700 shadow-sm">
            {{ $initial }}
        </div>
        <div class="min-w-0 flex-1">
            <h4 class="text-sm font-bold text-gray-900 leading-tight truncate">{{ $name }}</h4>
            <p class="text-xs font-medium text-gray-500 truncate">{{ $email }}</p>
        </div>
    </div>

    <div class="mb-3">
        <p class="text-xs font-bold text-gray-800 line-clamp-2 mb-1 leading-snug">{{ $title }}</p>
        <p class="text-[11px] font-semibold text-gray-500">{{ $divisi }} • {{ $periode }}</p>
    </div>

    <div class="flex flex-wrap gap-2 mb-4">
        <span
            class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded {{ $statusConfig['class'] }}">
            <i class="bi {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['label'] }}
        </span>

        @if ($assessment)
            <span
                class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded {{ $passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                <i class="bi bi-bar-chart-fill"></i> Nilai: {{ number_format($score, 1) }}
            </span>
        @endif

        @if ($cert)
            <span
                class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                <i class="bi bi-award-fill"></i> Sertif OK
            </span>
        @endif
    </div>

    <div class="mt-auto flex items-center gap-2 border-t border-gray-100 pt-3">
        @if ($status === 'accepted' || $status === 'completed')
            <a href="{{ route('admin.assessments.create', $member->id) }}"
                class="flex-1 text-center py-2 rounded-lg text-xs font-bold transition-all {{ $assessment ? 'bg-white border border-gray-300 text-gray-700' : 'bg-indigo-600 text-white' }}">
                {{ $assessment ? 'Edit Nilai' : 'Input Nilai' }}
            </a>
        @endif

        @if ($status === 'completed' && $assessment)
            <a href="{{ route('admin.certificates.create') }}"
                class="flex-1 text-center py-2 rounded-lg text-xs font-bold transition-all {{ $cert ? 'bg-white border border-gray-300 text-gray-700' : 'bg-blue-600 text-white' }}">
                {{ $cert ? 'Ubah Sertifikat' : 'Upload Sertifikat' }}
            </a>
        @endif
    </div>
</div>
