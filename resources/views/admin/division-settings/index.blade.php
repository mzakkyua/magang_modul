@extends('layouts.admin')

@section('title', 'Kelola Kuota Divisi')

@section('content')

    {{-- ===================== HEADER ===================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Kelola Kuota Divisi</h1>
            <p class="text-sm text-gray-500 mt-1">
                Monitor kapasitas dan atur batas maksimal lowongan aktif tiap divisi.
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

        {{-- ===================== TABEL KONFIGURASI ===================== --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 shadow-inner">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Konfigurasi Kuota</h3>
                </div>
                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full border border-gray-200">
                    {{ $settings->count() }} Divisi
                </span>
            </div>

            @if ($settings->isEmpty())
                <div class="py-20 text-center flex flex-col items-center justify-center">
                    <div
                        class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                        <svg class="w-8 h-8 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 12.677c-.066.214-.1.437-.1.661z" />
                        </svg>
                    </div>
                    <h4 class="text-gray-900 font-bold mb-1">Belum Ada Divisi</h4>
                    <p class="text-gray-500 text-sm">Data konfigurasi divisi kosong.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($settings as $setting)
                        @php
                            $cap = $capacityData->get($setting->division_name);
                            $filled = $cap['filled_slots'] ?? 0;
                            $max = $setting->max_open_vacancies;
                            $isFull = $max !== null && $filled >= $max;
                            $percentage = $max && $max > 0 ? min(100, round(($filled / $max) * 100)) : 0;

                            $statusColor = match (true) {
                                $percentage >= 100 => 'red',
                                $percentage >= 85 => 'orange',
                                $percentage >= 60 => 'amber',
                                default => 'emerald',
                            };

                            $statusLabel = match (true) {
                                $percentage >= 100 => 'Penuh',
                                $percentage >= 85 => 'Kritis',
                                $percentage >= 60 => 'Hampir Penuh',
                                default => 'Aman',
                            };
                        @endphp

                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-5 hover:bg-gray-50 transition-colors group">

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1.5">
                                    <h4 class="font-bold text-sm text-gray-900 truncate">{{ $setting->division_name }}</h4>
                                    <span
                                        class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 border border-{{ $statusColor }}-200">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <p class="text-xs text-gray-500 flex items-center gap-2 mb-2">
                                    <span class="font-semibold text-gray-700">{{ $filled }} aktif</span>
                                    <span class="text-gray-300">|</span>
                                    @if ($max !== null)
                                        Maks {{ $max }}
                                    @else
                                        Tidak Terbatas
                                    @endif

                                    @if ($cap && $cap['last_batch_end'])
                                        <span class="text-gray-300">|</span>
                                        <span class="text-blue-500 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Last Batch: {{ $cap['last_batch_end'] }}
                                        </span>
                                    @endif
                                </p>

                                @if ($max !== null)
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 max-w-50 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-{{ $statusColor }}-500 rounded-full transition-all duration-500 ease-out relative"
                                                style="width: {{ $percentage }}%">
                                                @if ($percentage >= 85)
                                                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold text-gray-400">{{ $percentage }}%</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Ditambahkan class js-track-form untuk memantau form --}}
                            <form action="{{ route('admin.division-settings.update', $setting) }}" method="POST"
                                class="js-track-form shrink-0 flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <div class="relative group-focus-within:ring-2 ring-blue-100 rounded-lg">
                                    {{-- Ditambahkan class js-track-input dan data-original --}}
                                    <input type="number" name="max_open_vacancies"
                                        value="{{ $setting->max_open_vacancies }}"
                                        data-original="{{ $setting->max_open_vacancies }}" min="1" max="99"
                                        placeholder="∞"
                                        class="js-track-input w-20 text-center text-sm font-semibold border border-gray-300 rounded-lg pl-3 pr-3 py-2 text-gray-700 bg-white hover:border-blue-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all shadow-sm">
                                </div>
                                {{-- Ditambahkan class js-submit-btn dan disabled by default karena data belum berubah --}}
                                <button type="submit" disabled title="Simpan Perubahan"
                                    class="js-submit-btn w-9 h-9 flex items-center justify-center bg-white text-blue-600 border border-gray-300 hover:border-blue-400 hover:bg-blue-50 disabled:opacity-40 disabled:bg-gray-50 disabled:border-gray-200 disabled:cursor-not-allowed disabled:text-gray-400 rounded-lg transition-all shadow-sm focus:ring-2 focus:ring-blue-100">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===================== PANEL INFO ===================== --}}
        <div class="space-y-6 lg:sticky lg:top-6">
            <div
                class="bg-linear-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 shadow-sm relative overflow-hidden">
                <svg class="absolute -bottom-6 -right-6 w-32 h-32 text-blue-500/10 rotate-12"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12.516 2.17a.75.75 0 00-1.032 0 11.209 11.209 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 00-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08zm3.094 8.016a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                        clip-rule="evenodd" />
                </svg>
                <div class="flex items-start gap-3 relative z-10">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600 shrink-0">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.758a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900 mb-2">Panduan Sistem Kuota</h4>
                        <ul class="text-xs text-blue-800 space-y-2.5">
                            <li class="flex gap-2 leading-relaxed">
                                <svg class="w-3 h-3 text-blue-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="6" />
                                </svg>
                                <span><strong>Slot terisi</strong> dihitung dari lowongan berstatus <span
                                        class="bg-white px-1 py-0.5 rounded border border-blue-200">open</span> atau <span
                                        class="bg-white px-1 py-0.5 rounded border border-blue-200">closed</span>.</span>
                            </li>
                            <li class="flex gap-2 leading-relaxed">
                                <svg class="w-3 h-3 text-blue-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="6" />
                                </svg>
                                <span>Status <strong>closed</strong> berarti kuota pendaftar penuh, namun program sedang
                                    berjalan.</span>
                            </li>
                            <li class="flex gap-2 leading-relaxed">
                                <svg class="w-3 h-3 text-blue-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="6" />
                                </svg>
                                <span>Status <strong>archived</strong> tidak dihitung memakan slot.</span>
                            </li>
                            <li class="flex gap-2 leading-relaxed text-red-700 bg-red-50/50 p-2 rounded-lg">
                                <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Admin divisi <strong>terkunci</strong> (tidak bisa membuat lowongan) jika slot
                                    penuh.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Validasi Form Inline Vanilla JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Logic Disable Tombol Jika Tidak Ada Perubahan (Untuk form inline)
            const trackForms = document.querySelectorAll('.js-track-form');

            trackForms.forEach(form => {
                const input = form.querySelector('.js-track-input');
                const btn = form.querySelector('.js-submit-btn');
                const originalValue = input.dataset.original;

                input.addEventListener('input', function() {
                    // Tombol aktif JIKA: value berbeda dari awal
                    if (this.value !== originalValue) {
                        btn.removeAttribute('disabled');
                    } else {
                        btn.setAttribute('disabled', 'true');
                    }
                });
            });

            // 2. Logic Loading Spinner Mencegah Double Submit (Untuk SEMUA form di halaman ini)
            const allForms = document.querySelectorAll('form');

            allForms.forEach(form => {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        // Kunci tombol agar tidak di-klik 2x
                        btn.setAttribute('disabled', 'true');
                        btn.classList.add('opacity-75', 'cursor-wait');

                        // Ganti ikon dengan Spinner SVG Tailwind (Ringan & Cepat)
                        const spinnerSVG =
                            `<svg class="animate-spin w-4 h-4 text-current mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

                        // Cek apakah tombol memiliki teks (seperti tombol "Simpan Divisi Baru") atau hanya Icon.
                        if (btn.innerText.trim() !== "") {
                            btn.innerHTML = spinnerSVG + ' <span class="ml-2">Memproses...</span>';
                        } else {
                            btn.innerHTML = spinnerSVG;
                        }
                    }
                });
            });
        });
    </script>
@endsection
