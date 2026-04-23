@extends('layouts.admin')

@section('title', 'Penilaian Peserta Magang')

@section('content')

    {{-- ===================== TOOLBAR: SEARCH ===================== --}}
    <form method="GET" action="{{ route('admin.assessments.index') }}" class="flex gap-3 mb-5">
        <div class="relative flex-1 max-w-sm">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="bi bi-search text-gray-400 text-sm"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peserta..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       outline-none transition-all duration-200
                       border-gray-200 bg-white placeholder:text-gray-300 placeholder:font-normal
                       hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <button type="submit"
            class="px-4 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl
                   hover:bg-blue-700 transition-all duration-200 shadow-md shadow-blue-600/25">
            Cari
        </button>
        @if (request('search'))
            <a href="{{ route('admin.assessments.index') }}"
                class="px-4 py-2.5 bg-white border border-gray-200 text-gray-500 text-sm font-medium
                      rounded-xl hover:bg-gray-50 transition-colors">
                Reset
            </a>
        @endif
    </form>

    {{-- ===================== TABEL ===================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Header bar --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                    <i class="bi bi-pencil-square text-blue-500 text-xs"></i>
                </div>
                <span class="text-sm font-extrabold text-gray-900">Daftar Peserta Aktif</span>
                <span
                    class="text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100
                             px-2.5 py-0.5 rounded-full">
                    {{ $members->total() }} peserta
                </span>
            </div>
            <span class="text-xs text-gray-400 hidden sm:block">
                Hanya peserta berstatus <strong class="text-gray-600">Diterima</strong> yang tampil
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Peserta
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Posisi & Divisi
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Status Nilai
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    @forelse($members as $member)
                        <tr class="hover:bg-slate-50/70 transition-colors group">

                            {{-- Peserta --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center
                                                border border-blue-200 shrink-0 font-extrabold text-blue-600 text-sm">
                                        {{ strtoupper(substr($member->user->profile->full_name ?? $member->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-bold text-gray-900 leading-tight
                                                  group-hover:text-blue-600 transition-colors">
                                            {{ $member->user->profile->full_name ?? $member->user->name }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $member->user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Posisi & Divisi --}}
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-800 leading-tight mb-1">
                                    {{ $member->application->vacancy->title }}
                                </p>
                                <span
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold
                                             text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full">
                                    <i class="bi bi-building text-[10px]"></i>
                                    {{ $member->application->vacancy->division_name }}
                                </span>
                            </td>

                            {{-- Status Nilai --}}
                            <td class="px-5 py-4 text-center">
                                @if ($member->assessment)
                                    @php $score = $member->assessment->final_score; @endphp
                                    <div class="flex flex-col items-center gap-1">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] font-extrabold
                                                     rounded-full {{ $member->assessment->isPassed()
                                                         ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                                         : 'bg-red-50 text-red-600 border border-red-200' }}">
                                            <i
                                                class="bi {{ $member->assessment->isPassed() ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} text-[10px]"></i>
                                            {{ $member->assessment->isPassed() ? 'Lulus' : 'Tidak Lulus' }}
                                        </span>
                                        <span class="text-[11px] font-extrabold text-gray-700">
                                            {{ number_format($score, 2) }}
                                            <span class="text-gray-400 font-normal">/ 100</span>
                                        </span>
                                    </div>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold
                                                 bg-amber-50 text-amber-600 border border-amber-200 rounded-full">
                                        <span
                                            class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse inline-block"></span>
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('admin.assessments.create', $member->id) }}"
                                    class="inline-flex items-center gap-1.5 text-sm font-bold px-3 py-1.5 rounded-xl
                                           border transition-all duration-150
                                           {{ $member->assessment
                                               ? 'text-indigo-600 border-indigo-200 bg-indigo-50 hover:bg-indigo-600 hover:text-white hover:border-indigo-600'
                                               : 'text-blue-600 border-blue-200 bg-blue-50 hover:bg-blue-600 hover:text-white hover:border-blue-600' }}">
                                    <i class="bi {{ $member->assessment ? 'bi-pencil' : 'bi-plus-lg' }} text-xs"></i>
                                    {{ $member->assessment ? 'Edit Nilai' : 'Input Nilai' }}
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div
                                    class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="bi bi-people text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-600">Belum ada peserta aktif</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    @if (request('search'))
                                        Tidak ditemukan peserta dengan nama "<strong>{{ request('search') }}</strong>".
                                    @else
                                        Terima pelamar di menu <strong>Verifikasi</strong> terlebih dahulu.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        @if ($members->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $members->appends(request()->query())->links() }}
            </div>
        @endif

    </div>

@endsection
