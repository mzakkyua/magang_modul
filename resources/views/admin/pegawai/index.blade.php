@extends('layouts.admin')

@section('title', 'Data Pegawai & Hak Akses')

@section('content')

    {{-- ===================== PAGE HEADER ===================== --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-blue-600 font-bold uppercase tracking-widest text-xs mb-1 flex items-center gap-1.5">
                <i class="bi bi-people"></i> Kepegawaian
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Manajemen Pegawai</h1>
            <p class="text-gray-400 text-sm mt-0.5">Kelola data pegawai dan berikan hak akses untuk mengelola Modul Magang.
            </p>
        </div>
    </div>

    {{-- ===================== TABEL ===================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Table Header Bar --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-person-badge text-blue-600 text-xs"></i>
                </div>
                <h2 class="font-bold text-gray-800 text-sm">Semua Pegawai</h2>
            </div>
            <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1 rounded-full font-medium">
                {{ $pegawai->total() }} pegawai
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Informasi Pegawai
                        </th>
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Status Akses Saat Ini
                        </th>
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Kelola Hak Akses Magang
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    @foreach ($pegawai as $user)
                        <tr class="hover:bg-gray-50/80 transition-colors">

                            {{-- 1. INFO PEGAWAI --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-linear-to-br from-slate-100 to-slate-200 flex items-center justify-center shrink-0 border border-slate-200 text-sm font-extrabold text-slate-500">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-gray-900 leading-tight">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- 2. BADGE STATUS AKSES --}}
                            <td class="px-6 py-4">
                                @if ($user->magangAccess)
                                    @if ($user->magangAccess->role == 'superadmin')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-purple-50 text-purple-700 text-[11px] font-bold border border-purple-200">
                                            <i class="bi bi-shield-fill-check text-[10px]"></i>
                                            SUPERADMIN
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold border border-blue-200">
                                            <i class="bi bi-building text-[10px]"></i>
                                            ADMIN — {{ strtoupper($user->magangAccess->division_name) }}
                                        </span>
                                    @endif
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-gray-100 text-gray-400 text-[11px] font-semibold border border-gray-200">
                                        <i class="bi bi-dash-circle text-[10px]"></i>
                                        Tidak Ada Akses
                                    </span>
                                @endif
                            </td>

                            {{-- 3. KONTROL AKSI (FORM INLINE) --}}
                            {{-- Logic form tidak berubah sama sekali --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 flex-nowrap">

                                    {{-- Select Role --}}
                                    <select name="role" form="form-access-{{ $user->id }}"
                                        class="border border-gray-200 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 outline-none bg-white transition w-32 shrink-0">
                                        <option value="" disabled {{ !$user->magangAccess ? 'selected' : '' }}>
                                            — Role —
                                        </option>
                                        <option value="admin"
                                            {{ ($user->magangAccess->role ?? '') == 'admin' ? 'selected' : '' }}>
                                            Admin Divisi
                                        </option>
                                        <option value="superadmin"
                                            {{ ($user->magangAccess->role ?? '') == 'superadmin' ? 'selected' : '' }}>
                                            Superadmin
                                        </option>
                                    </select>

                                    {{-- Input Nama Divisi --}}
                                    <input type="text" name="division_name" form="form-access-{{ $user->id }}"
                                        placeholder="Nama Divisi" value="{{ $user->magangAccess->division_name ?? '' }}"
                                        class="border border-gray-200 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 outline-none w-28 shrink-0 transition placeholder-gray-300">

                                    {{-- Form Tambah/Ubah Akses --}}
                                    <form id="form-access-{{ $user->id }}"
                                        action="{{ route('admin.pegawai.access.store', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-600/20 shrink-0 whitespace-nowrap">
                                            <i
                                                class="bi bi-{{ $user->magangAccess ? 'arrow-repeat' : 'plus-lg' }} text-[10px]"></i>
                                            {{ $user->magangAccess ? 'Update' : 'Beri Akses' }}
                                        </button>
                                    </form>

                                    {{-- Form Cabut Akses --}}
                                    {{-- Muncul HANYA jika pegawai sudah punya akses — logic tidak berubah --}}
                                    @if ($user->magangAccess)
                                        <form action="{{ route('admin.pegawai.access.destroy', $user->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin mencabut hak akses magang pegawai ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-500 hover:text-white text-red-500 border border-red-200 hover:border-red-500 px-3 py-2 rounded-xl text-xs font-bold transition-all shrink-0 whitespace-nowrap">
                                                <i class="bi bi-x-lg text-[10px]"></i>
                                                Cabut Akses
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($pegawai->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $pegawai->appends(request()->query())->links() }}
            </div>
        @endif

    </div>

    {{-- ===================== KETERANGAN ROLE ===================== --}}
    <div class="mt-5 bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4">
        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Keterangan Hak Akses</p>
        <div class="flex flex-wrap gap-3">
            <div class="flex items-start gap-2.5 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 max-w-xs">
                <span
                    class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100 text-gray-400 text-[10px] font-bold border border-gray-200 shrink-0 mt-0.5">
                    <i class="bi bi-dash-circle text-[9px]"></i> Tidak Ada
                </span>
                <p class="text-xs text-gray-500 leading-relaxed">Pegawai tidak bisa mengakses fitur Modul Magang sama
                    sekali.</p>
            </div>
            <div class="flex items-start gap-2.5 bg-blue-50/50 border border-blue-100 rounded-xl px-4 py-3 max-w-xs">
                <span
                    class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200 shrink-0 mt-0.5">
                    <i class="bi bi-building text-[9px]"></i> Admin
                </span>
                <p class="text-xs text-gray-500 leading-relaxed">Hanya bisa mengelola data dari divisinya sendiri.</p>
            </div>
            <div class="flex items-start gap-2.5 bg-purple-50/50 border border-purple-100 rounded-xl px-4 py-3 max-w-xs">
                <span
                    class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-purple-50 text-purple-700 text-[10px] font-bold border border-purple-200 shrink-0 mt-0.5">
                    <i class="bi bi-shield-fill-check text-[9px]"></i> Superadmin
                </span>
                <p class="text-xs text-gray-500 leading-relaxed">Akses penuh ke seluruh data magang dari semua divisi.</p>
            </div>
        </div>
    </div>

@endsection
