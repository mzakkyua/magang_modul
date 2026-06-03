@extends('layouts.admin')

@php
    $profile = $member->user->profile ?? null;
    $name = $profile->full_name ?? ($member->user->name ?? '-');
    $email = $member->user->email ?? '-';
    $vacancy = optional($member->application)->vacancy;
    $application = $member->application;
    $divisi = $vacancy->division_name ?? '-';
    $title = $vacancy->title ?? '-';
    $status = optional($application)->status ?? '-';
    $assessment = $member->assessment;
    $cert = $member->certificate;
    $score = $assessment ? $assessment->final_score : null;
    $passed = $score !== null && $score >= 70;
    $initial = strtoupper(substr($name, 0, 1));

    $leaderProfile = optional(optional($application)->leader)->profile ?? null;
    $leaderName = $leaderProfile->full_name ?? (optional(optional($application)->leader)->name ?? null);

    $periode = '-';
    if ($vacancy && $vacancy->start_date && $vacancy->end_date) {
        $periode =
            \Carbon\Carbon::parse($vacancy->start_date)->format('d M Y') .
            ' – ' .
            \Carbon\Carbon::parse($vacancy->end_date)->format('d M Y');
    }

    $statusMap = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-gray-100 text-gray-700', 'dot' => 'bg-gray-400'],
        'verified' => ['label' => 'Terverifikasi', 'class' => 'bg-sky-100 text-sky-700', 'dot' => 'bg-sky-500'],
        'interview' => ['label' => 'Interview', 'class' => 'bg-violet-100 text-violet-700', 'dot' => 'bg-violet-500'],
        'accepted' => ['label' => 'Aktif Magang', 'class' => 'bg-blue-100 text-blue-700', 'dot' => 'bg-blue-500'],
        'completed' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700', 'dot' => 'bg-emerald-500'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700', 'dot' => 'bg-red-500'],
        'resigned' => [
            'label' => 'Mengundurkan Diri',
            'class' => 'bg-orange-100 text-orange-700',
            'dot' => 'bg-orange-400',
        ],
    ];
    $sc = $statusMap[$status] ?? [
        'label' => ucfirst($status),
        'class' => 'bg-gray-100 text-gray-600',
        'dot' => 'bg-gray-400',
    ];
@endphp

@section('title', 'Detail Peserta – ' . $name)

@section('content')

    {{-- ============================================================ --}}
    {{-- BREADCRUMB + BACK                                             --}}
    {{-- ============================================================ --}}
    <div class="flex items-center gap-2 mb-5 text-xs text-gray-500">
        <a href="{{ route('admin.peserta.index') }}"
            class="hover:text-indigo-600 font-semibold transition-colors flex items-center gap-1">
            <i class="bi bi-arrow-left"></i> Rekap Peserta
        </a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-900 font-bold truncate">{{ $name }}</span>
    </div>

    {{-- ============================================================ --}}
    {{-- HERO CARD — identitas utama peserta                           --}}
    {{-- ============================================================ --}}
    <div
        class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center">
        {{-- Avatar --}}
        <div
            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-200 to-blue-200 border-2 border-indigo-300 flex items-center justify-center text-2xl font-black text-indigo-700 shadow-sm shrink-0">
            {{ $initial }}
        </div>

        {{-- Info utama --}}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <h1 class="text-lg font-extrabold text-gray-900 leading-tight">{{ $name }}</h1>
                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full {{ $sc['class'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }} inline-block"></span>
                    {{ $sc['label'] }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mb-2">{{ $email }}</p>
            <div class="flex flex-wrap gap-3 text-xs">
                <span class="flex items-center gap-1 text-gray-600">
                    <i class="bi bi-building text-indigo-400"></i>
                    <span class="font-semibold">{{ $profile->institution_name ?? '-' }}</span>
                </span>
                <span class="flex items-center gap-1 text-gray-600">
                    <i class="bi bi-mortarboard text-indigo-400"></i>
                    <span>{{ $profile->education_level ?? '-' }} – {{ $profile->major ?? '-' }}</span>
                </span>
                @if ($profile && $profile->nim_nisn)
                    <span class="flex items-center gap-1 text-gray-600">
                        <i class="bi bi-card-text text-indigo-400"></i>
                        <span>{{ $profile->nim_nisn }}</span>
                    </span>
                @endif
                @if ($profile && $profile->phone_number)
                    <span class="flex items-center gap-1 text-gray-600">
                        <i class="bi bi-telephone text-indigo-400"></i>
                        <span>{{ $profile->phone_number }}</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- Aksi utama --}}
        <div class="flex flex-wrap gap-2 shrink-0">
            @if (in_array($status, ['accepted', 'completed']))
                <a href="{{ route('admin.assessments.create', $member->id) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl shadow-sm transition-all {{ $assessment ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-amber-500 text-white hover:bg-amber-600' }}">
                    <i class="bi {{ $assessment ? 'bi-pencil-fill' : 'bi-clipboard2-check-fill' }}"></i>
                    {{ $assessment ? 'Edit Nilai' : 'Input Nilai' }}
                </a>
            @endif
            @if ($status === 'completed' && $assessment)
                <a href="{{ route('admin.certificates.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl shadow-sm transition-all {{ $cert ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                    <i class="bi {{ $cert ? 'bi-arrow-repeat' : 'bi-cloud-arrow-up-fill' }}"></i>
                    {{ $cert ? 'Update Sertifikat' : 'Upload Sertifikat' }}
                </a>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- GRID 2 KOLOM — info magang kiri, nilai+sertif kanan           --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

        {{-- ── Kolom Kiri (span 2): Info Magang & Riset ───────────── --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Info Penempatan --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="bi bi-briefcase-fill text-indigo-400"></i> Informasi Penempatan
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Posisi / Lowongan</p>
                        <p class="text-sm font-bold text-gray-800">{{ $title }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Divisi</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $divisi }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Periode Magang</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $periode }}</p>
                    </div>
                    @if ($leaderName)
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Ketua Kelompok
                            </p>
                            <p class="text-sm font-semibold text-gray-800">{{ $leaderName }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Riset --}}
            @if ($application && ($application->research_title || $application->research_abstract))
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="bi bi-journal-text text-indigo-400"></i> Penelitian / Riset
                    </h2>
                    @if ($application->research_title)
                        <div class="mb-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Judul</p>
                            <p class="text-sm font-bold text-gray-800">{{ $application->research_title }}</p>
                        </div>
                    @endif
                    @if ($application->research_abstract)
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Abstrak</p>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $application->research_abstract }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Kontak & Alamat --}}
            @if ($profile && $profile->address)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-indigo-400"></i> Alamat
                    </h2>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $profile->address }}</p>
                </div>
            @endif

            {{-- Feedback Admin --}}
            @if ($application && $application->admin_feedback)
                <div class="bg-amber-50 rounded-2xl border border-amber-200 shadow-sm p-5">
                    <h2
                        class="text-xs font-extrabold text-amber-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class="bi bi-chat-square-text-fill"></i> Catatan Admin
                    </h2>
                    <p class="text-sm text-amber-800 leading-relaxed">{{ $application->admin_feedback }}</p>
                </div>
            @endif

        </div>

        {{-- ── Kolom Kanan: Nilai & Sertifikat ─────────────────────── --}}
        <div class="flex flex-col gap-4">

            {{-- Nilai / Assessment --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="bi bi-bar-chart-fill text-indigo-400"></i> Penilaian
                </h2>

                @if ($assessment)
                    <div class="text-center mb-4">
                        <div
                            class="inline-flex flex-col items-center justify-center w-24 h-24 rounded-full border-4 {{ $passed ? 'border-emerald-400 bg-emerald-50' : 'border-red-400 bg-red-50' }} mb-2">
                            <span class="text-3xl font-black {{ $passed ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ number_format($score, 0) }}
                            </span>
                            <span
                                class="text-[10px] font-bold {{ $passed ? 'text-emerald-500' : 'text-red-500' }} uppercase">
                                {{ $passed ? 'Lulus' : 'Remedial' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">Nilai Final (dari 100)</p>
                    </div>

                    {{-- Komponen Nilai --}}
                    @php
                        $components = [
                            'Kehadiran' => $assessment->attendance_score ?? null,
                            'Kinerja' => $assessment->performance_score ?? null,
                            'Sikap' => $assessment->attitude_score ?? null,
                            'Laporan' => $assessment->report_score ?? null,
                            'Presentasi' => $assessment->presentation_score ?? null,
                        ];
                        $components = array_filter($components, fn($v) => $v !== null);
                    @endphp

                    @if (!empty($components))
                        <div class="space-y-2">
                            @foreach ($components as $label => $val)
                                @php $pct = min(100, max(0, $val)); @endphp
                                <div>
                                    <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-0.5">
                                        <span>{{ $label }}</span>
                                        <span>{{ number_format($val, 1) }}</span>
                                    </div>
                                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all {{ $val >= 70 ? 'bg-emerald-400' : 'bg-red-400' }}"
                                            style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($assessment->notes)
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Catatan Penilai</p>
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $assessment->notes }}</p>
                        </div>
                    @endif
                @elseif (in_array($status, ['accepted']))
                    <div class="text-center py-6">
                        <div
                            class="w-12 h-12 rounded-full bg-amber-50 border-2 border-amber-200 flex items-center justify-center mx-auto mb-2">
                            <i class="bi bi-hourglass-split text-amber-500"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-700 mb-0.5">Belum Dinilai</p>
                        <p class="text-[11px] text-gray-400 mb-3">Peserta masih aktif magang</p>
                        <a href="{{ route('admin.assessments.create', $member->id) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 transition-all">
                            <i class="bi bi-clipboard2-check-fill"></i> Input Nilai
                        </a>
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="bi bi-dash-circle text-gray-200 text-3xl"></i>
                        <p class="text-xs text-gray-400 mt-2">Belum ada data penilaian</p>
                    </div>
                @endif
            </div>

            {{-- Sertifikat --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="bi bi-award-fill text-indigo-400"></i> Sertifikat
                </h2>

                @if ($cert)
                    <div class="text-center py-2">
                        <div
                            class="w-14 h-14 rounded-2xl bg-emerald-50 border-2 border-emerald-200 flex items-center justify-center mx-auto mb-3">
                            <i class="bi bi-patch-check-fill text-emerald-500 text-2xl"></i>
                        </div>
                        <p class="text-sm font-bold text-emerald-700 mb-0.5">Sertifikat Terbit</p>
                        <p class="text-[11px] text-gray-400 mb-3">
                            {{ $cert->uploaded_at ? \Carbon\Carbon::parse($cert->uploaded_at)->format('d M Y') : '-' }}
                        </p>
                        <a href="{{ route('admin.certificates.create') }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition-all">
                            <i class="bi bi-arrow-repeat"></i> Update
                        </a>
                    </div>
                @elseif ($status === 'completed' && $assessment)
                    <div class="text-center py-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-rose-50 border-2 border-rose-200 flex items-center justify-center mx-auto mb-3">
                            <i class="bi bi-exclamation-circle-fill text-rose-500 text-2xl"></i>
                        </div>
                        <p class="text-sm font-bold text-rose-700 mb-0.5">Belum Ada Sertifikat</p>
                        <p class="text-[11px] text-gray-400 mb-3">Peserta sudah selesai & dinilai</p>
                        <a href="{{ route('admin.certificates.create') }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-all">
                            <i class="bi bi-cloud-arrow-up-fill"></i> Upload Sekarang
                        </a>
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="bi bi-award text-gray-200 text-3xl"></i>
                        <p class="text-xs text-gray-400 mt-2">Belum dapat diterbitkan</p>
                        <p class="text-[11px] text-gray-300 mt-0.5">Status harus Selesai & sudah dinilai</p>
                    </div>
                @endif
            </div>

            {{-- Dokumen Pendaftaran --}}
            @if ($profile && ($profile->cv_file_path || $profile->proposal_file_path))
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <h2
                        class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="bi bi-file-earmark-text-fill text-indigo-400"></i> Dokumen Pendaftaran
                    </h2>
                    <div class="flex flex-col gap-2">
                        @if ($profile->cv_file_path)
                            <a href="{{ route('admin.applications.files.cv', $member->user_id) }}" target="_blank"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill text-red-500 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-800 group-hover:text-indigo-700">Curriculum Vitae
                                    </p>
                                    <p class="text-[10px] text-gray-400">Lihat dokumen CV</p>
                                </div>
                                <i
                                    class="bi bi-box-arrow-up-right text-[10px] text-gray-400 group-hover:text-indigo-500"></i>
                            </a>
                        @endif
                        @if ($profile->proposal_file_path)
                            <a href="{{ route('admin.applications.files.proposal', $member->user_id) }}" target="_blank"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                    <i class="bi bi-file-earmark-word-fill text-blue-500 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-800 group-hover:text-indigo-700">Proposal
                                        Penelitian</p>
                                    <p class="text-[10px] text-gray-400">Lihat dokumen proposal</p>
                                </div>
                                <i
                                    class="bi bi-box-arrow-up-right text-[10px] text-gray-400 group-hover:text-indigo-500"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>

@endsection
