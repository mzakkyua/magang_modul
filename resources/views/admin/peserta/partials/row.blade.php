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

<tr class="hover:bg-indigo-50/30 transition-colors group">
    {{-- Peserta --}}
    <td class="px-6 py-4 align-middle">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-linear-to-br from-indigo-100 to-blue-100 flex items-center justify-center border border-indigo-200 shrink-0 font-black text-indigo-700 shadow-sm">
                {{ $initial }}
            </div>
            <div class="min-w-0">
                <p
                    class="text-sm font-bold text-gray-900 leading-tight truncate group-hover:text-indigo-600 transition-colors">
                    {{ $name }}</p>
                <p class="text-xs font-medium text-gray-500 mt-0.5 truncate">{{ $email }}</p>
            </div>
        </div>
    </td>

    {{-- Divisi & Periode --}}
    <td class="px-6 py-4 align-middle">
        <p class="text-sm font-bold text-gray-800 leading-tight truncate max-w-50" title="{{ $title }}">
            {{ $title }}</p>
        <div class="flex items-center gap-2 mt-1.5">
            <span
                class="text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $divisi }}</span>
            <span class="text-[10px] font-semibold text-gray-400">{{ $periode }}</span>
        </div>
    </td>

    {{-- Status --}}
    <td class="px-6 py-4 text-center align-middle">
        <span
            class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full {{ $statusConfig['class'] }}">
            <i class="bi {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['label'] }}
        </span>
    </td>

    {{-- Nilai --}}
    <td class="px-6 py-4 text-center align-middle">
        @if ($assessment)
            <div class="flex flex-col items-center">
                <span
                    class="text-xl font-black {{ $passed ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($score, 1) }}</span>
                <span
                    class="text-[9px] font-bold uppercase tracking-wider {{ $passed ? 'text-emerald-500' : 'text-red-500' }}">{{ $passed ? 'Lulus' : 'Remedial' }}</span>
            </div>
        @elseif (in_array($status, ['accepted']))
            <span
                class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded uppercase tracking-wide">
                <i class="bi bi-hourglass-split"></i> Pending
            </span>
        @else
            <span class="text-gray-300 font-bold">—</span>
        @endif
    </td>

    {{-- Sertifikat --}}
    <td class="px-6 py-4 text-center align-middle">
        @if ($cert)
            <div class="flex flex-col items-center gap-1">
                <span
                    class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded uppercase tracking-wide">
                    <i class="bi bi-check-lg"></i> Terbit
                </span>
                <span
                    class="text-[9px] font-semibold text-gray-400">{{ \Carbon\Carbon::parse($cert->uploaded_at)->format('d M Y') }}</span>
            </div>
        @elseif ($status === 'completed' && $assessment)
            <span
                class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded uppercase tracking-wide">
                <i class="bi bi-exclamation-circle"></i> Kosong
            </span>
        @else
            <span class="text-gray-300 font-bold">—</span>
        @endif
    </td>

    {{-- Aksi --}}
    <td class="px-6 py-4 text-center align-middle">
        <div class="flex items-center justify-center gap-2">
            @if ($status === 'accepted' || $status === 'completed')
                <a href="{{ route('admin.assessments.create', $member->id) }}"
                    title="{{ $assessment ? 'Edit Nilai' : 'Input Nilai' }}"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all shadow-sm {{ $assessment ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-200' }}">
                    <i class="bi {{ $assessment ? 'bi-pencil-square' : 'bi-clipboard2-check-fill' }} text-sm"></i>
                </a>
            @endif

            @if ($status === 'completed' && $assessment)
                <a href="{{ route('admin.certificates.create') }}"
                    title="{{ $cert ? 'Update Sertifikat' : 'Upload Sertifikat' }}"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all shadow-sm {{ $cert ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-200' }}">
                    <i class="bi {{ $cert ? 'bi-arrow-repeat' : 'bi-cloud-arrow-up-fill' }} text-sm"></i>
                </a>
            @endif
        </div>
    </td>
</tr>
