@extends('layouts.admin')

@section('title', 'Dashboard Magang')

@section('content')

    {{-- =======================
    STATISTIK DASHBOARD
    ======================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        {{-- Lowongan Aktif --}}
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="shrink-0 p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="bi bi-briefcase text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-500 uppercase">
                        Lowongan Aktif
                    </p>
                    <h3 class="text-2xl font-bold text-slate-800">
                        {{ $totalLowongan }}
                    </h3>
                </div>
            </div>
        </div>

        {{-- Verifikasi Masuk --}}
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-yellow-500 hover:bg-yellow-50 transition cursor-pointer"
            onclick="window.location='{{ route('admin.applications.index') }}'">
            <div class="flex items-center">
                <div class="shrink-0 p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="bi bi-file-earmark-person text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-500 uppercase">
                        Verifikasi Masuk
                    </p>
                    <h3 class="text-2xl font-bold text-slate-800">
                        {{ $perluVerifikasi }}
                    </h3>
                    <span class="text-xs text-yellow-600 font-semibold">
                        Perlu ditindak →
                    </span>
                </div>
            </div>
        </div>

        {{-- Sedang Magang --}}
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="shrink-0 p-3 rounded-full bg-green-100 text-green-600">
                    <i class="bi bi-people text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-500 uppercase">
                        Sedang Magang
                    </p>
                    <h3 class="text-2xl font-bold text-slate-800">
                        {{ $sedangMagang }}
                    </h3>
                </div>
            </div>
        </div>

        {{-- Total Peserta --}}
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-slate-400">
            <div class="flex items-center">
                <div class="shrink-0 p-3 rounded-full bg-slate-100 text-slate-600">
                    <i class="bi bi-person-badge text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-500 uppercase">
                        Total Peserta Terdaftar
                    </p>
                    <h3 class="text-2xl font-bold text-slate-800">
                        {{ $totalSiswa + $totalMahasiswa }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- =======================
    PENDAFTARAN TERBARU
    ======================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 mb-8">

        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="text-lg font-medium text-slate-800">
                <i class="bi bi-clock-history mr-2 text-blue-500"></i>
                Pendaftaran Terbaru
            </h3>

            <a href="{{ route('admin.applications.index') }}" class="text-sm text-blue-600 hover:underline font-medium">
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Nama</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Instansi</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Tanggal Daftar</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Status</th>
                        <th class="px-6 py-3 text-right font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($pendaftaranTerbaru as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $item->leader->user->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $item->leader->user->profile->institution_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($item->submission_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @switch($item->status)
                                    @case('pending')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 font-medium">
                                            Pending
                                        </span>
                                    @break

                                    @case('accepted')
                                    @case('active')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-medium">
                                            Aktif
                                        </span>
                                    @break

                                    @case('completed')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 font-medium">
                                            Selesai
                                        </span>
                                    @break

                                    @case('rejected')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-medium">
                                            Ditolak
                                        </span>
                                    @break

                                    @default
                                        <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-600 font-medium">
                                            Tidak Diketahui
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.applications.show', $item) }}"
                                    class="text-blue-600 hover:underline font-medium">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center text-slate-500">
                                        <i class="bi bi-inbox text-3xl mb-2"></i>
                                        <p class="font-medium">Belum ada pendaftaran magang</p>
                                        <p class="text-xs mt-1">
                                            Data pendaftaran terbaru akan muncul di sini.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

        {{-- =======================
    INFORMASI SISTEM
    ======================= --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-medium text-slate-800">
                    <i class="bi bi-info-circle mr-2 text-blue-500"></i>
                    Informasi Sistem
                </h3>
            </div>

            <div class="p-6 text-slate-700 text-sm">
                <p class="mb-4">
                    Selamat datang kembali,
                    <strong>{{ $user->name }}</strong>.
                </p>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded text-blue-700">
                    @if ($hakAkses->role === 'superadmin')
                        <p>
                            <strong>Status: SUPER ADMIN.</strong>
                            Anda memiliki akses penuh ke seluruh data magang dari semua divisi.
                        </p>
                    @else
                        <p>
                            <strong>Status: ADMIN BIDANG ({{ $hakAkses->division_name }}).</strong>
                            Data pada dashboard dan menu magang telah difilter otomatis sesuai divisi Anda.
                        </p>
                    @endif
                </div>
            </div>
        </div>

    @endsection
