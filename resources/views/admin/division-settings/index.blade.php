@extends('layouts.admin')

@section('title', 'Kelola Kuota Divisi')

@section('content')

    {{-- ===================== HEADER ===================== --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-extrabold text-gray-800">Kelola Kuota Divisi</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Monitor kapasitas dan atur batas maksimal lowongan aktif tiap divisi.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
            class="text-sm text-gray-500 hover:text-blue-600 flex items-center gap-1 transition">
            <i class="bi bi-arrow-left text-xs"></i>
            Kembali ke Dashboard
        </a>
    </div>

    {{-- ===================== FLASH MESSAGES ===================== --}}
    @if (session('success'))
        <div
            class="mb-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4">
            <i class="bi bi-check-circle-fill text-emerald-500 shrink-0 mt-0.5"></i>

            <p class="text-sm font-semibold">
                {{ session('success') }}
            </p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4">
            <i class="bi bi-exclamation-circle-fill text-red-500 shrink-0 mt-0.5"></i>

            <div>
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-semibold">
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===================== TABEL KONFIGURASI ===================== --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">

                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="bi bi-sliders text-blue-600 text-xs"></i>
                </div>

                <h3 class="font-bold text-gray-800 text-sm">
                    Konfigurasi Kuota per Divisi
                </h3>

                <span class="ml-auto text-xs text-gray-400">
                    {{ $settings->count() }} divisi dikonfigurasi
                </span>
            </div>

            {{-- Empty State --}}
            @if ($settings->isEmpty())
                <div class="py-16 text-center">

                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-folder-x text-gray-400 text-xl"></i>
                    </div>

                    <p class="text-gray-500 font-semibold text-sm">
                        Belum ada konfigurasi divisi
                    </p>

                </div>
            @else
                <div class="divide-y divide-gray-50">

                    @foreach ($settings as $setting)
                        @php

                            /**
                             * =====================================================
                             * OCCUPANCY DATA
                             * =====================================================
                             */

                            $cap = $capacityData->get($setting->division_name);

                            $filled = $cap['filled_slots'] ?? 0;

                            $max = $setting->max_open_vacancies;

                            $isFull = $max !== null && $filled >= $max;

                            /**
                             * =====================================================
                             * PERCENTAGE
                             * =====================================================
                             */

                            $percentage = $max && $max > 0 ? min(100, round(($filled / $max) * 100)) : 0;

                            /**
                             * =====================================================
                             * SMART STATUS BADGE
                             * =====================================================
                             */

                            $statusLabel = 'Aman';

                            if ($percentage >= 100) {
                                $statusLabel = 'Penuh';
                            } elseif ($percentage >= 85) {
                                $statusLabel = 'Kritis';
                            } elseif ($percentage >= 60) {
                                $statusLabel = 'Hampir Penuh';
                            }

                        @endphp

                        <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50/50 transition group">

                            {{-- ===================== INFO DIVISI ===================== --}}
                            <div class="flex-1 min-w-0">

                                <div class="flex items-center gap-2">

                                    <p class="font-semibold text-sm text-gray-800">
                                        {{ $setting->division_name }}
                                    </p>

                                    <span @class([
                                        'text-[10px] font-bold border px-2 py-0.5 rounded-full',
                                    
                                        'bg-red-50 text-red-600 border-red-100' => $percentage >= 100,
                                    
                                        'bg-orange-50 text-orange-600 border-orange-100' =>
                                            $percentage >= 85 && $percentage < 100,
                                    
                                        'bg-amber-50 text-amber-600 border-amber-100' =>
                                            $percentage >= 60 && $percentage < 85,
                                    
                                        'bg-emerald-50 text-emerald-600 border-emerald-100' => $percentage < 60,
                                    ])>
                                        {{ $statusLabel }}
                                    </span>

                                </div>

                                <p class="text-xs text-gray-400 mt-0.5">

                                    {{ $filled }} lowongan aktif

                                    @if ($max !== null)
                                        dari maks {{ $max }}
                                    @else
                                        (unlimited)
                                    @endif

                                    @if ($cap && $cap['estimated_open'])
                                        · Estimasi buka:
                                        {{ $cap['estimated_open'] }}
                                    @endif

                                </p>

                                {{-- Progress Bar --}}
                                @if ($max !== null)
                                    <div class="mt-1.5 w-full max-w-xs h-1.5 bg-gray-100 rounded-full overflow-hidden">

                                        <div @class([
                                            'h-full rounded-full',
                                        
                                            'bg-red-400' => $percentage >= 100,
                                        
                                            'bg-orange-400' => $percentage >= 85 && $percentage < 100,
                                        
                                            'bg-amber-400' => $percentage >= 60 && $percentage < 85,
                                        
                                            'bg-emerald-400' => $percentage < 60,
                                        ]) style="width: {{ $percentage }}%">

                                        </div>

                                    </div>

                                    <p class="text-[10px] text-gray-400 mt-1">
                                        {{ $percentage }}% terpakai
                                    </p>
                                @endif

                            </div>

                            {{-- ===================== FORM UPDATE ===================== --}}
                            <form action="{{ route('admin.division-settings.update', $setting) }}" method="POST"
                                class="flex items-center gap-2 shrink-0">

                                @csrf
                                @method('PATCH')

                                <div class="relative">

                                    <input type="number" name="max_open_vacancies"
                                        value="{{ $setting->max_open_vacancies }}" min="1" max="99"
                                        placeholder="∞"
                                        class="w-16 text-center text-sm border border-gray-200 rounded-lg px-2 py-1.5
                                               focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100
                                               bg-gray-50 transition">

                                </div>

                                <button type="submit"
                                    class="text-xs font-semibold text-blue-600 hover:text-blue-800
                                           bg-blue-50 hover:bg-blue-100 border border-blue-100
                                           px-3 py-1.5 rounded-lg transition">

                                    Simpan

                                </button>

                            </form>

                        </div>
                    @endforeach

                </div>
            @endif

        </div>

        {{-- ===================== PANEL INFO ===================== --}}
        <div class="space-y-5">

            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">

                <div class="flex items-start gap-2">

                    <i class="bi bi-lightbulb-fill text-blue-400 shrink-0 mt-0.5"></i>

                    <div>

                        <p class="text-xs font-bold text-blue-800 mb-1">
                            Cara kerja kuota
                        </p>

                        <ul class="text-xs text-blue-700 space-y-1.5 leading-relaxed">

                            <li>
                                · "Slot terisi" = lowongan dengan status
                                <strong>open</strong> atau <strong>closed</strong>
                            </li>

                            <li>
                                · Status <strong>closed</strong> = kuota peserta penuh,
                                program masih berjalan
                            </li>

                            <li>
                                · Status <strong>archived</strong> = selesai,
                                tidak dihitung
                            </li>

                            <li>
                                · Admin divisi <strong>tidak bisa</strong>
                                membuat lowongan baru jika slot sudah penuh
                            </li>

                            <li>
                                · Kosongkan field kuota untuk menghapus batas
                                (unlimited)
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
