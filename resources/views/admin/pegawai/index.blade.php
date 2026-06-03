@extends('layouts.admin')

@section('title', 'Data Pegawai & Hak Akses')

@section('content')

    {{-- ================================================================
     PAGE HEADER
     ================================================================ --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
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
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Manajemen Pegawai</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola data pegawai dan berikan hak akses secara langsung.</p>
        </div>

        <div class="flex items-center gap-2.5 bg-white border border-gray-200 rounded-xl px-4 py-2.5 shadow-sm shrink-0">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-sm font-bold text-gray-700">{{ $pegawai->total() }} Total Pegawai</span>
        </div>
    </div>


    {{-- ================================================================
     TABEL PEGAWAI (GAYA EXCEL - INLINE EDIT)
     ================================================================ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-xs flex items-center gap-2">
                <i class="bi bi-table text-indigo-500"></i> Daftar Pegawai
            </h3>
            <span class="text-[10px] font-bold bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full">
                {{ $pegawai->count() }} baris
            </span>
        </div>

        {{-- WUJUD 1: TABEL DESKTOP --}}
        <div class="hidden lg:block w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-white border-b border-gray-100 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">
                        <th class="px-4 py-3 w-1/3">Pegawai</th>
                        <th class="px-4 py-3 w-1/4">Hak Akses</th>
                        <th class="px-4 py-3 w-1/4">Penempatan Divisi</th>
                        <th class="px-4 py-3 w-auto text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($pegawai as $user)
                        @php
                            $role = $user->magangAccessRight->role ?? '';
                            $divisionName = $user->magangAccessRight->division_name ?? '';
                            $hasAccess = $role !== '';
                            $initial = strtoupper(substr($user->name, 0, 1));
                            $isMe = $user->id === Auth::id();
                            $colors = [
                                'bg-indigo-100 text-indigo-700',
                                'bg-blue-100 text-blue-700',
                                'bg-emerald-100 text-emerald-700',
                                'bg-rose-100 text-rose-700',
                                'bg-violet-100 text-violet-700',
                            ];
                            $colorClass = $colors[ord($initial) % count($colors)];
                        @endphp

                        <tr class="hover:bg-gray-50/50 transition-colors js-access-row">
                            {{-- Info Pegawai --}}
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-xs shrink-0 border border-black/5 {{ $colorClass }}">
                                        {{ $initial }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 leading-tight truncate">
                                            {{ $user->name }}</p>
                                        <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kolom Dropdown: Jika bukan akun sendiri --}}
                            @if (!$isMe)
                                {{-- Form Rahasia Penghubung --}}
                                <form id="desk-edit-{{ $user->id }}"
                                    action="{{ route('admin.pegawai.access.store', $user->id) }}" method="POST">@csrf
                                </form>
                                <form id="desk-del-{{ $user->id }}"
                                    action="{{ route('admin.pegawai.access.destroy', $user->id) }}" method="POST"
                                    class="js-del-form">@csrf @method('DELETE')</form>

                                <td class="px-4 py-3 align-middle">
                                    <select form="desk-edit-{{ $user->id }}" name="role"
                                        class="js-role w-full bg-white border border-gray-200 rounded-lg px-2 py-1.5 text-xs font-semibold text-gray-700 focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer hover:border-indigo-300 transition-all shadow-sm">
                                        <option value="" class="text-gray-400">— Tanpa Akses —</option>
                                        <option value="admin_bidang" {{ $role === 'admin_bidang' ? 'selected' : '' }}>Admin
                                            Divisi</option>
                                        <option value="superadmin" {{ $role === 'superadmin' ? 'selected' : '' }}>
                                            Superadmin</option>
                                    </select>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <select form="desk-edit-{{ $user->id }}" name="division_name"
                                        class="js-div w-full bg-white border border-gray-200 rounded-lg px-2 py-1.5 text-xs font-semibold text-gray-700 focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer hover:border-indigo-300 transition-all shadow-sm {{ $role !== 'admin_bidang' ? 'hidden' : '' }}">
                                        <option value="" class="text-gray-400">— Pilih Divisi —</option>
                                        @foreach ($divisions as $div)
                                            <option value="{{ $div->name }}"
                                                {{ $divisionName === $div->name ? 'selected' : '' }}>{{ $div->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-4 py-3 align-middle text-center flex items-center justify-center gap-1.5">
                                    {{-- Tombol Update (Awalnya mati) --}}
                                    <button form="desk-edit-{{ $user->id }}" type="submit" disabled
                                        class="js-btn-save inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-gray-100 text-gray-400 cursor-not-allowed border border-transparent shadow-none">
                                        <i class="bi bi-check2"></i> Simpan
                                    </button>

                                    {{-- Tombol Cabut (Hanya muncul kalau sudah punya akses) --}}
                                    @if ($hasAccess)
                                        <button form="desk-del-{{ $user->id }}" type="submit" title="Cabut Akses"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white border border-red-200 text-red-500 hover:bg-red-50 hover:text-red-700 transition-all shadow-sm">
                                            <i class="bi bi-trash3-fill text-[11px]"></i>
                                        </button>
                                    @endif
                                </td>
                            @else
                                {{-- Jika akun superadmin yang sedang login --}}
                                <td colspan="3" class="px-4 py-3 align-middle">
                                    <div
                                        class="flex items-center gap-2 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
                                        <i class="bi bi-person-check-fill text-emerald-500 text-sm"></i>
                                        <p class="text-xs font-bold text-emerald-700">Ini adalah akun Anda saat ini (Akses
                                            tidak dapat diubah dari sini).</p>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- WUJUD 2: MOBILE CARDS (Tampil di HP) --}}
        <div class="flex flex-col gap-3 p-4 lg:hidden bg-gray-50/50">
            @foreach ($pegawai as $user)
                @php
                    $role = $user->magangAccessRight->role ?? '';
                    $divisionName = $user->magangAccessRight->division_name ?? '';
                    $hasAccess = $role !== '';
                    $initial = strtoupper(substr($user->name, 0, 1));
                    $isMe = $user->id === Auth::id();
                    $colors = [
                        'bg-indigo-100 text-indigo-700',
                        'bg-blue-100 text-blue-700',
                        'bg-emerald-100 text-emerald-700',
                        'bg-rose-100 text-rose-700',
                        'bg-violet-100 text-violet-700',
                    ];
                    $colorClass = $colors[ord($initial) % count($colors)];
                @endphp

                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col gap-3 js-access-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div
                            class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-sm shrink-0 border border-black/5 {{ $colorClass }}">
                            {{ $initial }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 truncate leading-tight">{{ $user->name }}</h4>
                            <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
                        </div>
                    </div>

                    @if (!$isMe)
                        <form id="mob-edit-{{ $user->id }}"
                            action="{{ route('admin.pegawai.access.store', $user->id) }}" method="POST">@csrf</form>
                        <form id="mob-del-{{ $user->id }}"
                            action="{{ route('admin.pegawai.access.destroy', $user->id) }}" method="POST"
                            class="js-del-form">@csrf @method('DELETE')</form>

                        <div class="flex flex-col gap-2 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Hak Akses:</label>
                            <select form="mob-edit-{{ $user->id }}" name="role"
                                class="js-role w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold text-gray-700 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm">
                                <option value="">— Tanpa Akses —</option>
                                <option value="admin_bidang" {{ $role === 'admin_bidang' ? 'selected' : '' }}>Admin Divisi
                                </option>
                                <option value="superadmin" {{ $role === 'superadmin' ? 'selected' : '' }}>Superadmin
                                </option>
                            </select>

                            <select form="mob-edit-{{ $user->id }}" name="division_name"
                                class="js-div w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold text-gray-700 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm mt-1 {{ $role !== 'admin_bidang' ? 'hidden' : '' }}">
                                <option value="">— Pilih Divisi —</option>
                                @foreach ($divisions as $div)
                                    <option value="{{ $div->name }}"
                                        {{ $divisionName === $div->name ? 'selected' : '' }}>{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-2 mt-1">
                            <button form="mob-edit-{{ $user->id }}" type="submit" disabled
                                class="js-btn-save flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all bg-gray-100 text-gray-400 cursor-not-allowed">
                                <i class="bi bi-check2-circle"></i> Simpan
                            </button>
                            @if ($hasAccess)
                                <button form="mob-del-{{ $user->id }}" type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white border border-red-200 text-red-500 hover:bg-red-50 text-xs font-bold shadow-sm transition-all">
                                    Cabut
                                </button>
                            @endif
                        </div>
                    @else
                        <div
                            class="flex items-center justify-center gap-2 bg-emerald-50 border border-emerald-100 rounded-lg p-2 mt-1">
                            <i class="bi bi-person-check-fill text-emerald-500 text-xs"></i>
                            <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wide">Ini Akun Anda</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ================================================================
     PAGINATION
     ================================================================ --}}
    @if ($pegawai->hasPages())
        <div class="mt-5 border-t border-gray-100 pt-5 flex justify-center">
            {{ $pegawai->appends(request()->query())->links() }}
        </div>
    @endif

    {{-- ================================================================
     JAVASCRIPT LOGIC (INLINE DETECTOR)
     ================================================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.js-access-row');

            rows.forEach(row => {
                const roleSel = row.querySelector('.js-role');
                const divSel = row.querySelector('.js-div');
                const saveBtn = row.querySelector('.js-btn-save');

                if (!roleSel) return; // Skip baris "Ini Akun Anda"

                const origRole = roleSel.value;
                const origDiv = divSel.value;

                const evaluateState = () => {
                    const role = roleSel.value;
                    const div = divSel.value;

                    // 1. Munculkan atau sembunyikan dropdown divisi
                    if (role === 'admin_bidang') {
                        divSel.classList.remove('hidden');
                    } else {
                        divSel.classList.add('hidden');
                        divSel.value = ""; // Reset divisi
                    }

                    // 2. Deteksi apakah ada perubahan
                    const isChanged = (role !== origRole) || (role === 'admin_bidang' && div !==
                        origDiv);

                    // 3. Validasi: Jangan izinkan save jika role kosong, atau jika admin bidang tapi belum milih divisi
                    let isValid = true;
                    if (role === '') isValid = false; // Harus pakai tombol cabut akses
                    if (role === 'admin_bidang' && div === '') isValid = false;

                    // 4. Nyalakan / Matikan tombol save
                    if (isChanged && isValid) {
                        saveBtn.removeAttribute('disabled');

                        // Gaya aktif (Nyala Indigo)
                        saveBtn.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed',
                            'shadow-none', 'border-transparent');
                        saveBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm',
                            'hover:bg-indigo-700');
                    } else {
                        saveBtn.setAttribute('disabled', 'true');

                        // Gaya mati (Abu-abu)
                        saveBtn.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed',
                            'shadow-none', 'border-transparent');
                        saveBtn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm',
                            'hover:bg-indigo-700');
                    }
                };

                // Pantau setiap perubahan pada dropdown
                roleSel.addEventListener('change', evaluateState);
                divSel.addEventListener('change', evaluateState);
            });

            // Konfirmasi sebelum mencabut akses
            const delForms = document.querySelectorAll('.js-del-form');
            delForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm(
                            'PERINGATAN: Yakin ingin mencabut hak akses pegawai ini dari sistem?'
                        )) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
