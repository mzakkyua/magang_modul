@extends('layouts.admin')

@section('title', 'Rekap Peserta Magang')

@section('content')

    {{-- ============================================================ --}}
    {{-- HEADER & SUMMARY STATS                                       --}}
    {{-- ============================================================ --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Rekap Peserta Magang</h1>
                <p class="text-sm text-gray-500 mt-1">Pusat kontrol data peserta, status, nilai, dan sertifikat.</p>
            </div>
            <a href="{{ route('admin.certificates.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all focus:ring-4 focus:ring-indigo-200 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd"
                        d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                        clip-rule="evenodd" />
                </svg>
                Unggah Sertifikat Massal
            </a>
        </div>

        {{-- Summary Stats Bar (Scrollable horizontally on mobile) --}}
        <div
            class="flex overflow-x-auto pb-4 -mx-4 px-4 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-3 lg:grid-cols-5 gap-4 hide-scrollbar">

            {{-- Total --}}
            <div
                class="bg-white rounded-2xl border border-gray-200 p-4 flex flex-col justify-center min-w-40 sm:min-w-0 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2 z-10 relative">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Peserta</p>
                    <div class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 flex items-center justify-center"><i
                            class="bi bi-people-fill"></i></div>
                </div>
                <p class="text-2xl font-black text-gray-900 z-10 relative">{{ number_format($stats['total']) }}</p>
            </div>

            {{-- Aktif --}}
            <div
                class="bg-blue-50 rounded-2xl border border-blue-100 p-4 flex flex-col justify-center min-w-40in-w-0 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2 z-10 relative">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">Sedang Magang</p>
                    <div class="w-8 h-8 rounded-lg bg-white text-blue-500 flex items-center justify-center shadow-sm"><i
                            class="bi bi-person-workspace"></i></div>
                </div>
                <p class="text-2xl font-black text-blue-700 z-10 relative">{{ number_format($stats['aktif']) }}</p>
            </div>

            {{-- Selesai --}}
            <div
                class="bg-emerald-50 rounded-2xl border border-emerald-100 p-4 flex flex-col justify-center min-w-40 sm:min-w-0 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2 z-10 relative">
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Selesai</p>
                    <div class="w-8 h-8 rounded-lg bg-white text-emerald-500 flex items-center justify-center shadow-sm"><i
                            class="bi bi-check-circle-fill"></i></div>
                </div>
                <p class="text-2xl font-black text-emerald-700 z-10 relative">{{ number_format($stats['selesai']) }}</p>
            </div>

            {{-- Bersertifikat --}}
            <div
                class="bg-violet-50 rounded-2xl border border-violet-100 p-4 flex flex-col justify-center min-w-40 sm:min-w-0 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2 z-10 relative">
                    <p class="text-xs font-bold text-violet-600 uppercase tracking-wider">Bersertifikat</p>
                    <div class="w-8 h-8 rounded-lg bg-white text-violet-500 flex items-center justify-center shadow-sm"><i
                            class="bi bi-award-fill"></i></div>
                </div>
                <p class="text-2xl font-black text-violet-700 z-10 relative">{{ number_format($stats['bersertif']) }}</p>
            </div>

            {{-- Belum Dinilai --}}
            <div
                class="rounded-2xl border p-4 flex flex-col justify-center min-w-40 sm:min-w-0 shadow-sm relative overflow-hidden transition-colors {{ $stats['belum_nilai'] > 0 ? 'bg-rose-50 border-rose-200' : 'bg-gray-50 border-gray-200' }}">
                <div class="flex items-center justify-between mb-2 z-10 relative">
                    <p
                        class="text-xs font-bold uppercase tracking-wider {{ $stats['belum_nilai'] > 0 ? 'text-rose-600' : 'text-gray-500' }}">
                        Belum Dinilai</p>
                    <div
                        class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm {{ $stats['belum_nilai'] > 0 ? 'bg-white text-rose-500' : 'bg-white text-gray-400' }}">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
                <p
                    class="text-2xl font-black z-10 relative {{ $stats['belum_nilai'] > 0 ? 'text-rose-700' : 'text-gray-700' }}">
                    {{ number_format($stats['belum_nilai']) }}</p>
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FILTER & SEARCH BAR                                          --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.peserta.index') }}" id="filterForm">
            <div class="flex flex-col lg:flex-row gap-4">

                {{-- Search --}}
                <div class="flex-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Cari
                        Peserta</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Ketik nama atau email..."
                            class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm font-medium text-gray-900 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                    </div>
                </div>

                <div class="flex flex-wrap sm:flex-nowrap gap-4">
                    {{-- Filter: Status --}}
                    <div class="flex-1 sm:w-40 shrink-0">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Status
                            Magang</label>
                        <select name="status" onchange="document.getElementById('filterForm').submit()"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 bg-gray-50 hover:bg-white focus:bg-white outline-none appearance-none transition-all cursor-pointer">
                            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status
                            </option>
                            @foreach ($allStatuses as $val => $label)
                                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter: Sertifikat --}}
                    <div class="flex-1 sm:w-36 shrink-0">
                        <label
                            class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Sertifikat</label>
                        <select name="sertifikat" onchange="document.getElementById('filterForm').submit()"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 bg-gray-50 hover:bg-white focus:bg-white outline-none appearance-none transition-all cursor-pointer">
                            <option value="all" {{ request('sertifikat', 'all') === 'all' ? 'selected' : '' }}>Semua
                            </option>
                            <option value="sudah" {{ request('sertifikat') === 'sudah' ? 'selected' : '' }}>Selesai
                            </option>
                            <option value="belum" {{ request('sertifikat') === 'belum' ? 'selected' : '' }}>Belum</option>
                        </select>
                    </div>

                    {{-- Filter: Divisi (Superadmin Only) --}}
                    @if ($divisiList->isNotEmpty())
                        <div class="w-full sm:w-48 shrink-0">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Divisi
                                Penempatan</label>
                            <select name="divisi" onchange="document.getElementById('filterForm').submit()"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 bg-gray-50 hover:bg-white focus:bg-white outline-none appearance-none transition-all cursor-pointer">
                                <option value="all" {{ request('divisi', 'all') === 'all' ? 'selected' : '' }}>Semua
                                    Divisi</option>
                                @foreach ($divisiList as $div)
                                    <option value="{{ $div }}"
                                        {{ request('divisi') === $div ? 'selected' : '' }}>{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-2 items-end shrink-0 w-full lg:w-auto">
                    <button type="submit"
                        class="flex-1 lg:flex-none px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-all shadow-md focus:ring-4 focus:ring-gray-200">
                        Filter Data
                    </button>

                    @if (request()->hasAny(['search', 'status', 'sertifikat', 'divisi']) &&
                            (request('search') ||
                                request('status') !== 'all' ||
                                request('sertifikat') !== 'all' ||
                                request('divisi') !== 'all'))
                        <a href="{{ route('admin.peserta.index') }}" title="Reset Pencarian"
                            class="px-4 py-2.5 bg-white border border-gray-300 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 hover:text-red-600 hover:border-red-300 transition-all flex items-center justify-center">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL REKAP PESERTA (Responsive Grid Cards on Mobile)        --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">

        {{-- Header Data --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <i class="bi bi-person-lines-fill text-indigo-500"></i> Detail Peserta
            </h3>
            <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">
                {{ $members->total() }} Data Ditemukan
            </span>
        </div>

        @if ($members->isEmpty())
            <div class="px-6 py-20 text-center flex flex-col items-center">
                <div
                    class="w-20 h-20 bg-gray-50 border-2 border-dashed border-gray-200 rounded-full flex items-center justify-center mb-4 text-gray-400">
                    <i class="bi bi-search text-2xl"></i>
                </div>
                <h4 class="text-gray-900 font-bold mb-1">Data Tidak Ditemukan</h4>
                <p class="text-sm text-gray-500 max-w-sm">Tidak ada peserta yang cocok dengan filter atau kata kunci
                    pencarian Anda. Silakan coba kriteria lain.</p>
                @if (request()->hasAny(['search', 'status', 'sertifikat', 'divisi']))
                    <a href="{{ route('admin.peserta.index') }}"
                        class="mt-4 text-sm font-bold text-indigo-600 hover:text-indigo-800 underline underline-offset-2">Reset
                        Semua Filter</a>
                @endif
            </div>
        @else
            {{-- Wrapper untuk Grid (Mobile) & Table (Desktop) --}}
            <div class="w-full">
                {{-- Desktop Table View (Hidden on mobile) --}}
                <div class="hidden lg:block w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-white border-b border-gray-200 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">
                                <th class="px-6 py-4">Informasi Peserta</th>
                                <th class="px-6 py-4">Penempatan Divisi</th>
                                <th class="px-6 py-4 text-center">Status Magang</th>
                                <th class="px-6 py-4 text-center">Penilaian</th>
                                <th class="px-6 py-4 text-center">Sertifikat</th>
                                <th class="px-6 py-4 text-center">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($members as $member)
                                @include('admin.peserta.partials.row', ['member' => $member])
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Grid View (Hidden on desktop) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 lg:hidden bg-gray-50/50">
                    @foreach ($members as $member)
                        @include('admin.peserta.partials.card', ['member' => $member])
                    @endforeach
                </div>
            </div>

            {{-- Pagination Footer --}}
            @if ($members->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 mt-auto">
                    {{ $members->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
