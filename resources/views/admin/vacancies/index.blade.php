@extends('layouts.admin')

@section('title', 'Manajemen Lowongan')

{{-- 
    TOMBOL INI dipindah ke topbar via @stack('header_actions').
    Layout admin sudah menyediakan @stack('header_actions') di sebelah kanan topbar.
    Lebih rapi: judul halaman di kiri, tombol aksi di kanan — satu baris.
--}}
@push('header_actions')
    <a href="{{ route('admin.vacancies.create') }}"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
               text-white text-sm font-bold px-4 py-2 rounded-xl
               shadow-md shadow-blue-600/25 hover:shadow-blue-600/40
               transition-all duration-200 hover:-translate-y-0.5">
        <i class="bi bi-plus-lg"></i> Buat Lowongan Baru
    </a>
@endpush

@section('content')

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <i class="bi bi-briefcase text-blue-500 text-base"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900 leading-none">{{ $totalVacancies ?? 0 }}</p>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Total Lowongan</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-emerald-500 text-base"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-emerald-600 leading-none">{{ $openCount ?? 0 }}</p>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Sedang Buka</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <i class="bi bi-x-circle text-red-400 text-base"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-red-500 leading-none">{{ $closedCount ?? 0 }}</p>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Ditutup</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <i class="bi bi-people text-amber-500 text-base"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-amber-500 leading-none">{{ $withApplicantsCount ?? 0 }}</p>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Ada Pendaftar</p>
            </div>
        </div>

    </div>

    {{-- =========================
    TABEL DATA LOWONGAN
    ========================= --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Table header bar --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <div class="flex items-center gap-2.5">
                <span class="text-sm font-extrabold text-gray-900">Daftar Lowongan</span>
                <span
                    class="text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100
                             px-2.5 py-0.5 rounded-full">
                    {{ $vacancies->total() }} lowongan
                </span>
            </div>
            <p class="text-xs text-gray-400 hidden sm:block">Buka / tutup / edit sesuai kondisi</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">

                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Lowongan
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Tipe & Mode
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Kuota
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Periode
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50">
                    @forelse ($vacancies as $vacancy)
                        <tr class="hover:bg-slate-50/70 transition-colors duration-150 group">

                            {{-- KOLOM LOWONGAN --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- Accent bar: biru = magang, ungu = penelitian --}}
                                    <div
                                        class="w-1 h-10 rounded-full shrink-0
                                        {{ $vacancy->type === 'penelitian' ? 'bg-violet-400' : 'bg-blue-500' }}">
                                    </div>
                                    <div>
                                        <p
                                            class="font-bold text-gray-900 text-[13.5px] leading-snug
                                                  group-hover:text-blue-600 transition-colors duration-150">
                                            {{ $vacancy->title }}
                                        </p>
                                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                                            <span
                                                class="inline-flex items-center gap-1 text-[10.5px] font-semibold
                                                         bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                                                <i class="bi bi-building text-[9px]"></i>
                                                {{ $vacancy->division_name }}
                                            </span>
                                            @if ($vacancy->applications_count > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 text-[10.5px] font-semibold
                                                             bg-amber-50 text-amber-600 border border-amber-200
                                                             px-2 py-0.5 rounded-full">
                                                    <i class="bi bi-people-fill text-[9px]"></i>
                                                    {{ $vacancy->applications_count }} Pendaftar
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- KOLOM TIPE & MODE --}}
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 text-[11.5px] font-bold capitalize
                                             px-2.5 py-1 rounded-lg
                                             {{ $vacancy->type === 'penelitian'
                                                 ? 'text-violet-600 bg-violet-50 border border-violet-100'
                                                 : 'text-blue-600 bg-blue-50 border border-blue-100' }}">
                                    <i
                                        class="bi {{ $vacancy->type === 'penelitian' ? 'bi-journal-text' : 'bi-person-workspace' }} text-[10px]"></i>
                                    {{ $vacancy->type }}
                                </span>
                                <p class="text-[11px] text-gray-400 capitalize mt-1.5">
                                    <i class="bi bi-people text-[9px] mr-0.5"></i>
                                    {{ $vacancy->registration_mode }}
                                </p>
                            </td>

                            {{-- KOLOM KUOTA --}}
                            <td class="px-5 py-4 text-center">
                                <span
                                    class="inline-flex items-center gap-1 text-[11.5px] font-bold
                                             bg-blue-50 text-blue-600 border border-blue-100
                                             px-3 py-1 rounded-full">
                                    <i class="bi bi-people text-[10px]"></i>
                                    {{ $vacancy->quota_slots }} Slot
                                </span>
                            </td>

                            {{-- KOLOM PERIODE --}}
                            <td class="px-5 py-4 text-center">
                                <p class="text-[12px] font-semibold text-gray-700">
                                    {{ $vacancy->start_date->format('d M Y') }}
                                </p>
                                <p class="text-[10px] text-gray-300 my-0.5">s/d</p>
                                <p class="text-[12px] font-semibold text-gray-700">
                                    {{ $vacancy->end_date->format('d M Y') }}
                                </p>
                            </td>

                            {{-- KOLOM STATUS --}}
                            <td class="px-5 py-4 text-center">
                                @if ($vacancy->status === 'open')
                                    <span
                                        class="inline-flex items-center gap-1.5 text-[11px] font-extrabold
                                                 bg-emerald-50 text-emerald-600 border border-emerald-200
                                                 px-2.5 py-1 rounded-full">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                                        OPEN
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 text-[11px] font-extrabold
                                                 bg-gray-100 text-gray-400 border border-gray-200
                                                 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                        CLOSED
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM AKSI --}}
                            {{-- Semua route, form method, data-* attribute tidak diubah --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">

                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.vacancies.edit', $vacancy) }}" title="Edit Lowongan"
                                        class="w-8 h-8 rounded-lg border border-gray-200 bg-white
                                               flex items-center justify-center text-gray-400
                                               hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600
                                               transition-all duration-150">
                                        <i class="bi bi-pencil-square text-[13px]"></i>
                                    </a>

                                    {{-- TOGGLE --}}
                                    <form action="{{ route('admin.vacancies.toggle', $vacancy) }}" method="POST"
                                        class="inline-flex form-toggle" data-title="{{ $vacancy->title }}"
                                        data-status="{{ $vacancy->status }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Buka / Tutup Lowongan"
                                            class="w-8 h-8 rounded-lg border flex items-center justify-center
                                                   transition-all duration-150
                                                   {{ $vacancy->status === 'open'
                                                       ? 'border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-100'
                                                       : 'border-gray-200 bg-white text-gray-400 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-500' }}">
                                            <i
                                                class="bi {{ $vacancy->status === 'open' ? 'bi-toggle-on' : 'bi-toggle-off' }} text-lg leading-none"></i>
                                        </button>
                                    </form>

                                    {{-- DELETE --}}
                                    @if ($vacancy->applications_count === 0)
                                        <form action="{{ route('admin.vacancies.destroy', $vacancy) }}" method="POST"
                                            class="inline-flex form-delete" data-name="{{ $vacancy->title }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Permanen"
                                                class="w-8 h-8 rounded-lg border border-gray-200 bg-white
                                                       flex items-center justify-center text-gray-400
                                                       hover:border-red-200 hover:bg-red-50 hover:text-red-500
                                                       transition-all duration-150">
                                                <i class="bi bi-trash text-[13px]"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div title="Tidak dapat dihapus — sudah ada pendaftar"
                                            class="w-8 h-8 rounded-lg border border-gray-100 bg-gray-50
                                                    flex items-center justify-center text-gray-300 cursor-not-allowed">
                                            <i class="bi bi-trash text-[13px]"></i>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                                        <i class="bi bi-briefcase text-gray-400 text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-700">Belum Ada Lowongan</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Buat lowongan pertama untuk mulai menerima pendaftar.
                                        </p>
                                    </div>
                                    <a href="{{ route('admin.vacancies.create') }}"
                                        class="inline-flex items-center gap-2 bg-blue-600 text-white
                                               text-xs font-bold px-4 py-2 rounded-xl
                                               hover:bg-blue-700 transition-colors mt-1">
                                        <i class="bi bi-plus-lg"></i> Buat Lowongan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($vacancies->hasPages())
            <div class="px-5 py-4 border-t border-gray-50 bg-gray-50/50">
                {{ $vacancies->links() }}
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/delete-confirm.js'])
@endpush
