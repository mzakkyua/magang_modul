@extends('layouts.landing')

@section('content')
    <div class="bg-gray-50 min-h-screen py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ===================== PAGE HEADER ===================== --}}
            <div class="mb-10">
                <p class="text-blue-600 font-bold uppercase tracking-widest text-xs mb-2 flex items-center gap-2">
                    <i class="bi bi-card-checklist"></i> Pendaftaran Magang
                </p>
                <h1 class="text-3xl font-extrabold text-gray-900">Riwayat Pengajuan</h1>
                <p class="text-gray-500 text-sm mt-1">Pantau status seluruh lamaran magang yang pernah kamu ajukan.</p>
            </div>

            {{-- ===================== SUMMARY CARDS ===================== --}}
            @php
                // FIX: Hitung ringkasan status secara spesifik per nilai status
                // Sebelumnya $pending pakai whereNotIn sehingga completed & resigned ikut terhitung
                $total = $applications->count();
                $pending = $applications->whereIn('status', ['pending', 'verified', 'interview'])->count();
                $accepted = $applications->where('status', 'accepted')->count();
                $rejected = $applications->where('status', 'rejected')->count();
                $completed = $applications->where('status', 'completed')->count();
                $resigned = $applications->where('status', 'resigned')->count();
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">

                {{-- Total --}}
                <div
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 col-span-2 md:col-span-1 lg:col-span-1">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center shrink-0">
                        <i class="bi bi-collection text-gray-500 text-base"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-gray-800 leading-none">{{ $total }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-medium">Total</p>
                    </div>
                </div>

                {{-- Menunggu --}}
                <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                        <i class="bi bi-hourglass-split text-amber-500 text-base"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-amber-600 leading-none">{{ $pending }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-medium">Menunggu</p>
                    </div>
                </div>

                {{-- Diterima --}}
                <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                        <i class="bi bi-check-circle text-emerald-500 text-base"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-emerald-600 leading-none">{{ $accepted }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-medium">Diterima</p>
                    </div>
                </div>

                {{-- Selesai --}}
                <div class="bg-white rounded-2xl border border-teal-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center shrink-0">
                        <i class="bi bi-award text-teal-500 text-base"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-teal-600 leading-none">{{ $completed }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-medium">Selesai</p>
                    </div>
                </div>

                {{-- Ditolak --}}
                <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center shrink-0">
                        <i class="bi bi-x-circle text-red-400 text-base"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-red-500 leading-none">{{ $rejected }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-medium">Ditolak</p>
                    </div>
                </div>

                {{-- Mundur --}}
                <div class="bg-white rounded-2xl border border-orange-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center shrink-0">
                        <i class="bi bi-person-walking text-orange-400 text-base"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-orange-500 leading-none">{{ $resigned }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-medium">Mundur</p>
                    </div>
                </div>

            </div>

            {{-- ===================== TABEL RIWAYAT ===================== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Table Header Bar --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="bi bi-list-ul text-blue-600 text-xs"></i>
                        </div>
                        <h2 class="font-bold text-gray-800 text-sm">Semua Lamaran</h2>
                    </div>
                    <span
                        class="text-xs text-gray-400 font-medium bg-gray-50 border border-gray-100 px-3 py-1 rounded-full">
                        {{ $total }} lamaran
                    </span>
                </div>

                {{-- ===================== DESKTOP: TABLE ===================== --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-gray-400">No.
                                </th>
                                <th class="px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-gray-400">Posisi
                                    Magang</th>
                                <th
                                    class="px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-gray-400 text-center">
                                    Tanggal Pengajuan</th>
                                <th
                                    class="px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-gray-400 text-center">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">

                            @forelse($applications as $index => $app)
                                @php
                                    // FIX: Tambahkan warna aksen untuk completed & resigned
                                    $rowAccent = match ($app->status) {
                                        'accepted' => 'border-l-4 border-l-emerald-400',
                                        'completed' => 'border-l-4 border-l-teal-400',
                                        'rejected' => 'border-l-4 border-l-red-400',
                                        'resigned' => 'border-l-4 border-l-orange-400',
                                        default => 'border-l-4 border-l-amber-400',
                                    };
                                @endphp

                                <tr class="hover:bg-gray-50/80 transition-colors {{ $rowAccent }}">

                                    {{-- No --}}
                                    <td class="px-6 py-4 text-sm text-gray-400 font-medium w-12">
                                        {{ $index + 1 }}
                                    </td>

                                    {{-- Posisi --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                                <i class="bi bi-briefcase text-blue-500 text-sm"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-800 text-sm block leading-tight">
                                                    {{ $app->vacancy->title ?? 'Posisi Terhapus' }}
                                                </span>
                                                <span class="text-xs text-gray-400 mt-0.5 block">
                                                    Dinas Tenaga Kerja Prov. Jatim
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm text-gray-600 font-medium block">
                                            {{ $app->created_at->format('d F Y') }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $app->created_at->format('H:i') }} WIB
                                        </span>
                                    </td>

                                    {{-- Status Badge — DESKTOP --}}
                                    {{-- FIX: Tambahkan @elseif untuk 'completed' dan 'resigned'
                                         Sebelumnya keduanya jatuh ke @else → tampil "Menunggu Review" --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($app->status == 'accepted')
                                            <span
                                                class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wide border border-emerald-200 flex items-center gap-1.5 justify-center w-fit mx-auto">
                                                <i class="bi bi-check-circle-fill text-[10px]"></i> Diterima
                                            </span>
                                        @elseif ($app->status == 'completed')
                                            <span
                                                class="bg-teal-50 text-teal-600 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wide border border-teal-200 flex items-center gap-1.5 justify-center w-fit mx-auto">
                                                <i class="bi bi-award-fill text-[10px]"></i> Selesai
                                            </span>
                                        @elseif ($app->status == 'rejected')
                                            <span
                                                class="bg-red-50 text-red-500 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wide border border-red-200 flex items-center gap-1.5 justify-center w-fit mx-auto">
                                                <i class="bi bi-x-circle-fill text-[10px]"></i> Ditolak
                                            </span>
                                        @elseif ($app->status == 'resigned')
                                            <span
                                                class="bg-orange-50 text-orange-500 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wide border border-orange-200 flex items-center gap-1.5 justify-center w-fit mx-auto">
                                                <i class="bi bi-person-walking text-[10px]"></i> Mengundurkan Diri
                                            </span>
                                        @elseif ($app->status == 'interview')
                                            <span
                                                class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wide border border-blue-200 flex items-center gap-1.5 justify-center w-fit mx-auto">
                                                <i class="bi bi-camera-video-fill text-[10px]"></i> Interview
                                            </span>
                                        @elseif ($app->status == 'verified')
                                            <span
                                                class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wide border border-indigo-200 flex items-center gap-1.5 justify-center w-fit mx-auto">
                                                <i class="bi bi-shield-check text-[10px]"></i> Terverifikasi
                                            </span>
                                        @else
                                            {{-- pending atau status tak terduga --}}
                                            <span
                                                class="bg-amber-50 text-amber-600 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wide border border-amber-200 flex items-center gap-1.5 justify-center w-fit mx-auto">
                                                <i class="bi bi-clock-fill text-[10px]"></i> Menunggu Review
                                            </span>
                                        @endif
                                    </td>

                                </tr>

                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <i class="bi bi-inbox text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-semibold text-sm">Belum ada riwayat lamaran</p>
                                        <p class="text-gray-400 text-xs mt-1 mb-5">Kamu belum pernah mengajukan lamaran
                                            magang.</p>
                                        <a href="{{ route('dashboard.index') }}"
                                            class="inline-flex items-center gap-2 bg-blue-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-blue-700 transition shadow-sm shadow-blue-600/20">
                                            <i class="bi bi-search"></i> Lihat Lowongan
                                        </a>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- ===================== MOBILE: CARD LIST ===================== --}}
                <div class="md:hidden divide-y divide-gray-50">

                    @forelse($applications as $index => $app)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between gap-3 mb-3">

                                {{-- Info posisi --}}
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                        <i class="bi bi-briefcase text-blue-500 text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-800 text-sm leading-tight truncate">
                                            {{ $app->vacancy->title ?? 'Posisi Terhapus' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">Disnakertrans Prov. Jatim</p>
                                    </div>
                                </div>

                                {{-- Badge status — MOBILE --}}
                                {{-- FIX: Sama seperti desktop, tambahkan case completed & resigned --}}
                                @if ($app->status == 'accepted')
                                    <span
                                        class="shrink-0 bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border border-emerald-200 flex items-center gap-1">
                                        <i class="bi bi-check-circle-fill"></i> Diterima
                                    </span>
                                @elseif ($app->status == 'completed')
                                    <span
                                        class="shrink-0 bg-teal-50 text-teal-600 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border border-teal-200 flex items-center gap-1">
                                        <i class="bi bi-award-fill"></i> Selesai
                                    </span>
                                @elseif ($app->status == 'rejected')
                                    <span
                                        class="shrink-0 bg-red-50 text-red-500 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border border-red-200 flex items-center gap-1">
                                        <i class="bi bi-x-circle-fill"></i> Ditolak
                                    </span>
                                @elseif ($app->status == 'resigned')
                                    <span
                                        class="shrink-0 bg-orange-50 text-orange-500 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border border-orange-200 flex items-center gap-1">
                                        <i class="bi bi-person-walking"></i> Mundur
                                    </span>
                                @elseif ($app->status == 'interview')
                                    <span
                                        class="shrink-0 bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border border-blue-200 flex items-center gap-1">
                                        <i class="bi bi-camera-video-fill"></i> Interview
                                    </span>
                                @elseif ($app->status == 'verified')
                                    <span
                                        class="shrink-0 bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border border-indigo-200 flex items-center gap-1">
                                        <i class="bi bi-shield-check"></i> Terverifikasi
                                    </span>
                                @else
                                    {{-- pending atau status tak terduga --}}
                                    <span
                                        class="shrink-0 bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border border-amber-200 flex items-center gap-1">
                                        <i class="bi bi-clock-fill"></i> Review
                                    </span>
                                @endif

                            </div>

                            {{-- Tanggal di bawah --}}
                            <div class="flex items-center gap-1.5 text-xs text-gray-400 pl-12">
                                <i class="bi bi-calendar3 text-[10px]"></i>
                                {{ $app->created_at->format('d F Y, H:i') }} WIB
                            </div>
                        </div>

                    @empty
                        <div class="px-6 py-16 text-center">
                            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-inbox text-2xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-semibold text-sm">Belum ada riwayat lamaran</p>
                            <p class="text-gray-400 text-xs mt-1 mb-4">Kamu belum pernah mengajukan lamaran.</p>
                            <a href="{{ route('dashboard.index') }}"
                                class="inline-flex items-center gap-2 bg-blue-600 text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-blue-700 transition">
                                <i class="bi bi-search"></i> Lihat Lowongan
                            </a>
                        </div>
                    @endforelse

                </div>

            </div>

            {{-- ===================== KETERANGAN STATUS ===================== --}}
            {{-- FIX: Tambahkan badge Selesai & Mengundurkan Diri agar legend lengkap --}}
            <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Keterangan Status</p>
                <div class="flex flex-wrap gap-3">
                    <span
                        class="bg-amber-50 text-amber-600 px-3 py-1.5 rounded-full text-[11px] font-bold border border-amber-200 flex items-center gap-1.5">
                        <i class="bi bi-clock-fill text-[10px]"></i> Menunggu Review
                    </span>
                    <span
                        class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-full text-[11px] font-bold border border-indigo-200 flex items-center gap-1.5">
                        <i class="bi bi-shield-check text-[10px]"></i> Terverifikasi — Sedang diproses admin
                    </span>
                    <span
                        class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full text-[11px] font-bold border border-blue-200 flex items-center gap-1.5">
                        <i class="bi bi-camera-video-fill text-[10px]"></i> Interview — Menunggu sesi interview
                    </span>
                    <span
                        class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full text-[11px] font-bold border border-emerald-200 flex items-center gap-1.5">
                        <i class="bi bi-check-circle-fill text-[10px]"></i> Diterima — Selamat!
                    </span>
                    <span
                        class="bg-teal-50 text-teal-600 px-3 py-1.5 rounded-full text-[11px] font-bold border border-teal-200 flex items-center gap-1.5">
                        <i class="bi bi-award-fill text-[10px]"></i> Selesai — Magang telah rampung
                    </span>
                    <span
                        class="bg-red-50 text-red-500 px-3 py-1.5 rounded-full text-[11px] font-bold border border-red-200 flex items-center gap-1.5">
                        <i class="bi bi-x-circle-fill text-[10px]"></i> Ditolak
                    </span>
                    <span
                        class="bg-orange-50 text-orange-500 px-3 py-1.5 rounded-full text-[11px] font-bold border border-orange-200 flex items-center gap-1.5">
                        <i class="bi bi-person-walking text-[10px]"></i> Mengundurkan Diri
                    </span>
                </div>
            </div>

        </div>
    </div>
@endsection
