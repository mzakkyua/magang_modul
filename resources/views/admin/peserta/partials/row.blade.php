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
        'accepted' => ['label' => 'Aktif', 'class' => 'bg-blue-100 text-blue-700'],
        'completed' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700'],
        'resigned' => ['label' => 'Resign', 'class' => 'bg-orange-100 text-orange-700'],
    ];
    $sc = $statusMap[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-600'];
@endphp

<tr class="hover:bg-indigo-50/40 transition-colors group border-b border-gray-100 last:border-0">

    {{-- Avatar + Nama --}}
    <td class="px-4 py-2.5 align-middle">
        <div class="flex items-center gap-2.5">
            <div
                class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-100 to-blue-100 border border-indigo-200 flex items-center justify-center text-[11px] font-black text-indigo-700 shrink-0">
                {{ $initial }}
            </div>
            <div class="min-w-0">
                <p
                    class="text-xs font-bold text-gray-900 truncate group-hover:text-indigo-600 transition-colors leading-tight max-w-[140px]">
                    {{ $name }}</p>
                <p class="text-[10px] text-gray-400 truncate max-w-[140px]">{{ $email }}</p>
            </div>
        </div>
    </td>

    {{-- Divisi --}}
    <td class="px-4 py-2.5 align-middle hidden md:table-cell">
        <p class="text-xs font-semibold text-gray-700 truncate max-w-[120px]" title="{{ $title }}">
            {{ $title }}</p>
        <p class="text-[10px] text-gray-400 truncate">{{ $divisi }}</p>
    </td>

    {{-- Status --}}
    <td class="px-4 py-2.5 align-middle text-center">
        <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md {{ $sc['class'] }}">
            {{ $sc['label'] }}
        </span>
    </td>

    {{-- Nilai --}}
    <td class="px-4 py-2.5 align-middle text-center hidden sm:table-cell">
        @if ($score !== null)
            <span class="text-xs font-black {{ $passed ? 'text-emerald-600' : 'text-red-500' }}">
                {{ number_format($score, 1) }}
            </span>
        @elseif (in_array($status, ['accepted']))
            <span class="text-[10px] text-amber-500 font-semibold">Pending</span>
        @else
            <span class="text-gray-300 text-xs">—</span>
        @endif
    </td>

    {{-- Sertifikat --}}
    <td class="px-4 py-2.5 align-middle text-center hidden sm:table-cell">
        @if ($cert)
            <i class="bi bi-patch-check-fill text-emerald-500 text-sm" title="Sertifikat Terbit"></i>
        @elseif ($status === 'completed' && $assessment)
            <i class="bi bi-exclamation-circle-fill text-rose-400 text-sm" title="Belum Ada Sertifikat"></i>
        @else
            <span class="text-gray-300 text-xs">—</span>
        @endif
    </td>

    {{-- Aksi --}}
    <td class="px-4 py-2.5 align-middle text-center">
        <div class="flex items-center justify-center gap-1.5">
            {{-- Tombol Detail --}}
            <a href="{{ route('admin.peserta.show', $member->id) }}" title="Lihat Detail"
                class="w-7 h-7 rounded-md flex items-center justify-center bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-sm">
                <i class="bi bi-eye-fill text-[11px]"></i>
            </a>

            {{-- Nilai --}}
            @if (in_array($status, ['accepted', 'completed']))
                <a href="{{ route('admin.assessments.create', $member->id) }}"
                    title="{{ $assessment ? 'Edit Nilai' : 'Input Nilai' }}"
                    class="w-7 h-7 rounded-md flex items-center justify-center transition-all shadow-sm {{ $assessment ? 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' : 'bg-amber-500 text-white hover:bg-amber-600' }}">
                    <i class="bi {{ $assessment ? 'bi-pencil-fill' : 'bi-clipboard2-check-fill' }} text-[11px]"></i>
                </a>
            @endif

            {{-- Sertifikat --}}
            @if ($status === 'completed' && $assessment)
                <a href="{{ route('admin.certificates.create') }}"
                    title="{{ $cert ? 'Update Sertifikat' : 'Upload Sertifikat' }}"
                    class="w-7 h-7 rounded-md flex items-center justify-center transition-all shadow-sm {{ $cert ? 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' : 'bg-blue-500 text-white hover:bg-blue-600' }}">
                    <i class="bi {{ $cert ? 'bi-arrow-repeat' : 'bi-cloud-arrow-up-fill' }} text-[11px]"></i>
                </a>
            @endif
        </div>
    </td>
</tr>
