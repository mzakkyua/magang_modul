@extends('layouts.admin')

@section('title', 'Data Pegawai & Hak Akses')

@section('content')

    {{-- ===================== PAGE HEADER ===================== --}}
    <div class="flex items-center justify-between mb-6">

        <div>
            <p class="text-blue-600 font-bold uppercase tracking-widest text-xs mb-1 flex items-center gap-1.5">
                <i class="bi bi-people"></i>
                Kepegawaian
            </p>

            <h1 class="text-2xl font-extrabold text-gray-900">
                Manajemen Pegawai
            </h1>

            <p class="text-gray-400 text-sm mt-0.5">
                Kelola data pegawai dan hak akses Modul Magang.
            </p>
        </div>

    </div>

    {{-- ===================== TABLE ===================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">

            <div class="flex items-center gap-2">

                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-person-badge text-blue-600 text-xs"></i>
                </div>

                <h2 class="font-bold text-gray-800 text-sm">
                    Semua Pegawai
                </h2>

            </div>

            <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1 rounded-full font-medium">
                {{ $pegawai->total() }} pegawai
            </span>

        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">

            <table class="min-w-full">

                {{-- TABLE HEAD --}}
                <thead>

                    <tr class="bg-gray-50 border-b border-gray-100">

                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Informasi Pegawai
                        </th>

                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Status Akses
                        </th>

                        <th class="px-6 py-3.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            Kelola Hak Akses
                        </th>

                    </tr>

                </thead>

                {{-- TABLE BODY --}}
                <tbody class="divide-y divide-gray-50">

                    @foreach ($pegawai as $user)
                        <tr class="hover:bg-gray-50/70 transition">

                            {{-- ===================================================== --}}
                            {{-- INFO PEGAWAI --}}
                            {{-- ===================================================== --}}
                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    {{-- Avatar --}}
                                    <div
                                        class="w-10 h-10 rounded-xl bg-linear-to-br from-slate-100 to-slate-200
                                               flex items-center justify-center shrink-0 border border-slate-200">

                                        <span class="text-sm font-extrabold text-slate-500">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>

                                    </div>

                                    {{-- Identity --}}
                                    <div class="min-w-0">

                                        <p class="font-bold text-sm text-gray-900 truncate">
                                            {{ $user->name }}
                                        </p>

                                        <p class="text-xs text-gray-400 truncate mt-0.5">
                                            {{ $user->email }}
                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- ===================================================== --}}
                            {{-- STATUS AKSES --}}
                            {{-- ===================================================== --}}
                            <td class="px-6 py-4">

                                @if ($user->magangAccessRight)
                                    {{-- SUPERADMIN --}}
                                    @if ($user->magangAccessRight->role === 'superadmin')
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                                   bg-purple-50 border border-purple-100">

                                            <div
                                                class="w-7 h-7 rounded-lg bg-purple-100
                                                       flex items-center justify-center">

                                                <i class="bi bi-shield-fill-check text-purple-600 text-xs"></i>

                                            </div>

                                            <div>

                                                <p class="text-[11px] font-extrabold text-purple-700 uppercase">
                                                    Superadmin
                                                </p>

                                                <p class="text-[10px] text-purple-400">
                                                    Akses seluruh divisi
                                                </p>

                                            </div>

                                        </div>
                                    @else
                                        {{-- ADMIN BIDANG --}}
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                                   bg-blue-50 border border-blue-100">

                                            <div
                                                class="w-7 h-7 rounded-lg bg-blue-100
                                                       flex items-center justify-center">

                                                <i class="bi bi-building text-blue-600 text-xs"></i>

                                            </div>

                                            <div>

                                                <p class="text-[11px] font-extrabold text-blue-700 uppercase">
                                                    Admin Divisi
                                                </p>

                                                <p class="text-[10px] text-blue-400">
                                                    {{ $user->magangAccessRight->division_name }}
                                                </p>

                                            </div>

                                        </div>
                                    @endif
                                @else
                                    {{-- NO ACCESS --}}
                                    <div
                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                               bg-gray-50 border border-gray-200">

                                        <div
                                            class="w-7 h-7 rounded-lg bg-gray-100
                                                   flex items-center justify-center">

                                            <i class="bi bi-dash-circle text-gray-400 text-xs"></i>

                                        </div>

                                        <div>

                                            <p class="text-[11px] font-bold text-gray-500 uppercase">
                                                Tidak Ada Akses
                                            </p>

                                            <p class="text-[10px] text-gray-400">
                                                Belum memiliki role
                                            </p>

                                        </div>

                                    </div>
                                @endif

                            </td>

                            {{-- ===================================================== --}}
                            {{-- FORM AKSES --}}
                            {{-- ===================================================== --}}
                            <td class="px-6 py-4">

                                <div class="flex flex-col gap-2">

                                    {{-- FORM UPDATE --}}
                                    <form id="form-access-{{ $user->id }}"
                                        action="{{ route('admin.pegawai.access.store', $user->id) }}" method="POST">

                                        @csrf

                                        <div class="flex items-center gap-2 flex-wrap">

                                            {{-- ROLE --}}
                                            <select name="role"
                                                class="role-select border border-gray-200 rounded-xl px-3 py-2 text-xs
                                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                                       text-gray-700 outline-none bg-white transition w-40">

                                                <option value="" disabled
                                                    {{ !$user->magangAccessRight ? 'selected' : '' }}>
                                                    — Pilih Role —
                                                </option>

                                                <option value="admin_bidang"
                                                    {{ ($user->magangAccessRight->role ?? '') == 'admin_bidang' ? 'selected' : '' }}>
                                                    Admin Divisi
                                                </option>

                                                <option value="superadmin"
                                                    {{ ($user->magangAccessRight->role ?? '') == 'superadmin' ? 'selected' : '' }}>
                                                    Superadmin
                                                </option>

                                            </select>

                                            {{-- DIVISION --}}
                                            <select name="division_name"
                                                class="division-select border border-gray-200 rounded-xl px-3 py-2 text-xs
                                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                                       text-gray-700 outline-none bg-white transition w-52
                                                       {{ ($user->magangAccessRight->role ?? '') === 'superadmin' ? 'opacity-50 pointer-events-none bg-gray-100' : '' }}">

                                                <option value="">
                                                    — Pilih Divisi —
                                                </option>

                                                @foreach ($divisions as $division)
                                                    <option value="{{ $division->name }}"
                                                        {{ ($user->magangAccessRight->division_name ?? '') === $division->name ? 'selected' : '' }}>
                                                        {{ $division->name }}
                                                    </option>
                                                @endforeach

                                            </select>

                                            {{-- SUBMIT --}}
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700
                                                       text-white px-4 py-2 rounded-xl text-xs font-bold transition-all
                                                       shadow-sm shadow-blue-600/20">

                                                <i
                                                    class="bi bi-{{ $user->magangAccessRight ? 'arrow-repeat' : 'plus-lg' }} text-[10px]"></i>

                                                {{ $user->magangAccessRight ? 'Update Akses' : 'Beri Akses' }}

                                            </button>

                                        </div>

                                    </form>

                                    {{-- FORM REVOKE --}}
                                    @if ($user->magangAccessRight)
                                        <form action="{{ route('admin.pegawai.access.destroy', $user->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin mencabut hak akses pegawai ini?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-500
                                                       hover:text-white text-red-500 border border-red-200
                                                       hover:border-red-500 px-4 py-2 rounded-xl text-xs
                                                       font-bold transition-all">

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

        {{-- PAGINATION --}}
        @if ($pegawai->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">

                {{ $pegawai->appends(request()->query())->links() }}

            </div>
        @endif

    </div>

    {{-- ===================== SCRIPT ===================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const rows = document.querySelectorAll('tr');

            rows.forEach(row => {

                const roleSelect = row.querySelector('.role-select');
                const divisionSelect = row.querySelector('.division-select');

                if (!roleSelect || !divisionSelect) {
                    return;
                }

                function toggleDivisionField() {

                    const isSuperadmin = roleSelect.value === 'superadmin';

                    if (isSuperadmin) {

                        divisionSelect.value = '';

                        divisionSelect.classList.add(
                            'opacity-50',
                            'pointer-events-none',
                            'bg-gray-100'
                        );

                    } else {

                        divisionSelect.classList.remove(
                            'opacity-50',
                            'pointer-events-none',
                            'bg-gray-100'
                        );

                    }
                }

                toggleDivisionField();

                roleSelect.addEventListener('change', toggleDivisionField);

            });

        });
    </script>

@endsection
