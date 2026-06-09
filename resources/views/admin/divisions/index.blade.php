@extends('layouts.admin')

@section('title', 'Kelola Master Divisi')

@section('content')

    {{-- ===================== HEADER ===================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Master Divisi Magang</h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelola <i>source of truth</i> seluruh referensi nama divisi sistem.
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-blue-600 bg-white hover:bg-blue-50 px-4 py-2 rounded-xl border border-gray-200 transition-all shadow-sm">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

        {{-- TANGKAP PESAN ERROR DARI CONTROLLER DI SINI --}}
        @if ($errors->any())
            <div
                class="lg:col-span-3 mb-2 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3 shadow-sm animate-pulse">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <h4 class="text-sm font-extrabold text-red-800">Tindakan Ditolak Sistem!</h4>
                    <ul class="text-xs font-bold text-red-600 mt-1 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ===================== TABEL DIVISI (KIRI) ===================== --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 shadow-inner">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Daftar Divisi Terdaftar</h3>
                </div>
                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full border border-gray-200">
                    {{ $divisions->total() }} Data
                </span>
            </div>

            @if ($divisions->isEmpty())
                <div class="py-20 text-center flex flex-col items-center justify-center">
                    <div
                        class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                        <svg class="w-8 h-8 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                        </svg>
                    </div>
                    <h4 class="text-gray-900 font-bold mb-1">Data Kosong</h4>
                    <p class="text-gray-500 text-sm">Tambahkan divisi pertama melalui panel di samping.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($divisions as $division)
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 hover:bg-gray-50 transition-colors group">

                            <div class="min-w-0 pr-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="relative flex h-2 w-2 mr-1">
                                        @if ($division->is_active)
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        @else
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-400"></span>
                                        @endif
                                    </span>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500">
                                        {{ $division->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Mempunyai <strong class="text-gray-700">{{ $division->active_vacancies_count }}</strong>
                                    lowongan aktif
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                {{-- Ditambahkan js-track-form --}}
                                <form action="{{ route('admin.divisions.update', $division) }}" method="POST"
                                    class="js-track-form flex relative group/form">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex rounded-lg shadow-sm">
                                        {{-- Ditambahkan js-track-input dan data-original --}}
                                        <input type="text" name="name" value="{{ $division->name }}"
                                            data-original="{{ $division->name }}" maxlength="100" required
                                            class="js-track-input w-32 sm:w-48 text-sm font-semibold text-gray-900 border border-gray-300 rounded-l-lg px-3 py-1.5 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                            placeholder="Nama Divisi">

                                        {{-- Ditambahkan js-submit-btn dan disabled by default --}}
                                        <button type="submit" disabled
                                            class="js-submit-btn -ml-px inline-flex items-center gap-x-1.5 rounded-r-lg border border-gray-300 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300 disabled:opacity-50 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed focus:z-10 transition-colors"
                                            title="Simpan Nama">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </button>
                                    </div>
                                </form>

                                <div class="w-px h-6 bg-gray-200 mx-1"></div>

                                <form action="{{ route('admin.divisions.toggle-active', $division) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        title="{{ $division->is_active ? 'Nonaktifkan Divisi' : 'Aktifkan Divisi' }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border transition-all 
                                        {{ $division->is_active ? 'border-amber-200 text-amber-600 hover:bg-amber-50 hover:border-amber-300' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-300' }}">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                        </svg>
                                    </button>
                                </form>

                                <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST"
                                    data-confirm="PERINGATAN: Hapus divisi {{ $division->name }}?\nData tidak dapat dikembalikan jika berhasil dihapus.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Divisi"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-transparent text-gray-400 hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-all">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($divisions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 mt-auto">
                        {{ $divisions->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- ===================== PANEL KANAN ===================== --}}
        <div class="space-y-6 lg:sticky lg:top-6">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-emerald-50/30">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Buat Divisi Baru
                    </h3>
                </div>
                <form action="{{ route('admin.divisions.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                            Nama Divisi Lengkap
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" maxlength="100" required
                            placeholder="Cth: Teknologi Informasi"
                            class="w-full text-sm text-gray-900 border border-gray-300 rounded-xl px-4 py-3 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all shadow-inner">
                        <p class="text-[11px] text-gray-500 mt-2 flex items-start gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.732l-1.115 4.542a.75.75 0 001.444.354l.05-.152a.75.75 0 00-1.423-.454l-.05.152a.25.25 0 01-.482-.118l1.115-4.542c.311-1.269-.98-2.305-2.126-1.732l-1.445.723a.75.75 0 00.67 1.342l1.445-.723zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            Otomatis tersinkronisasi ke seluruh dropdown master.
                        </p>
                    </div>
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:ring-gray-200 text-white text-sm font-bold py-3 px-4 rounded-xl transition-all shadow-md">
                        Simpan Divisi Baru
                    </button>
                </form>
            </div>

            <div class="bg-gray-900 text-gray-300 rounded-2xl p-6 shadow-md relative overflow-hidden">
                <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-white/5" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M11.644 1.59a.75.75 0 0 1 .712 0l9.75 5.25c.343.186.5.597.348.943l-8 18.25a.75.75 0 0 1-1.373 0l-8-18.25a.75.75 0 0 1 .348-.943l9.75-5.25ZM12 4.14 4.58 8.139l7.42 16.924L19.42 8.14 12 4.14Z" />
                </svg>
                <div class="relative z-10">
                    <h4 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"
                                clip-rule="evenodd" />
                        </svg>
                        Business Rules
                    </h4>
                    <ul class="text-xs space-y-3">
                        <li class="flex gap-2">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>Perubahan nama langsung berlaku global ke data yang ada.</span>
                        </li>
                        <li class="flex gap-2">
                            <svg class="w-4 h-4 text-red-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Divisi <strong>Nonaktif</strong> disembunyikan dari form pembuatan lowongan.</span>
                        </li>
                        <li class="flex gap-2">
                            <svg class="w-4 h-4 text-blue-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Divisi yang sudah memiliki lowongan <strong>tidak dapat dihapus</strong> (proteksi
                                database).</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    {{-- Script Validasi Form Inline Vanilla JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Logic Disable Tombol Jika Tidak Ada Perubahan / Kosong
            const trackForms = document.querySelectorAll('.js-track-form');

            trackForms.forEach(form => {
                const input = form.querySelector('.js-track-input');
                const btn = form.querySelector('.js-submit-btn');
                const originalValue = input.dataset.original;

                input.addEventListener('input', function() {
                    const currentValue = this.value.trim();

                    // Tombol aktif HANYA JIKA value berubah DAN tidak string kosong
                    if (currentValue !== originalValue && currentValue !== '') {
                        btn.removeAttribute('disabled');
                    } else {
                        btn.setAttribute('disabled', 'true');
                    }
                });
            });

            // 2. Logic Loading Spinner Mencegah Double Submit (Untuk SEMUA form)
            const allForms = document.querySelectorAll('form');

            allForms.forEach(form => {
                form.addEventListener('submit', function(e) {

                    // CEK DULU: Apakah form ini butuh konfirmasi (punya data-confirm)?
                    const confirmMessage = this.getAttribute('data-confirm');

                    if (confirmMessage) {
                        if (!window.confirm(confirmMessage)) {
                            e.preventDefault(); // Batalkan pengiriman
                            return; // Stop JS di sini
                        }
                    }

                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        // SULAPNYA DI SINI: Gunakan setTimeout agar browser jalan duluan
                        setTimeout(() => {
                            btn.setAttribute('disabled', 'true');
                            btn.classList.add('opacity-75', 'cursor-wait');

                            const spinnerSVG =
                                `<svg class="animate-spin w-4 h-4 text-current mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

                            if (btn.innerText.trim() !== "") {
                                btn.innerHTML = spinnerSVG +
                                    ' <span class="ml-2">Memproses...</span>';
                            } else {
                                btn.innerHTML = spinnerSVG;
                            }
                        }, 10); // Ditunda 10 milidetik (sangat cepat, mata tidak akan sadar)
                    }
                });
            });
        });
    </script>

@endsection
