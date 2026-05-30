@extends('layouts.admin')

@section('title', 'Data Pegawai & Hak Akses')

@section('content')

    {{-- ================================================================
     SVG ICONS — didefinisikan sekali, dipakai ulang
     Tidak bergantung pada Bootstrap Icons font sama sekali
     ================================================================ --}}
    @php
        // SVG icons sebagai string — selalu render, tidak perlu font load
        $svgShield =
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M8 1.5a.5.5 0 0 1 .5.5v.5A3 3 0 0 1 11 5.5V8a3 3 0 0 1-2.5 2.958V12h1.5a.5.5 0 0 1 0 1h-4a.5.5 0 0 1 0-1H7.5v-1.042A3 3 0 0 1 5 8V5.5A3 3 0 0 1 7.5 2.5V2a.5.5 0 0 1 .5-.5z" clip-rule="evenodd"/><path d="M8 0C5.56 0 4 1.791 4 4v4a4 4 0 0 0 8 0V4c0-2.209-1.56-4-4-4z"/></svg>';

        $svgBuilding =
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5"><path d="M4 2.5A1.5 1.5 0 0 1 5.5 1h5A1.5 1.5 0 0 1 12 2.5v11H4v-11zM5.5 2a.5.5 0 0 0-.5.5V13h7V2.5a.5.5 0 0 0-.5-.5h-5z"/><path d="M2 13h12v1H2z"/><rect x="6" y="4" width="1" height="2"/><rect x="9" y="4" width="1" height="2"/><rect x="6" y="7" width="1" height="2"/><rect x="9" y="7" width="1" height="2"/><rect x="7" y="10" width="2" height="3"/></svg>';

        $svgDash =
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M5 8h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
    @endphp

    {{-- ================================================================
     PAGE HEADER
     ================================================================ --}}
    <div class="mb-8">

        <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-500 flex items-center gap-2 mb-2">
            <span class="inline-flex w-5 h-5 rounded-md bg-indigo-100 items-center justify-center">
                <i class="bi bi-people-fill text-[10px]"></i>
            </span>
            Kepegawaian
        </p>

        <div class="flex items-end justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                    Manajemen Pegawai
                </h1>
                <p class="text-gray-400 text-sm mt-1">
                    Kelola data pegawai dan hak akses Modul Magang.
                </p>
            </div>

            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-2xl px-4 py-2.5 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="text-sm font-semibold text-gray-700">{{ $pegawai->total() }} Pegawai</span>
            </div>
        </div>

    </div>

    {{-- ================================================================
     PEGAWAI CARDS
     ================================================================ --}}
    <div class="space-y-3">

        @foreach ($pegawai as $user)
            @php
                $role = $user->magangAccessRight->role ?? null;
                $isSuperadmin = $role === 'superadmin';
                $isAdminBidang = $role === 'admin_bidang';
                $hasAccess = !is_null($role);

                $initial = strtoupper(substr($user->name, 0, 1));

                // Palet warna avatar — deterministik per huruf awal
                $palettes = [
                    'A' => ['bg' => '#EEF2FF', 'text' => '#4338CA'],
                    'B' => ['bg' => '#FFF7ED', 'text' => '#C2410C'],
                    'C' => ['bg' => '#F0FDF4', 'text' => '#15803D'],
                    'D' => ['bg' => '#FDF4FF', 'text' => '#7E22CE'],
                    'E' => ['bg' => '#FFF1F2', 'text' => '#BE123C'],
                    'F' => ['bg' => '#ECFEFF', 'text' => '#0E7490'],
                    'G' => ['bg' => '#FEFCE8', 'text' => '#A16207'],
                    'H' => ['bg' => '#F0F9FF', 'text' => '#0369A1'],
                    'I' => ['bg' => '#F0FDF4', 'text' => '#166534'],
                    'J' => ['bg' => '#FFF1F2', 'text' => '#9F1239'],
                    'K' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8'],
                    'L' => ['bg' => '#FDF4FF', 'text' => '#86198F'],
                    'M' => ['bg' => '#FFF7ED', 'text' => '#9A3412'],
                    'N' => ['bg' => '#F0FDFA', 'text' => '#0F766E'],
                    'O' => ['bg' => '#FEFCE8', 'text' => '#854D0E'],
                    'P' => ['bg' => '#EEF2FF', 'text' => '#3730A3'],
                ];
                $color = $palettes[$initial] ?? ['bg' => '#F1F5F9', 'text' => '#475569'];
            @endphp

            <div
                class="bg-white rounded-2xl border border-gray-100
                hover:border-indigo-100 hover:shadow-lg hover:shadow-indigo-500/5
                transition-all duration-200">

                <div class="flex items-center p-5 gap-0">

                    {{-- =========================================================
                 KOLOM 1 — IDENTITAS
                 ========================================================= --}}
                    <div class="flex items-center gap-4 w-72 shrink-0">

                        {{-- Avatar dengan status dot --}}
                        <div class="relative shrink-0">

                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center
                                font-black text-lg shadow-sm select-none"
                                style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                {{ $initial }}
                            </div>

                            {{-- Status dot — pakai warna saja, tidak perlu icon di dalam --}}
                            @if ($isSuperadmin)
                                <span
                                    class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full
                                     bg-violet-500 border-2 border-white shadow-sm"
                                    title="Superadmin"></span>
                            @elseif ($isAdminBidang)
                                <span
                                    class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full
                                     bg-blue-500 border-2 border-white shadow-sm"
                                    title="Admin Divisi"></span>
                            @else
                                <span
                                    class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full
                                     bg-gray-300 border-2 border-white shadow-sm"
                                    title="Tanpa Akses"></span>
                            @endif

                        </div>

                        {{-- Nama & Email --}}
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 text-sm truncate leading-tight">
                                {{ $user->name }}
                            </p>
                            <p class="text-xs text-gray-400 truncate mt-0.5">
                                {{ $user->email }}
                            </p>
                        </div>

                    </div>

                    {{-- Divider --}}
                    <div class="w-px h-12 bg-gray-100 mx-6 shrink-0"></div>

                    {{-- =========================================================
                 KOLOM 2 — STATUS BADGE
                 Menggunakan inline SVG — tidak bergantung Bootstrap Icons font
                 ========================================================= --}}
                    <div class="w-56 shrink-0">

                        @if ($isSuperadmin)
                            <div
                                class="inline-flex items-center gap-2.5
                                bg-violet-50 border border-violet-200/80
                                rounded-xl px-3 py-2.5">

                                {{-- Icon shield — inline SVG --}}
                                <span class="text-violet-600 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-4 h-4">
                                        <path fill-rule="evenodd"
                                            d="M9.661 2.237a.531.531 0 0 1 .678 0 11.947 11.947 0 0 0 7.078 2.749.5.5 0 0 1 .479.425c.069.52.104 1.05.104 1.589 0 5.162-3.26 9.563-7.834 11.256a.48.48 0 0 1-.332 0C5.26 16.563 2 12.162 2 7c0-.538.035-1.069.104-1.589a.5.5 0 0 1 .48-.425 11.947 11.947 0 0 0 7.077-2.749Zm4.196 5.954a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>

                                <div>
                                    <p
                                        class="text-[11px] font-extrabold text-violet-700 uppercase tracking-wide leading-none">
                                        Superadmin
                                    </p>
                                    <p class="text-[10px] text-violet-400 mt-0.5 leading-none">
                                        Akses seluruh divisi
                                    </p>
                                </div>

                            </div>
                        @elseif ($isAdminBidang)
                            <div
                                class="inline-flex items-center gap-2.5
                                bg-blue-50 border border-blue-200/80
                                rounded-xl px-3 py-2.5">

                                {{-- Icon building — inline SVG --}}
                                <span class="text-blue-600 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-4 h-4">
                                        <path fill-rule="evenodd"
                                            d="M4 16.5v-13h-.25a.75.75 0 0 1 0-1.5h12.5a.75.75 0 0 1 0 1.5H16v13h.25a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1-.75-.75v-2.5a.75.75 0 0 0-.75-.75h-2.5a.75.75 0 0 0-.75.75v2.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0-1.5H4Zm3-11a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm.5 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm3.5-3.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm.5 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <p
                                        class="text-[11px] font-extrabold text-blue-700 uppercase tracking-wide leading-none">
                                        Admin Divisi
                                    </p>
                                    <p class="text-[10px] text-blue-400 mt-0.5 leading-none truncate max-w-27.5">
                                        {{ $user->magangAccessRight->division_name }}
                                    </p>
                                </div>

                            </div>
                        @else
                            <div
                                class="inline-flex items-center gap-2.5
                                bg-gray-50 border border-gray-200/80
                                rounded-xl px-3 py-2.5">

                                {{-- Icon minus circle — inline SVG --}}
                                <span class="text-gray-400 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-4 h-4">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM6.75 9.25a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>

                                <div>
                                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wide leading-none">
                                        Tanpa Akses
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 leading-none">
                                        Belum ada role
                                    </p>
                                </div>

                            </div>
                        @endif

                    </div>

                    {{-- Divider --}}
                    <div class="w-px h-12 bg-gray-100 mx-6 shrink-0"></div>

                    {{-- =========================================================
                 KOLOM 3 — FORM KELOLA AKSES
                 ========================================================= --}}
                    <div class="flex-1 min-w-0">

                        <form id="form-access-{{ $user->id }}"
                            action="{{ route('admin.pegawai.access.store', $user->id) }}" method="POST">

                            @csrf

                            <div class="flex items-center gap-2 flex-wrap">

                                {{-- Role Select --}}
                                <div class="relative">
                                    <select name="role"
                                        class="role-select appearance-none bg-gray-50 border border-gray-200
                                           rounded-xl pl-3 pr-8 py-2.5 text-xs font-semibold text-gray-700
                                           focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400
                                           outline-none transition cursor-pointer w-40">

                                        <option value="" disabled {{ !$hasAccess ? 'selected' : '' }}>
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
                                    {{-- Chevron pakai inline SVG --}}
                                    <span
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="none"
                                            stroke="currentColor" stroke-width="2" class="w-2.5 h-2.5">
                                            <path d="M2 4l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>

                                {{-- Division Select --}}
                                <div class="relative">
                                    <select name="division_name"
                                        class="division-select appearance-none bg-gray-50 border border-gray-200
                                           rounded-xl pl-3 pr-8 py-2.5 text-xs font-semibold text-gray-700
                                           focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400
                                           outline-none transition cursor-pointer w-56
                                           {{ $isSuperadmin ? 'opacity-40 pointer-events-none' : '' }}">

                                        <option value="">— Pilih Divisi —</option>

                                        @foreach ($divisions as $division)
                                            <option value="{{ $division->name }}"
                                                {{ ($user->magangAccessRight->division_name ?? '') === $division->name ? 'selected' : '' }}>
                                                {{ $division->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span
                                        class="division-chevron absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400
                                         {{ $isSuperadmin ? 'opacity-40' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="none"
                                            stroke="currentColor" stroke-width="2" class="w-2.5 h-2.5">
                                            <path d="M2 4l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>

                                {{-- Submit --}}
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl
                                       text-xs font-bold transition-all duration-150 shadow-sm
                                       {{ $hasAccess
                                           ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-500/20'
                                           : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-500/20' }}">

                                    {{-- Icon refresh / plus — inline SVG --}}
                                    @if ($hasAccess)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                            class="w-3 h-3">
                                            <path fill-rule="evenodd"
                                                d="M13.836 2.477a.75.75 0 0 1 .75.75v3.182a.75.75 0 0 1-.75.75h-3.182a.75.75 0 0 1 0-1.5h1.37l-.84-.841a4.5 4.5 0 0 0-7.08.932.75.75 0 0 1-1.3-.75 6 6 0 0 1 9.45-1.242l.842.84V3.227a.75.75 0 0 1 .75-.75Zm-.911 7.5A.75.75 0 0 1 13.199 11a6 6 0 0 1-9.45 1.241l-.84-.84v1.371a.75.75 0 0 1-1.5 0V9.591a.75.75 0 0 1 .75-.75H5.35a.75.75 0 0 1 0 1.5H3.98l.841.841a4.5 4.5 0 0 0 7.08-.932.75.75 0 0 1 1.024-.273Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Update
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                            class="w-3 h-3">
                                            <path
                                                d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                        </svg>
                                        Beri Akses
                                    @endif

                                </button>

                                {{-- Cabut Akses --}}
                                @if ($hasAccess)
                                    <div class="w-px h-6 bg-gray-200 mx-1 shrink-0"></div>

                                    <form action="{{ route('admin.pegawai.access.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin mencabut hak akses pegawai ini?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl
                                               text-xs font-bold transition-all duration-150
                                               text-red-500 bg-red-50 border border-red-100
                                               hover:bg-red-500 hover:text-white hover:border-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                                                fill="currentColor" class="w-3 h-3">
                                                <path
                                                    d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />
                                            </svg>
                                            Cabut
                                        </button>
                                    </form>
                                @endif

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        @endforeach

    </div>

    {{-- ================================================================
     PAGINATION
     ================================================================ --}}
    @if ($pegawai->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $pegawai->appends(request()->query())->links() }}
        </div>
    @endif

    {{-- ================================================================
     SCRIPT — TOGGLE DIVISION FIELD
     Logic identik dengan versi sebelumnya, tidak ada yang berubah
     ================================================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const cards = document.querySelectorAll('[id^="form-access-"]');

            cards.forEach(form => {

                const card = form.closest('.bg-white');
                const roleSelect = form.querySelector('.role-select');
                const divSelect = form.querySelector('.division-select');
                const divChevron = form.querySelector('.division-chevron');

                if (!roleSelect || !divSelect) return;

                function toggleDivisionField() {

                    const isSuperadmin = roleSelect.value === 'superadmin';

                    if (isSuperadmin) {
                        divSelect.value = '';
                        divSelect.classList.add('opacity-40', 'pointer-events-none');
                        if (divChevron) divChevron.classList.add('opacity-40');
                    } else {
                        divSelect.classList.remove('opacity-40', 'pointer-events-none');
                        if (divChevron) divChevron.classList.remove('opacity-40');
                    }
                }

                toggleDivisionField();
                roleSelect.addEventListener('change', toggleDivisionField);
            });
        });
    </script>

@endsection
