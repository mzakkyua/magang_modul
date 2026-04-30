@extends('layouts.admin')

@section('title', 'Dashboard Magang')

@section('content')

    {{-- ===================== WELCOME BANNER ===================== --}}
    <div
        class="bg-linear-to-r from-blue-600 to-blue-500 rounded-2xl p-6 mb-6 flex items-center justify-between overflow-hidden relative">

        {{-- Dekorasi --}}
        <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full translate-x-1/3 -translate-y-1/3"></div>
        <div class="absolute right-16 bottom-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2"></div>

        {{-- Teks Selamat Datang --}}
        <div class="relative z-10">
            <p class="text-blue-100 text-sm font-medium mb-1">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
            <h2 class="text-white font-extrabold text-xl md:text-2xl leading-tight mb-1">
                Selamat Datang, {{ $user->name }} 👋
            </h2>
            <p class="text-blue-100 text-sm">
                @if ($hakAkses->role === 'superadmin')
                    Anda login sebagai <strong class="text-white">Super Admin</strong> — akses penuh ke semua divisi.
                @else
                    Anda login sebagai <strong class="text-white">Admin {{ $hakAkses->division_name }}</strong> — data
                    difilter sesuai divisi Anda.
                @endif
            </p>
        </div>

        {{-- Badge role --}}
        <div class="relative z-10 hidden md:flex flex-col items-end gap-2">
            <span
                class="inline-flex items-center gap-2 bg-white/15 border border-white/20 text-white text-xs font-bold px-4 py-2 rounded-full backdrop-blur-sm">
                <i class="bi bi-shield-check"></i>
                {{ $hakAkses->role === 'superadmin' ? 'Super Admin' : 'Admin Divisi' }}
            </span>
            @if ($perluVerifikasi > 0)
                <span
                    class="inline-flex items-center gap-1.5 bg-amber-400 text-amber-900 text-xs font-bold px-3 py-1.5 rounded-full">
                    <i class="bi bi-exclamation-circle-fill text-[10px]"></i>
                    {{ $perluVerifikasi }} lamaran perlu ditindak
                </span>
            @endif
        </div>

    </div>

    {{-- ===================== STATS CARDS ===================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Lowongan Aktif --}}
        <a href="{{ route('admin.vacancies.index') }}"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md hover:border-blue-100 transition-all duration-200 group">
            <div
                class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center shrink-0 border border-blue-100 group-hover:bg-blue-100 transition">
                <i class="bi bi-briefcase text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Lowongan Aktif</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $totalLowongan }}</h3>
            </div>
        </a>

        {{-- Verifikasi Masuk --}}
        <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}"
            class="bg-white rounded-2xl border border-amber-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md hover:border-amber-200 transition-all duration-200 group cursor-pointer">
            <div
                class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0 border border-amber-200 group-hover:bg-amber-100 transition relative">
                <i class="bi bi-file-earmark-person text-amber-500 text-xl"></i>
                @if ($perluVerifikasi > 0)
                    <span
                        class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                        {{ $perluVerifikasi > 9 ? '9+' : $perluVerifikasi }}
                    </span>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Perlu Verifikasi</p>
                <h3 class="text-2xl font-extrabold text-amber-600">{{ $perluVerifikasi }}</h3>
                <p class="text-[10px] text-amber-500 font-semibold mt-0.5">Tindak sekarang →</p>
            </div>
        </a>

        {{-- Sedang Magang --}}
        <div
            class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-all duration-200 group">
            <div
                class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0 border border-emerald-100 group-hover:bg-emerald-100 transition">
                <i class="bi bi-people text-emerald-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Sedang Magang</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $sedangMagang }}</h3>
            </div>
        </div>

        {{-- Total Peserta --}}
        <div
            class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-all duration-200 group">
            <div
                class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center shrink-0 border border-slate-200 group-hover:bg-slate-100 transition">
                <i class="bi bi-person-badge text-slate-500 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Total Peserta</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $totalSiswa + $totalMahasiswa }}</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">
                    {{ $totalMahasiswa }} mahasiswa · {{ $totalSiswa }} siswa
                </p>
            </div>
        </div>

    </div>

    {{-- ===================== TABEL + INFO SISTEM ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===================== PENDAFTARAN TERBARU (2/3) ===================== --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="bi bi-clock-history text-blue-600 text-xs"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 text-sm">Pendaftaran Terbaru</h3>
                </div>
                <a href="{{ route('admin.applications.index') }}"
                    class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline transition flex items-center gap-1">
                    Lihat Semua <i class="bi bi-arrow-right text-[10px]"></i>
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                Nama</th>
                            <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                Instansi</th>
                            <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">

                        @forelse ($pendaftaranTerbaru as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">

                                {{-- Nama --}}
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                            <i class="bi bi-person-fill text-blue-400 text-sm"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-800">
                                            {{ $item->leader?->username ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Instansi --}}
                                <td class="px-6 py-3.5 text-sm text-gray-500">
                                    {{ $item->leader?->profile?->institution_name ?? '-' }}
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-6 py-3.5 text-sm text-gray-500">
                                    {{ $item->created_at?->format('d M Y') ?? '-' }}
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-3.5 text-center">
                                    @switch($item->status)
                                        @case('pending')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-full">
                                                <i class="bi bi-hourglass-split text-[9px]"></i> Pending
                                            </span>
                                        @break

                                        @case('accepted')
                                        @case('active')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">
                                                <i class="bi bi-check-circle-fill text-[9px]"></i> Aktif
                                            </span>
                                        @break

                                        @case('completed')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-full">
                                                <i class="bi bi-patch-check-fill text-[9px]"></i> Selesai
                                            </span>
                                        @break

                                        @case('rejected')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-red-600 bg-red-50 border border-red-200 rounded-full">
                                                <i class="bi bi-x-circle-fill text-[9px]"></i> Ditolak
                                            </span>
                                        @break

                                        @default
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-gray-500 bg-gray-100 border border-gray-200 rounded-full">
                                                <i class="bi bi-question-circle text-[9px]"></i> Tidak Diketahui
                                            </span>
                                    @endswitch
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('admin.applications.show', $item) }}"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 border border-blue-200 bg-blue-50 px-2.5 py-1.5 rounded-lg hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-150">
                                        Detail <i class="bi bi-arrow-right text-[10px]"></i>
                                    </a>
                                </td>

                            </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div
                                            class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                            <i class="bi bi-inbox text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-semibold text-sm">Belum ada pendaftaran</p>
                                        <p class="text-gray-400 text-xs mt-1">Data terbaru akan muncul di sini.</p>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

            </div>

            {{-- ===================== PANEL KANAN: INFO + QUICK ACCESS ===================== --}}
            <div class="space-y-4">

                {{-- Info Sistem --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                        <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="bi bi-info-circle text-blue-600 text-xs"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 text-sm">Informasi Sistem</h3>
                    </div>

                    <div class="p-5">
                        @if ($hakAkses->role === 'superadmin')
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                                <i class="bi bi-shield-fill-check text-blue-500 mt-0.5 shrink-0"></i>
                                <div>
                                    <p class="text-sm font-bold text-blue-800 mb-0.5">Super Admin</p>
                                    <p class="text-xs text-blue-600 leading-relaxed">Akses penuh ke seluruh data magang dari
                                        semua divisi.</p>
                                </div>
                            </div>
                        @else
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-start gap-3">
                                <i class="bi bi-building text-indigo-500 mt-0.5 shrink-0"></i>
                                <div>
                                    <p class="text-sm font-bold text-indigo-800 mb-0.5">Admin {{ $hakAkses->division_name }}
                                    </p>
                                    <p class="text-xs text-indigo-600 leading-relaxed">Data difilter otomatis sesuai divisi
                                        Anda.</p>
                                </div>
                            </div>
                        @endif

                        {{-- Breakdown peserta --}}
                        <div class="mt-4 space-y-2.5">
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Breakdown Peserta</p>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="bi bi-mortarboard text-blue-400 text-xs"></i>
                                    Mahasiswa
                                </div>
                                <span class="font-bold text-gray-800 text-sm">{{ $totalMahasiswa }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="bi bi-backpack text-purple-400 text-xs"></i>
                                    Siswa SMK
                                </div>
                                <span class="font-bold text-gray-800 text-sm">{{ $totalSiswa }}</span>
                            </div>

                            {{-- Progress bar total --}}
                            @php
                                $total = $totalMahasiswa + $totalSiswa;
                                $pctMahasiswa = $total > 0 ? ($totalMahasiswa / $total) * 100 : 0;
                            @endphp
                            <div class="mt-1">
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden flex">
                                    <div class="h-full bg-blue-500 rounded-l-full" style="width: {{ $pctMahasiswa }}%"></div>
                                    <div class="h-full bg-purple-400 rounded-r-full"
                                        style="width: {{ 100 - $pctMahasiswa }}%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                                    <span>Mahasiswa {{ round($pctMahasiswa) }}%</span>
                                    <span>Siswa {{ round(100 - $pctMahasiswa) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Access --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Akses Cepat</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.vacancies.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition group">
                            <i class="bi bi-briefcase text-gray-400 group-hover:text-blue-500 transition text-base"></i>
                            Kelola Lowongan
                            <i
                                class="bi bi-arrow-right text-xs ml-auto text-gray-300 group-hover:text-blue-400 transition"></i>
                        </a>
                        <a href="{{ route('admin.applications.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition group">
                            <i
                                class="bi bi-file-earmark-check text-gray-400 group-hover:text-blue-500 transition text-base"></i>
                            Verifikasi Lamaran
                            @if ($perluVerifikasi > 0)
                                <span
                                    class="ml-auto text-[10px] font-bold bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full">
                                    {{ $perluVerifikasi }}
                                </span>
                            @else
                                <i
                                    class="bi bi-arrow-right text-xs ml-auto text-gray-300 group-hover:text-blue-400 transition"></i>
                            @endif
                        </a>
                        <a href="{{ route('admin.assessments.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition group">
                            <i class="bi bi-pencil-square text-gray-400 group-hover:text-blue-500 transition text-base"></i>
                            Input Penilaian
                            <i
                                class="bi bi-arrow-right text-xs ml-auto text-gray-300 group-hover:text-blue-400 transition"></i>
                        </a>
                        <a href="{{ route('admin.calendar.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition group">
                            <i class="bi bi-calendar-check text-gray-400 group-hover:text-blue-500 transition text-base"></i>
                            Jadwal Kegiatan
                            <i
                                class="bi bi-arrow-right text-xs ml-auto text-gray-300 group-hover:text-blue-400 transition"></i>
                        </a>
                    </div>
                </div>

            </div>

        </div>

    @endsection
