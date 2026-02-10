@extends('layouts.admin')

@section('title', 'Input Penilaian Peserta')

@section('content')

    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border-l-4 border-blue-600">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ $member->user->profile->full_name ?? $member->user->name }}</h2>
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="bi bi-briefcase mr-1"></i> Posisi:
                        <strong>{{ $member->application->vacancy->title }}</strong>
                        <span class="mx-2">|</span>
                        <i class="bi bi-building mr-1"></i> Divisi:
                        <strong>{{ $member->application->vacancy->division_name }}</strong>
                    </p>
                </div>
                <div class="text-right">
                    <span class="block text-xs text-gray-400 uppercase tracking-wide">Status Saat Ini</span>
                    @if ($existingAssessment)
                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">SUDAH
                            DINILAI</span>
                    @else
                        <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">BELUM
                            DINILAI</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">

            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Formulir Evaluasi Magang</h3>
                <div class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow transition-colors duration-300">
                    <span class="text-xs block opacity-75">Prediksi Skor Akhir</span>
                    <span id="liveFinalScore" class="text-xl font-bold">0.00</span>
                </div>
            </div>

            <form action="{{ route('admin.assessments.store', $member->id) }}" method="POST" id="assessmentForm">
                @csrf

                <div class="p-8">
                    <h4 class="text-sm uppercase tracking-wide text-gray-500 font-bold mb-4 border-b pb-2">A. Penilaian
                        Kuantitatif (0 - 100)</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">1. Perilaku (Behavior)</label>
                            <div class="relative">
                                <input type="number" name="score_behavior" id="inputBehavior"
                                    value="{{ old('score_behavior', $existingAssessment->score_behavior ?? 0) }}"
                                    min="0" max="100"
                                    class="score-input w-full pl-4 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-bold text-gray-700 transition"
                                    placeholder="0" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-sm">Pts</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Etika, sopan santun, attitude.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">2. Kedisiplinan</label>
                            <div class="relative">
                                <input type="number" name="score_discipline" id="inputDiscipline"
                                    value="{{ old('score_discipline', $existingAssessment->score_discipline ?? 0) }}"
                                    min="0" max="100"
                                    class="score-input w-full pl-4 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-bold text-gray-700 transition"
                                    placeholder="0" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-sm">Pts</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Ketepatan waktu, kehadiran.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">3. Kinerja (Performance)</label>
                            <div class="relative">
                                <input type="number" name="score_performance" id="inputPerformance"
                                    value="{{ old('score_performance', $existingAssessment->score_performance ?? 0) }}"
                                    min="0" max="100"
                                    class="score-input w-full pl-4 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-bold text-gray-700 transition"
                                    placeholder="0" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-sm">Pts</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Hasil kerja, inisiatif, skill.</p>
                        </div>
                    </div>

                    <h4 class="text-sm uppercase tracking-wide text-gray-500 font-bold mb-4 border-b pb-2 mt-8">B. Catatan &
                        Evaluasi</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Evaluasi (Utama)</label>
                            <textarea name="evaluation_notes" rows="5"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Berikan evaluasi mendalam tentang kinerja peserta...">{{ old('evaluation_notes', $existingAssessment->evaluation_notes ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan
                                (Saran/Kritik)</label>
                            <textarea name="additional_notes" rows="5"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Saran pengembangan untuk peserta...">{{ old('additional_notes', $existingAssessment->additional_notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                    <a href="{{ route('admin.assessments.index') }}"
                        class="bg-white text-gray-700 border border-gray-300 px-6 py-2 rounded-lg hover:bg-gray-50 font-medium transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-lg transform hover:-translate-y-0.5 transition duration-200">
                        <i class="bi bi-save mr-2"></i> Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>

    </div>

    @push('scripts')
        @vite(['resources/js/admin/assessment-create.js'])
    @endpush

@endsection
