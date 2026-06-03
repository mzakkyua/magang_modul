@extends('layouts.admin')

@section('title', 'Rekap Peserta Magang')

@section('content')

    {{-- ============================================================ --}}
    {{-- HEADER                                                        --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Rekap Peserta Magang</h1>
            <p class="text-xs text-gray-500 mt-0.5">Semua peserta, status, nilai, dan sertifikat dalam satu tempat.</p>
        </div>
        <a href="{{ route('admin.certificates.create') }}"
            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm transition-all shrink-0">
            <i class="bi bi-cloud-arrow-up-fill"></i> Unggah Sertifikat
        </a>
    </div>

    {{-- ============================================================ --}}
    {{-- SUMMARY STATS — 5 kartu tipis                                --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-5">

        {{-- Total --}}
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                <i class="bi bi-people-fill text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total</p>
                <p class="text-lg font-black text-gray-900 leading-tight">{{ number_format($stats['total']) }}</p>
            </div>
        </div>

        {{-- Aktif --}}
        <div class="bg-blue-50 rounded-xl border border-blue-100 px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-blue-500 shadow-sm shrink-0">
                <i class="bi bi-person-workspace text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">Aktif</p>
                <p class="text-lg font-black text-blue-700 leading-tight">{{ number_format($stats['aktif']) }}</p>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="bg-emerald-50 rounded-xl border border-emerald-100 px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-emerald-500 shadow-sm shrink-0">
                <i class="bi bi-check-circle-fill text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Selesai</p>
                <p class="text-lg font-black text-emerald-700 leading-tight">{{ number_format($stats['selesai']) }}</p>
            </div>
        </div>

        {{-- Bersertifikat --}}
        <div class="bg-violet-50 rounded-xl border border-violet-100 px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-violet-500 shadow-sm shrink-0">
                <i class="bi bi-award-fill text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-violet-500 uppercase tracking-wider">Sertifikat</p>
                <p class="text-lg font-black text-violet-700 leading-tight">{{ number_format($stats['bersertif']) }}</p>
            </div>
        </div>

        {{-- Belum Dinilai --}}
        <div
            class="rounded-xl border px-4 py-3 flex items-center gap-3 shadow-sm {{ $stats['belum_nilai'] > 0 ? 'bg-rose-50 border-rose-200' : 'bg-gray-50 border-gray-200' }}">
            <div
                class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-sm shrink-0 {{ $stats['belum_nilai'] > 0 ? 'text-rose-500' : 'text-gray-400' }}">
                <i class="bi bi-exclamation-triangle-fill text-sm"></i>
            </div>
            <div>
                <p
                    class="text-[10px] font-bold uppercase tracking-wider {{ $stats['belum_nilai'] > 0 ? 'text-rose-500' : 'text-gray-400' }}">
                    Belum Nilai</p>
                <p
                    class="text-lg font-black leading-tight {{ $stats['belum_nilai'] > 0 ? 'text-rose-700' : 'text-gray-600' }}">
                    {{ number_format($stats['belum_nilai']) }}</p>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- FILTER BAR — compact single row                              --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
        <form method="GET" action="{{ route('admin.peserta.index') }}" id="filterForm">
            <div class="flex flex-wrap lg:flex-nowrap items-end gap-3 p-3">

                {{-- Search --}}
                <div class="flex-1 min-w-48">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-search text-gray-400 text-xs"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama atau email..."
                            class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-300 text-xs font-medium text-gray-900 bg-gray-50 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                    </div>
                </div>

                {{-- Status --}}
                <div class="w-36 shrink-0">
                    <select name="status" onchange="document.getElementById('filterForm').submit()"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 bg-gray-50 focus:bg-white outline-none appearance-none cursor-pointer transition-all">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status
                        </option>
                        @foreach ($allStatuses as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sertifikat --}}
                <div class="w-32 shrink-0">
                    <select name="sertifikat" onchange="document.getElementById('filterForm').submit()"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 bg-gray-50 focus:bg-white outline-none appearance-none cursor-pointer transition-all">
                        <option value="all" {{ request('sertifikat', 'all') === 'all' ? 'selected' : '' }}>Semua Sertif
                        </option>
                        <option value="sudah" {{ request('sertifikat') === 'sudah' ? 'selected' : '' }}>Sudah Terbit
                        </option>
                        <option value="belum" {{ request('sertifikat') === 'belum' ? 'selected' : '' }}>Belum Terbit
                        </option>
                    </select>
                </div>

                {{-- Divisi (Superadmin) --}}
                @if ($divisiList->isNotEmpty())
                    <div class="w-44 shrink-0">
                        <select name="divisi" onchange="document.getElementById('filterForm').submit()"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 bg-gray-50 focus:bg-white outline-none appearance-none cursor-pointer transition-all">
                            <option value="all" {{ request('divisi', 'all') === 'all' ? 'selected' : '' }}>Semua Divisi
                            </option>
                            @foreach ($divisiList as $div)
                                <option value="{{ $div }}" {{ request('divisi') === $div ? 'selected' : '' }}>
                                    {{ $div }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Tombol --}}
                <div class="flex gap-2 shrink-0">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition-all shadow-sm">
                        Filter
                    </button>
                    @if (request('search') ||
                            (request('status') && request('status') !== 'all') ||
                            (request('sertifikat') && request('sertifikat') !== 'all') ||
                            (request('divisi') && request('divisi') !== 'all'))
                        <a href="{{ route('admin.peserta.index') }}"
                            class="px-3 py-2 bg-white border border-gray-300 text-gray-500 text-xs font-bold rounded-lg hover:text-red-500 hover:border-red-300 transition-all flex items-center">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL PESERTA                                                 --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-xs flex items-center gap-2">
                <i class="bi bi-person-lines-fill text-indigo-500"></i> Daftar Peserta
            </h3>
            <span class="text-[10px] font-bold bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full">
                {{ $members->total() }} data
            </span>
        </div>

        @if ($members->isEmpty())
            <div class="py-16 text-center flex flex-col items-center">
                <div
                    class="w-14 h-14 bg-gray-50 border-2 border-dashed border-gray-200 rounded-full flex items-center justify-center mb-3 text-gray-300">
                    <i class="bi bi-search text-xl"></i>
                </div>
                <h4 class="text-sm text-gray-900 font-bold mb-1">Data Tidak Ditemukan</h4>
                <p class="text-xs text-gray-500 max-w-xs">Tidak ada peserta yang cocok dengan filter pencarian Anda.</p>
                @if (request()->hasAny(['search', 'status', 'sertifikat', 'divisi']))
                    <a href="{{ route('admin.peserta.index') }}"
                        class="mt-3 text-xs font-bold text-indigo-600 hover:underline">Reset Filter</a>
                @endif
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden lg:block">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="border-b border-gray-100 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">
                            <th class="px-4 py-2.5">Peserta</th>
                            <th class="px-4 py-2.5 hidden md:table-cell">Penempatan</th>
                            <th class="px-4 py-2.5 text-center">Status</th>
                            <th class="px-4 py-2.5 text-center hidden sm:table-cell">Nilai</th>
                            <th class="px-4 py-2.5 text-center hidden sm:table-cell">Sertif</th>
                            <th class="px-4 py-2.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            @include('admin.peserta.partials.row', ['member' => $member])
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="flex flex-col gap-2 p-3 lg:hidden bg-gray-50/50">
                @foreach ($members as $member)
                    @include('admin.peserta.partials.card', ['member' => $member])
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($members->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    {{ $members->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
