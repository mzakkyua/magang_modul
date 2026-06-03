@extends('layouts.admin')

@section('title', $existingAssessment ? 'Edit Penilaian' : 'Input Penilaian')

@push('header_actions')
    <a href="{{ route('admin.assessments.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500
               border border-gray-200 bg-white px-4 py-2 rounded-xl
               hover:bg-gray-50 transition-colors duration-150">
        <i class="bi bi-arrow-left text-xs"></i> Kembali
    </a>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto space-y-5">

        {{-- ── INFO PESERTA ── --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-11 h-11 rounded-xl bg-blue-100 border border-blue-200 flex items-center justify-center
                            font-extrabold text-blue-600 text-base shrink-0">
                        {{ strtoupper(substr($member->user->profile->full_name ?? $member->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-extrabold text-gray-900">
                            {{ $member->user->profile->full_name ?? $member->user->name }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            <i class="bi bi-briefcase text-[10px] mr-1"></i>
                            {{ $member->application->vacancy->title }}
                            <span class="mx-1.5 text-gray-300">·</span>
                            <i class="bi bi-building text-[10px] mr-1"></i>
                            {{ $member->application->vacancy->division_name }}
                        </p>
                    </div>
                </div>

                {{-- Status badge --}}
                @if ($existingAssessment)
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-extrabold
                             bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full shrink-0">
                        <i class="bi bi-check-circle-fill text-[11px]"></i> Sudah Dinilai
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-extrabold
                             bg-amber-50 text-amber-600 border border-amber-200 rounded-full shrink-0">
                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse inline-block"></span>
                        Belum Dinilai
                    </span>
                @endif
            </div>
        </div>

        {{-- ── FORM PENILAIAN ── --}}
        <form action="{{ route('admin.assessments.store', $member->id) }}" method="POST" id="assessmentForm">
            @csrf

            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

                {{-- Form header + live score --}}
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                            <i class="bi bi-bar-chart-fill text-blue-500 text-sm"></i>
                        </div>
                        <h2 class="text-sm font-extrabold text-gray-900">Formulir Evaluasi Magang</h2>
                    </div>

                    {{-- Live score display --}}
                    <div
                        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl shadow-md shadow-blue-600/25">
                        <div class="text-right">
                            <p class="text-[9px] font-bold uppercase tracking-wider opacity-70">Prediksi Skor Akhir</p>
                            <p id="liveFinalScore" class="text-xl font-extrabold leading-none">0.00</p>
                        </div>
                        <i class="bi bi-calculator text-blue-200 text-lg"></i>
                    </div>
                </div>

                <div class="p-5 space-y-6">

                    {{-- ── A. PENILAIAN KUANTITATIF ── --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                <i class="bi bi-123 text-indigo-500 text-xs"></i>
                            </div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                A. Penilaian Kuantitatif (0 – 100)
                            </p>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">

                            {{-- Behavior --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                    1. Perilaku (Behavior)
                                </label>
                                <div class="relative">
                                    <input type="number" name="score_behavior" id="inputBehavior"
                                        value="{{ old('score_behavior', $existingAssessment->score_behavior ?? 0) }}"
                                        min="0" max="100" required
                                        class="score-input w-full pl-4 pr-12 py-3 rounded-xl border text-lg font-extrabold
                                           text-gray-800 outline-none transition-all duration-200
                                           border-gray-200 bg-white
                                           hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                                           @error('score_behavior') @enderror"
                                        placeholder="0">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">pts</span>
                                </div>
                                <p class="text-[10.5px] text-gray-400 mt-1.5">Etika, sopan santun, attitude</p>
                                @error('score_behavior')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Discipline --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                    2. Kedisiplinan
                                </label>
                                <div class="relative">
                                    <input type="number" name="score_discipline" id="inputDiscipline"
                                        value="{{ old('score_discipline', $existingAssessment->score_discipline ?? 0) }}"
                                        min="0" max="100" required
                                        class="score-input w-full pl-4 pr-12 py-3 rounded-xl border text-lg font-extrabold
                                           text-gray-800 outline-none transition-all duration-200
                                           border-gray-200 bg-white
                                           hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                                           @error('score_discipline') @enderror"
                                        placeholder="0">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">pts</span>
                                </div>
                                <p class="text-[10.5px] text-gray-400 mt-1.5">Ketepatan waktu, kehadiran</p>
                                @error('score_discipline')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Performance --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                    3. Kinerja (Performance)
                                </label>
                                <div class="relative">
                                    <input type="number" name="score_performance" id="inputPerformance"
                                        value="{{ old('score_performance', $existingAssessment->score_performance ?? 0) }}"
                                        min="0" max="100" required
                                        class="score-input w-full pl-4 pr-12 py-3 rounded-xl border text-lg font-extrabold
                                           text-gray-800 outline-none transition-all duration-200
                                           border-gray-200 bg-white
                                           hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                                           @error('score_performance') @enderror"
                                        placeholder="0">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">pts</span>
                                </div>
                                <p class="text-[10.5px] text-gray-400 mt-1.5">Hasil kerja, inisiatif, skill</p>
                                @error('score_performance')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- ── B. CATATAN & EVALUASI ── --}}
                    <div class="border-t border-gray-100 pt-5">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                                <i class="bi bi-chat-left-text text-amber-500 text-xs"></i>
                            </div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                B. Catatan & Evaluasi
                            </p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                    Catatan Evaluasi (Utama)
                                </label>
                                <textarea name="evaluation_notes" rows="5" placeholder="Berikan evaluasi mendalam tentang kinerja peserta..."
                                    class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                                       outline-none resize-none transition-all duration-200
                                       border-gray-200 bg-white placeholder:text-gray-300 placeholder:font-normal
                                       hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100">{{ old('evaluation_notes', $existingAssessment->evaluation_notes ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                    Catatan Tambahan (Saran / Kritik)
                                </label>
                                <textarea name="additional_notes" rows="5" placeholder="Saran pengembangan untuk peserta..."
                                    class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                                       outline-none resize-none transition-all duration-200
                                       border-gray-200 bg-white placeholder:text-gray-300 placeholder:font-normal
                                       hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100">{{ old('additional_notes', $existingAssessment->additional_notes ?? '') }}</textarea>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- Footer tombol --}}
                <div class="px-5 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between gap-3">
                    <a href="{{ route('admin.assessments.index') }}"
                        class="text-sm text-gray-400 hover:text-gray-600 font-medium transition-colors flex items-center gap-1.5">
                        <i class="bi bi-arrow-left text-xs"></i> Batal
                    </a>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
                           text-white text-sm font-bold px-5 py-2.5 rounded-xl
                           shadow-md shadow-blue-600/25 hover:shadow-blue-600/40 hover:-translate-y-0.5
                           transition-all duration-200">
                        <i class="bi bi-save-fill"></i>
                        {{ $existingAssessment ? 'Perbarui Penilaian' : 'Simpan Penilaian' }}
                    </button>
                </div>

            </div>
        </form>

    </div>
@endsection

@push('scripts')
    @vite(['resources/js/admin/assessment-create.js'])
@endpush
