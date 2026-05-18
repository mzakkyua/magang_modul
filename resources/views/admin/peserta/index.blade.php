@extends('layouts.admin')

@section('title', 'Rekap Peserta Magang')

@section('content')

    {{-- ============================================================ --}}
    {{-- HEADER & SUMMARY STATS                                       --}}
    {{-- ============================================================ --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-extrabold text-gray-900">Rekap Peserta Magang</h1>
                <p class="text-xs text-gray-400 mt-0.5">Semua data peserta, nilai, dan sertifikat dalam satu halaman</p>
            </div>
            <a href="{{ route('admin.certificates.create') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
                       text-white text-sm font-bold px-4 py-2.5 rounded-xl
                       shadow-md shadow-blue-600/25 transition-all hover:-translate-y-0.5">
                <i class="bi bi-cloud-arrow-up-fill text-xs"></i>
                Upload Sertifikat
            </a>
        </div>

        {{-- Summary Stats Bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">

            {{-- Total --}}
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                    <i class="bi bi-people-fill text-gray-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-medium">Total Peserta</p>
                    <p class="text-base font-extrabold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>

            {{-- Aktif --}}
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                    <i class="bi bi-person-check-fill text-blue-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-medium">Sedang Magang</p>
                    <p class="text-base font-extrabold text-blue-600">{{ number_format($stats['aktif']) }}</p>
                </div>
            </div>

            {{-- Selesai --}}
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                    <i class="bi bi-patch-check-fill text-emerald-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-medium">Selesai</p>
                    <p class="text-base font-extrabold text-emerald-600">{{ number_format($stats['selesai']) }}</p>
                </div>
            </div>

            {{-- Bersertifikat --}}
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center shrink-0">
                    <i class="bi bi-award-fill text-violet-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-medium">Punya Sertifikat</p>
                    <p class="text-base font-extrabold text-violet-600">{{ number_format($stats['bersertif']) }}</p>
                </div>
            </div>

            {{-- Belum Dinilai --}}
            <div
                class="bg-white rounded-xl border border-gray-100 px-4 py-3 flex items-center gap-3
                        {{ $stats['belum_nilai'] > 0 ? 'border-amber-200 bg-amber-50/50' : '' }}">
                <div
                    class="w-8 h-8 rounded-lg {{ $stats['belum_nilai'] > 0 ? 'bg-amber-100' : 'bg-gray-100' }} flex items-center justify-center shrink-0">
                    <i
                        class="bi bi-exclamation-circle-fill {{ $stats['belum_nilai'] > 0 ? 'text-amber-500' : 'text-gray-400' }} text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-medium">Belum Dinilai</p>
                    <p
                        class="text-base font-extrabold {{ $stats['belum_nilai'] > 0 ? 'text-amber-600' : 'text-gray-500' }}">
                        {{ number_format($stats['belum_nilai']) }}
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FILTER & SEARCH BAR                                          --}}
    {{-- ============================================================ --}}
    <form method="GET" action="{{ route('admin.peserta.index') }}" id="filterForm">
        <div class="bg-white rounded-2xl border border-gray-100 px-5 py-4 mb-5">
            <div class="flex flex-wrap gap-3 items-end">

                {{-- Search --}}
                <div class="flex-1 min-w-48">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                        Cari Peserta
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="bi bi-search text-gray-300 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nama atau email..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                                   outline-none transition-all border-gray-200 bg-white
                                   placeholder:text-gray-300 placeholder:font-normal
                                   hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>

                {{-- Filter: Status Magang --}}
                <div class="min-w-40">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                        Status Magang
                    </label>
                    <select name="status" onchange="document.getElementById('filterForm').submit()"
                        class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                               outline-none appearance-none transition-all border-gray-200 bg-white
                               hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status
                        </option>
                        @foreach ($allStatuses as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter: Sertifikat --}}
                <div class="min-w-40">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                        Sertifikat
                    </label>
                    <select name="sertifikat" onchange="document.getElementById('filterForm').submit()"
                        class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                               outline-none appearance-none transition-all border-gray-200 bg-white
                               hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        <option value="all" {{ request('sertifikat', 'all') === 'all' ? 'selected' : '' }}>Semua
                        </option>
                        <option value="sudah" {{ request('sertifikat') === 'sudah' ? 'selected' : '' }}>✓ Sudah Terbit
                        </option>
                        <option value="belum" {{ request('sertifikat') === 'belum' ? 'selected' : '' }}>✗ Belum Terbit
                        </option>
                    </select>
                </div>

                {{-- Filter: Divisi (superadmin only) --}}
                @if ($divisiList->isNotEmpty())
                    <div class="min-w-40">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                            Divisi
                        </label>
                        <select name="divisi" onchange="document.getElementById('filterForm').submit()"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                                   outline-none appearance-none transition-all border-gray-200 bg-white
                                   hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            <option value="all" {{ request('divisi', 'all') === 'all' ? 'selected' : '' }}>Semua Divisi
                            </option>
                            @foreach ($divisiList as $div)
                                <option value="{{ $div }}" {{ request('divisi') === $div ? 'selected' : '' }}>
                                    {{ $div }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Search button --}}
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl
                               hover:bg-blue-700 transition-all shadow-md shadow-blue-600/20">
                        <i class="bi bi-search mr-1.5"></i> Cari
                    </button>

                    @if (request()->hasAny(['search', 'status', 'sertifikat', 'divisi']))
                        <a href="{{ route('admin.peserta.index') }}"
                            class="px-4 py-2.5 bg-white border border-gray-200 text-gray-500 text-sm font-medium
                                   rounded-xl hover:bg-gray-50 transition-colors flex items-center gap-1.5">
                            <i class="bi bi-x-circle text-xs"></i> Reset
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </form>

    {{-- ============================================================ --}}
    {{-- TABEL REKAP PESERTA                                          --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Table header bar --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                    <i class="bi bi-table text-blue-500 text-xs"></i>
                </div>
                <span class="text-sm font-extrabold text-gray-900">Daftar Peserta</span>
                <span
                    class="text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100 px-2.5 py-0.5 rounded-full">
                    {{ $members->total() }} peserta
                </span>
            </div>
            <p class="text-xs text-gray-400 hidden sm:block">
                Halaman {{ $members->currentPage() }} dari {{ $members->lastPage() }}
            </p>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Peserta
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Divisi & Periode
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Status Magang
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Nilai
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Sertifikat
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    @forelse($members as $member)
                        @php
                            $name = $member->user->profile->full_name ?? ($member->user->name ?? '-');
                            $email = $member->user->email ?? '-';
                            $vacancy = optional($member->application)->vacancy;
                            $divisi = $vacancy->division_name ?? '-';
                            $title = $vacancy->title ?? '-';
                            $status = optional($member->application)->status ?? '-';
                            $assessment = $member->assessment;
                            $cert = $member->certificate;

                            // Format periode
                            $periode = '-';
                            if ($vacancy && $vacancy->start_date && $vacancy->end_date) {
                                $periode =
                                    \Carbon\Carbon::parse($vacancy->start_date)->format('M Y') .
                                    ' – ' .
                                    \Carbon\Carbon::parse($vacancy->end_date)->format('M Y');
                            }

                            // Status badge config
                            $statusConfig = match ($status) {
                                'pending' => [
                                    'label' => 'Menunggu',
                                    'class' => 'bg-gray-100 text-gray-600 border-gray-200',
                                    'icon' => 'bi-clock',
                                ],
                                'verified' => [
                                    'label' => 'Terverifikasi',
                                    'class' => 'bg-sky-50 text-sky-700 border-sky-200',
                                    'icon' => 'bi-check-circle',
                                ],
                                'interview' => [
                                    'label' => 'Interview',
                                    'class' => 'bg-violet-50 text-violet-700 border-violet-200',
                                    'icon' => 'bi-camera-video',
                                ],
                                'accepted' => [
                                    'label' => 'Sedang Magang',
                                    'class' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'icon' => 'bi-person-workspace',
                                ],
                                'completed' => [
                                    'label' => 'Selesai',
                                    'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'icon' => 'bi-patch-check-fill',
                                ],
                                'rejected' => [
                                    'label' => 'Ditolak',
                                    'class' => 'bg-red-50 text-red-700 border-red-200',
                                    'icon' => 'bi-x-circle',
                                ],
                                'resigned' => [
                                    'label' => 'Mengundurkan Diri',
                                    'class' => 'bg-orange-50 text-orange-700 border-orange-200',
                                    'icon' => 'bi-box-arrow-right',
                                ],
                                default => [
                                    'label' => ucfirst($status),
                                    'class' => 'bg-gray-100 text-gray-600 border-gray-200',
                                    'icon' => 'bi-question-circle',
                                ],
                            };

                            // Avatar initial
                            $initial = strtoupper(substr($name, 0, 1));
                        @endphp

                        <tr class="hover:bg-slate-50/60 transition-colors group">

                            {{-- KOLOM: Peserta --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center
                                                border border-blue-200 shrink-0 font-extrabold text-blue-600 text-sm">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">
                                            {{ $name }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- KOLOM: Divisi & Periode --}}
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-800 leading-tight">{{ $title }}</p>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span
                                        class="text-[11px] font-bold bg-gray-100 text-gray-500 border border-gray-200 px-2 py-0.5 rounded-full">
                                        {{ $divisi }}
                                    </span>
                                    <span class="text-[11px] text-gray-400">{{ $periode }}</span>
                                </div>
                            </td>

                            {{-- KOLOM: Status Magang --}}
                            <td class="px-5 py-4 text-center">
                                <span
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold
                                             border px-2.5 py-1 rounded-full {{ $statusConfig['class'] }}">
                                    <i class="bi {{ $statusConfig['icon'] }} text-[10px]"></i>
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>

                            {{-- KOLOM: Nilai --}}
                            <td class="px-5 py-4 text-center">
                                @if ($assessment)
                                    @php
                                        $score = $assessment->final_score;
                                        $passed = $score >= 70;
                                    @endphp
                                    <div class="inline-flex flex-col items-center gap-0.5">
                                        <span
                                            class="text-lg font-extrabold leading-none
                                                     {{ $passed ? 'text-emerald-600' : 'text-red-500' }}">
                                            {{ number_format($score, 1) }}
                                        </span>
                                        <span
                                            class="text-[10px] font-bold
                                                     {{ $passed ? 'text-emerald-400' : 'text-red-400' }}">
                                            {{ $passed ? 'Lulus' : 'Tidak Lulus' }}
                                        </span>
                                    </div>
                                @elseif (in_array($status, ['accepted']))
                                    <span
                                        class="inline-flex items-center gap-1 text-[11px] font-bold
                                                 text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                                        <i class="bi bi-hourglass-split text-[10px]"></i>
                                        Belum Dinilai
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- KOLOM: Sertifikat --}}
                            <td class="px-5 py-4 text-center">
                                @if ($cert)
                                    <div class="inline-flex flex-col items-center gap-1">
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] font-bold
                                                     text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                            <i class="bi bi-award-fill text-[10px]"></i>
                                            Sudah Terbit
                                        </span>
                                        @if ($cert->replaced_at)
                                            <span class="text-[10px] text-amber-500 font-medium">
                                                (pernah diganti)
                                            </span>
                                        @endif
                                        <span class="text-[10px] text-gray-400 leading-none">
                                            {{ \Carbon\Carbon::parse($cert->uploaded_at)->format('d M Y') }}
                                        </span>
                                    </div>
                                @elseif ($status === 'completed' && $assessment)
                                    <span
                                        class="inline-flex items-center gap-1 text-[11px] font-bold
                                                 text-orange-600 bg-orange-50 border border-orange-200 px-2 py-0.5 rounded-full">
                                        <i class="bi bi-exclamation-circle text-[10px]"></i>
                                        Belum Terbit
                                    </span>
                                @elseif ($status === 'completed' && !$assessment)
                                    <span
                                        class="inline-flex items-center gap-1 text-[11px] font-bold
                                                 text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full">
                                        <i class="bi bi-x-circle text-[10px]"></i>
                                        Nilai Dulu
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- KOLOM: Aksi --}}
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">

                                    {{-- Tombol: Input Nilai (jika accepted) --}}
                                    @if ($status === 'accepted')
                                        <a href="{{ route('admin.assessments.create', $member->id) }}"
                                            title="{{ $assessment ? 'Edit Nilai' : 'Input Nilai' }}"
                                            class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1.5 rounded-lg
                                                   transition-all {{ $assessment
                                                       ? 'bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200'
                                                       : 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' }}">
                                            <i class="bi bi-pencil-square text-[10px]"></i>
                                            {{ $assessment ? 'Edit Nilai' : 'Nilai' }}
                                        </a>
                                    @endif

                                    {{-- Tombol: Upload Sertifikat (jika completed, belum ada cert) --}}
                                    @if ($status === 'completed' && !$cert && $assessment)
                                        <a href="{{ route('admin.certificates.create') }}" title="Upload Sertifikat"
                                            class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1.5 rounded-lg
                                                   bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-all">
                                            <i class="bi bi-cloud-arrow-up text-[10px]"></i>
                                            Upload
                                        </a>
                                    @endif

                                    {{-- Tombol: Ganti Sertifikat (jika sudah ada cert) --}}
                                    @if ($cert)
                                        <a href="{{ route('admin.certificates.create') }}" title="Ganti Sertifikat"
                                            class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1.5 rounded-lg
                                                   bg-violet-50 text-violet-700 hover:bg-violet-100 border border-violet-200 transition-all">
                                            <i class="bi bi-arrow-repeat text-[10px]"></i>
                                            Ganti
                                        </a>
                                    @endif

                                    {{-- Tombol: Detail Nilai (jika sudah dinilai) --}}
                                    @if ($assessment)
                                        <a href="{{ route('admin.assessments.create', $member->id) }}"
                                            title="Lihat Detail Nilai"
                                            class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1.5 rounded-lg
                                                   bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-all">
                                            <i class="bi bi-bar-chart-fill text-[10px]"></i>
                                            Nilai
                                        </a>
                                    @endif

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center">
                                        <i class="bi bi-people text-gray-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-500">Tidak ada peserta ditemukan</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Coba ubah filter atau kata kunci pencarian
                                        </p>
                                    </div>
                                    <a href="{{ route('admin.peserta.index') }}"
                                        class="text-xs text-blue-600 font-bold hover:underline">
                                        Reset filter
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($members->hasPages())
            <div class="px-5 py-4 border-t border-gray-50 flex items-center justify-between gap-4">
                <p class="text-xs text-gray-400">
                    Menampilkan <strong
                        class="text-gray-600">{{ $members->firstItem() }}–{{ $members->lastItem() }}</strong>
                    dari <strong class="text-gray-600">{{ $members->total() }}</strong> peserta
                </p>
                <div class="flex items-center gap-1.5">
                    {{-- Previous --}}
                    @if ($members->onFirstPage())
                        <span
                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-300 bg-gray-50 border border-gray-100 cursor-not-allowed">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $members->previousPageUrl() }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-white border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-colors">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach ($members->getUrlRange(max(1, $members->currentPage() - 2), min($members->lastPage(), $members->currentPage() + 2)) as $page => $url)
                        @if ($page == $members->currentPage())
                            <span
                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 border border-blue-600">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-white border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($members->hasMorePages())
                        <a href="{{ $members->nextPageUrl() }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-white border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-colors">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @else
                        <span
                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-300 bg-gray-50 border border-gray-100 cursor-not-allowed">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif

    </div>

@endsection
