@extends('layouts.admin')

@section('title', 'Data Pegawai & Hak Akses')

@section('content')

    {{-- ================================================================
     PAGE HEADER
     ================================================================ --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-500 flex items-center gap-2 mb-2">
                <span class="inline-flex w-6 h-6 rounded-md bg-indigo-100 items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                        <path
                            d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" />
                    </svg>
                </span>
                Kepegawaian
            </p>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                Manajemen Pegawai
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Kelola data pegawai dan berikan hak akses Modul Magang.
            </p>
        </div>

        <div class="flex items-center gap-2.5 bg-white border border-gray-200 rounded-xl px-4 py-2.5 shadow-sm shrink-0">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-sm font-bold text-gray-700">{{ $pegawai->total() }} Total Pegawai</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 shadow-sm">
            <svg class="w-6 h-6 shrink-0 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"
                    clip-rule="evenodd" />
            </svg>
            <div class="pt-0.5">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ================================================================
     PEGAWAI CARDS
     ================================================================ --}}
    <div class="space-y-4">
        @foreach ($pegawai as $user)
            @php
                $role = $user->magangAccessRight->role ?? null;
                $divisionName = $user->magangAccessRight->division_name ?? '';

                $isSuperadmin = $role === \App\Models\MagangAccessRight::ROLE_SUPERADMIN;
                $isAdminBidang = $role === \App\Models\MagangAccessRight::ROLE_DIVISION_ADMIN;
                $hasAccess = !is_null($role);

                $initial = strtoupper(substr($user->name, 0, 1));
                $isMe = $user->id === Auth::id();

                // Palet warna deterministik
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
                class="bg-white rounded-2xl border border-gray-200 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300 overflow-hidden flex flex-col lg:flex-row lg:items-center">

                {{-- INFO PEGAWAI & STATUS --}}
                <div class="flex items-center justify-between p-5 lg:w-[45%] lg:border-r border-gray-100 bg-gray-50/30">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-lg shadow-sm select-none border border-black/5"
                                style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                {{ $initial }}
                            </div>
                            @if ($isSuperadmin)
                                <span
                                    class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-violet-500 border-2 border-white shadow-sm"
                                    title="Superadmin"></span>
                            @elseif ($isAdminBidang)
                                <span
                                    class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-blue-500 border-2 border-white shadow-sm"
                                    title="Admin Divisi"></span>
                            @else
                                <span
                                    class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-gray-300 border-2 border-white shadow-sm"
                                    title="Tanpa Akses"></span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 text-sm truncate leading-tight">{{ $user->name }}</p>
                            <p class="text-xs font-medium text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
                        </div>
                    </div>

                    {{-- Badge Status Mobile / LG --}}
                    <div class="shrink-0 ml-4 hidden sm:block">
                        @if ($isSuperadmin)
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center gap-1 text-[10px] font-extrabold text-violet-700 bg-violet-100 px-2 py-0.5 rounded uppercase tracking-wide">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-3 h-3">
                                        <path fill-rule="evenodd"
                                            d="M9.661 2.237a.531.531 0 0 1 .678 0 11.947 11.947 0 0 0 7.078 2.749.5.5 0 0 1 .479.425c.069.52.104 1.05.104 1.589 0 5.162-3.26 9.563-7.834 11.256a.48.48 0 0 1-.332 0C5.26 16.563 2 12.162 2 7c0-.538.035-1.069.104-1.589a.5.5 0 0 1 .48-.425 11.947 11.947 0 0 0 7.077-2.749Zm4.196 5.954a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Superadmin
                                </span>
                            </div>
                        @elseif ($isAdminBidang)
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center gap-1 text-[10px] font-extrabold text-blue-700 bg-blue-100 px-2 py-0.5 rounded uppercase tracking-wide">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-3 h-3">
                                        <path fill-rule="evenodd"
                                            d="M4 16.5v-13h-.25a.75.75 0 0 1 0-1.5h12.5a.75.75 0 0 1 0 1.5H16v13h.25a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1-.75-.75v-2.5a.75.75 0 0 0-.75-.75h-2.5a.75.75 0 0 0-.75.75v2.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0-1.5H4Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Admin Divisi
                                </span>
                                <p class="text-[10px] font-medium text-gray-500 mt-1 truncate max-w-30"
                                    title="{{ $divisionName }}">{{ $divisionName }}</p>
                            </div>
                        @else
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center gap-1 text-[10px] font-extrabold text-gray-500 bg-gray-100 px-2 py-0.5 rounded uppercase tracking-wide">Tanpa
                                    Akses</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- FORM CONTROL --}}
                <div class="flex-1 p-4 lg:p-5 relative">
                    @if ($isMe)
                        {{-- TAMPILAN JIKA INI ADALAH AKUN SUPERADMIN ITU SENDIRI --}}
                        <div class="flex items-center justify-end h-full w-full">
                            <div
                                class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl border border-indigo-100 shadow-sm cursor-default">
                                <svg class="w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span class="text-[11px] font-extrabold uppercase tracking-widest mt-0.5">Ini Akun
                                    Anda</span>
                            </div>
                        </div>
                    @else
                        {{-- FORM SEPERTI BIASA UNTUK PEGAWAI LAIN --}}
                        <form id="form-access-{{ $user->id }}"
                            action="{{ route('admin.pegawai.access.store', $user->id) }}" method="POST"
                            class="js-access-form flex flex-col sm:flex-row sm:items-center gap-3">
                            @csrf

                            <div class="flex flex-col sm:flex-row gap-3 flex-1">
                                {{-- Pilih Role --}}
                                <div class="relative w-full sm:w-44">
                                    <select name="role" data-original="{{ $role }}"
                                        class="js-input-role appearance-none w-full bg-white border border-gray-300 rounded-xl pl-4 pr-10 py-2.5 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition cursor-pointer shadow-sm">
                                        <option value="" {{ !$hasAccess ? 'selected' : '' }}>— Pilih Akses —</option>
                                        <option value="admin_bidang" {{ $isAdminBidang ? 'selected' : '' }}>Admin Divisi
                                        </option>
                                        <option value="superadmin" {{ $isSuperadmin ? 'selected' : '' }}>Superadmin
                                        </option>
                                    </select>
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>

                                {{-- Pilih Divisi --}}
                                <div class="relative flex-1">
                                    <select name="division_name" data-original="{{ $divisionName }}"
                                        class="js-input-division appearance-none w-full bg-white border border-gray-300 rounded-xl pl-4 pr-10 py-2.5 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition cursor-pointer shadow-sm {{ $isSuperadmin || !$hasAccess ? 'opacity-50 pointer-events-none bg-gray-50' : '' }}">
                                        <option value="">— Pilih Nama Divisi —</option>
                                        @foreach ($divisions as $division)
                                            <option value="{{ $division->name }}"
                                                {{ $divisionName === $division->name ? 'selected' : '' }}>
                                                {{ $division->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span
                                        class="js-chevron-division absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500 {{ $isSuperadmin || !$hasAccess ? 'opacity-50' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div
                                class="flex items-center gap-2 shrink-0 border-t sm:border-t-0 border-gray-100 pt-3 sm:pt-0 mt-1 sm:mt-0">

                                {{-- Save/Update Button (Disabled by default) --}}
                                <button type="submit" disabled
                                    class="js-btn-submit flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm disabled:opacity-50 disabled:bg-gray-100 disabled:text-gray-400 disabled:border-gray-200 disabled:cursor-not-allowed disabled:shadow-none {{ $hasAccess ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-200' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-200' }}">
                                    @if ($hasAccess)
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        <span>Update</span>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        <span>Beri Akses</span>
                                    @endif
                                </button>

                            </div>
                        </form>

                        {{-- Form Cabut Akses --}}
                        @if ($hasAccess)
                            <form action="{{ route('admin.pegawai.access.destroy', $user->id) }}" method="POST"
                                class="js-delete-form mt-2 sm:mt-0 sm:absolute sm:right-5 sm:top-5 lg:static lg:mt-3 flex justify-end">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="js-btn-delete inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-red-600 hover:bg-red-50 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    <span>Cabut Akses</span>
                                </button>
                            </form>
                        @endif
                    @endif
                </div>

            </div>
        @endforeach
    </div>

    {{-- ================================================================
     PAGINATION
     ================================================================ --}}
    @if ($pegawai->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $pegawai->appends(request()->query())->links() }}
        </div>
    @endif

    {{-- ================================================================
     JAVASCRIPT: State Management & Form Protection
     ================================================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const accessForms = document.querySelectorAll('.js-access-form');

            // Reusable spinner SVG
            const spinnerSVG =
                `<svg class="animate-spin w-4 h-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

            accessForms.forEach(form => {
                const roleInput = form.querySelector('.js-input-role');
                const divInput = form.querySelector('.js-input-division');
                const divChevron = form.querySelector('.js-chevron-division');
                const submitBtn = form.querySelector('.js-btn-submit');

                const origRole = roleInput.dataset.original;
                const origDiv = divInput.dataset.original;

                // Function to evaluate state
                const evaluateFormState = () => {
                    const currentRole = roleInput.value;
                    const currentDiv = divInput.value;
                    let isChanged = false;
                    let isValid = true;

                    // Logic 1: Toggle Division Field UX
                    if (currentRole === 'superadmin' || currentRole === '') {
                        divInput.classList.add('opacity-50', 'pointer-events-none', 'bg-gray-50');
                        divChevron.classList.add('opacity-50');
                        divInput.value = ''; // auto clear
                    } else {
                        divInput.classList.remove('opacity-50', 'pointer-events-none', 'bg-gray-50');
                        divChevron.classList.remove('opacity-50');
                    }

                    // Logic 2: Has it changed?
                    if (currentRole !== origRole) {
                        isChanged = true;
                    } else if (currentRole === 'admin_bidang' && currentDiv !== origDiv) {
                        isChanged = true;
                    }

                    if (currentRole === '') {
                        isValid = false;
                    }

                    // Logic 3: Is it valid? (Admin Bidang MUST select division)
                    if (currentRole === 'admin_bidang' && divInput.value === '') {
                        isValid = false;
                    }

                    // Enable button ONLY IF changed AND valid
                    if (isChanged && isValid) {
                        submitBtn.removeAttribute('disabled');
                    } else {
                        submitBtn.setAttribute('disabled', 'true');
                    }
                };

                // Listen to changes
                roleInput.addEventListener('change', evaluateFormState);
                divInput.addEventListener('change', evaluateFormState);

                // Handle Submit Spinner (Mencegah Double Submit)
                form.addEventListener('submit', function() {
                    submitBtn.setAttribute('disabled', 'true');
                    submitBtn.classList.add('opacity-75', 'cursor-wait');
                    submitBtn.innerHTML = spinnerSVG + ' <span>Memproses...</span>';
                });
            });

            // Handle Delete Form Spinner & SweetAlert replacement
            const deleteForms = document.querySelectorAll('.js-delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm(
                            'PERINGATAN: Yakin ingin mencabut hak akses pegawai ini? Mereka tidak akan bisa login ke dashboard admin.'
                        )) {
                        e.preventDefault();
                        return;
                    }

                    const btn = this.querySelector('.js-btn-delete');
                    btn.setAttribute('disabled', 'true');
                    btn.classList.add('opacity-50', 'cursor-wait');
                    btn.innerHTML = spinnerSVG + ' <span>Mencabut...</span>';
                });
            });

        });

        form.addEventListener('submit', function() {
            // Simpan konten asli tombol untuk di-restore jika gagal
            const originalHTML = submitBtn.innerHTML;
            submitBtn.setAttribute('disabled', 'true');
            submitBtn.innerHTML = spinnerSVG + ' <span>Memproses...</span>';

            // Timeout sebagai fallback jika response lambat/error
            setTimeout(() => {
                submitBtn.removeAttribute('disabled');
                submitBtn.innerHTML = originalHTML;
            }, 8000);
        });
    </script>

@endsection
