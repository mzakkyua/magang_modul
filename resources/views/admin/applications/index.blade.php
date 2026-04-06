@extends('layouts.admin')

@section('title', 'Verifikasi Pelamar')

@section('content')

    {{-- ===================== PAGE HEADER + SUMMARY CARDS ===================== --}}

    {{-- Summary Cards (Diubah jadi grid-cols-7 agar kartu 'Selesai' muat) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-gray-800">{{ array_sum($statusCounts) }}</p>
            <p class="text-xs text-gray-400 font-medium">Total</p>
        </div>

        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-amber-500">{{ $statusCounts['pending'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Menunggu</p>
        </div>

        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-blue-500">{{ $statusCounts['verified'] + $statusCounts['interview'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Diproses</p>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-emerald-500">{{ $statusCounts['accepted'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Diterima</p>
        </div>

        <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-red-400">{{ $statusCounts['rejected'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Ditolak</p>
        </div>

        <div class="bg-white rounded-2xl border border-orange-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-orange-400">{{ $statusCounts['resigned'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Mundur</p>
        </div>

        {{-- TAMBAHAN BARU: Kartu Selesai --}}
        <div class="bg-white rounded-2xl border border-teal-100 shadow-sm p-4 flex flex-col gap-1">
            <p class="text-2xl font-extrabold text-teal-500">{{ $statusCounts['completed'] }}</p>
            <p class="text-xs text-gray-400 font-medium">Selesai/Lulus</p>
        </div>

    </div>

    {{-- ===================== FILTER TABS ===================== --}}
    @php
        $currentStatus = request('status');
        $activeClass = 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-600/20 font-bold';
        $inactiveClass = 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:border-gray-300 font-semibold';
    @endphp

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.applications.index') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm border transition-all duration-150 {{ !$currentStatus ? $activeClass : $inactiveClass }}">
            <i class="bi bi-grid-3x3-gap text-xs"></i> Semua
            <span
                class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ !$currentStatus ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">{{ array_sum($statusCounts) }}</span>
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm border transition-all duration-150 {{ $currentStatus === 'pending' ? $activeClass : $inactiveClass }}">
            <i
                class="bi bi-hourglass-split text-xs {{ $currentStatus === 'pending' ? 'text-white' : 'text-amber-500' }}"></i>
            Menunggu
            <span
                class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $currentStatus === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-600 border border-amber-200' }}">{{ $statusCounts['pending'] }}</span>
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'verified']) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm border transition-all duration-150 {{ $currentStatus === 'verified' ? $activeClass : $inactiveClass }}">
            <i class="bi bi-shield-check text-xs {{ $currentStatus === 'verified' ? 'text-white' : 'text-blue-500' }}"></i>
            Diverifikasi
            <span
                class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $currentStatus === 'verified' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600 border border-blue-200' }}">{{ $statusCounts['verified'] }}</span>
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'interview']) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm border transition-all duration-150 {{ $currentStatus === 'interview' ? $activeClass : $inactiveClass }}">
            <i
                class="bi bi-camera-video text-xs {{ $currentStatus === 'interview' ? 'text-white' : 'text-purple-500' }}"></i>
            Interview
            <span
                class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $currentStatus === 'interview' ? 'bg-white/20 text-white' : 'bg-purple-50 text-purple-600 border border-purple-200' }}">{{ $statusCounts['interview'] }}</span>
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'accepted']) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm border transition-all duration-150 {{ $currentStatus === 'accepted' ? $activeClass : $inactiveClass }}">
            <i
                class="bi bi-check-circle text-xs {{ $currentStatus === 'accepted' ? 'text-white' : 'text-emerald-500' }}"></i>
            Diterima
            <span
                class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $currentStatus === 'accepted' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">{{ $statusCounts['accepted'] }}</span>
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'rejected']) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm border transition-all duration-150 {{ $currentStatus === 'rejected' ? $activeClass : $inactiveClass }}">
            <i class="bi bi-x-circle text-xs {{ $currentStatus === 'rejected' ? 'text-white' : 'text-red-400' }}"></i>
            Ditolak
            <span
                class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $currentStatus === 'rejected' ? 'bg-white/20 text-white' : 'bg-red-50 text-red-500 border border-red-200' }}">{{ $statusCounts['rejected'] }}</span>
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'resigned']) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm border transition-all duration-150 {{ $currentStatus === 'resigned' ? $activeClass : $inactiveClass }}">
            <i
                class="bi bi-person-walking text-xs {{ $currentStatus === 'resigned' ? 'text-white' : 'text-orange-400' }}"></i>
            Mundur
            <span
                class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $currentStatus === 'resigned' ? 'bg-white/20 text-white' : 'bg-orange-50 text-orange-500 border border-orange-200' }}">{{ $statusCounts['resigned'] }}</span>
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'completed']) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm border transition-all duration-150 {{ $currentStatus === 'completed' ? $activeClass : $inactiveClass }}">
            <i class="bi bi-award-fill text-xs {{ $currentStatus === 'completed' ? 'text-white' : 'text-teal-500' }}"></i>
            Selesai
            <span
                class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $currentStatus === 'completed' ? 'bg-white/20 text-white' : 'bg-teal-50 text-teal-600 border border-teal-200' }}">{{ $statusCounts['completed'] }}</span>
        </a>
    </div>

    {{-- ===================== TABEL UTAMA ===================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Table header bar --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-file-earmark-check text-blue-600 text-xs"></i>
                </div>
                <h2 class="font-bold text-gray-800 text-sm">
                    @if ($currentStatus)
                        Lamaran —
                        <span class="text-blue-600 capitalize">{{ ucfirst($currentStatus) }}</span>
                    @else
                        Semua Lamaran
                    @endif
                </h2>
            </div>
            <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1 rounded-full font-medium">
                {{ $data->total() }} lamaran
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Pelamar</th>
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Lowongan Dilamar</th>
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Tanggal Masuk</th>
                        <th class="px-6 py-3.5 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3.5 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    @forelse($data as $app)
                        <tr class="hover:bg-gray-50/80 transition-colors">

                            {{-- Pelamar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center shrink-0 border border-gray-200">
                                        <i class="bi bi-person-fill text-gray-400 text-base"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 leading-tight">
                                            {{ $app->leader->name ?? 'User Terhapus' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $app->leader->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Lowongan --}}
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900 leading-tight mb-1">
                                    {{ $app->vacancy->title }}
                                </p>
                                <span
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full">
                                    <i class="bi bi-building text-[10px]"></i>
                                    {{ $app->vacancy->division_name }}
                                </span>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 font-medium">
                                    {{ \Carbon\Carbon::parse($app->submission_date)->format('d M Y') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($app->submission_date)->format('H:i') }} WIB
                                </p>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 text-center">
                                @if ($app->status === 'pending')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-full">
                                        <i class="bi bi-hourglass-split text-[10px]"></i> Menunggu
                                    </span>
                                @elseif ($app->status === 'verified')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-full">
                                        <i class="bi bi-shield-check text-[10px]"></i> Diverifikasi
                                    </span>
                                @elseif ($app->status === 'interview')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-purple-700 bg-purple-50 border border-purple-200 rounded-full">
                                        <i class="bi bi-camera-video text-[10px]"></i> Interview
                                    </span>
                                @elseif ($app->status === 'accepted')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">
                                        <i class="bi bi-check-circle-fill text-[10px]"></i> Diterima
                                    </span>
                                @elseif ($app->status === 'rejected')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-red-600 bg-red-50 border border-red-200 rounded-full">
                                        <i class="bi bi-x-circle-fill text-[10px]"></i> Ditolak
                                    </span>
                                @elseif ($app->status === 'resigned')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-orange-600 bg-orange-50 border border-orange-200 rounded-full">
                                        <i class="bi bi-person-walking text-[10px]"></i> Mundur
                                    </span>
                                @elseif ($app->status === 'completed')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-teal-700 bg-teal-50 border border-teal-200 rounded-full">
                                        <i class="bi bi-award-fill text-[10px]"></i> Lulus
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.applications.show', $app->id) }}"
                                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 border border-blue-200 bg-blue-50 px-3 py-1.5 rounded-xl hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-150">
                                    Periksa Berkas
                                    <i class="bi bi-arrow-right text-xs"></i>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="bi bi-inbox text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-semibold text-sm">Tidak ada lamaran</p>
                                <p class="text-gray-400 text-xs mt-1">
                                    @if ($currentStatus)
                                        Tidak ada lamaran dengan status "{{ ucfirst($currentStatus) }}" saat ini.
                                    @else
                                        Belum ada lamaran masuk untuk divisi Anda.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($data->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $data->appends(request()->query())->links() }}
            </div>
        @endif

    </div>

@endsection
