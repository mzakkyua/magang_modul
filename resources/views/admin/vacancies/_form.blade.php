@php
    $hakAkses = $hakAkses ?? \App\Models\MagangAccessRight::where('user_id', auth()->id())->first();
@endphp


{{-- =========================================
ERROR VALIDATION
========================================= --}}
@if ($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- =========================================
INFORMASI POSISI
========================================= --}}
<h4 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">
    Informasi Posisi
</h4>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    {{-- Judul --}}
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Judul Posisi
        </label>
        <input type="text" name="title" value="{{ old('title', $vacancy->title ?? '') }}" required
            class="w-full rounded-md border-gray-300 p-2 border">
    </div>

    {{-- Divisi --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Divisi Penempatan
        </label>

        @if ($hakAkses->role === 'superadmin')
            <input type="text" name="division_name" value="{{ old('division_name', $vacancy->division_name ?? '') }}"
                required class="w-full rounded-md border-gray-300 p-2 border">
        @else
            <input type="text" value="{{ $hakAkses->division_name }}" disabled
                class="w-full bg-gray-100 text-gray-500 rounded-md border-gray-300 p-2 border">
            <input type="hidden" name="division_name" value="{{ $hakAkses->division_name }}">
        @endif
    </div>

    {{-- Tipe --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Tipe Program
        </label>
        <select name="type" id="typeSelect" class="w-full rounded-md border-gray-300 p-2 border" required>
            <option value="magang" {{ old('type', $vacancy->type ?? '') === 'magang' ? 'selected' : '' }}>
                Magang
            </option>
            <option value="penelitian" {{ old('type', $vacancy->type ?? '') === 'penelitian' ? 'selected' : '' }}>
                Penelitian
            </option>
        </select>

        <p id="typeHint" class="text-xs text-orange-500 mt-1 hidden">
            Penelitian hanya untuk pendaftaran individu.
        </p>
    </div>
</div>

{{-- =========================================
TEKNIS PENDAFTARAN
========================================= --}}
<h4 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">
    Teknis Pendaftaran
</h4>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- Mode --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Mode Pendaftaran
        </label>

        <select id="regMode" class="w-full rounded-md border-gray-300 p-2 border"
            {{ $hasApplicant ?? false ? 'disabled' : '' }}>
            @foreach (['individu', 'kelompok', 'hybrid'] as $mode)
                <option value="{{ $mode }}"
                    {{ old('registration_mode', $vacancy->registration_mode ?? '') === $mode ? 'selected' : '' }}>
                    {{ ucfirst($mode) }}
                </option>
            @endforeach
        </select>

        {{-- nilai aman --}}
        <input type="hidden" name="registration_mode"
            value="{{ old('registration_mode', $vacancy->registration_mode ?? 'individu') }}">
    </div>

    {{-- Min --}}
    <div id="minMemberDiv" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Minimal Anggota
        </label>
        <input type="number" name="min_members" id="minInput"
            value="{{ old('min_members', $vacancy->min_members ?? 1) }}"
            class="w-full rounded-md border-gray-300 p-2 border">
    </div>

    {{-- Max --}}
    <div id="maxMemberDiv" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Maksimal Anggota
        </label>
        <input type="number" name="max_members" id="maxInput"
            value="{{ old('max_members', $vacancy->max_members ?? 1) }}"
            class="w-full rounded-md border-gray-300 p-2 border">
    </div>

    {{-- Kuota --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Total Kuota
        </label>
        <input type="number" name="quota_slots" min="1"
            value="{{ old('quota_slots', $vacancy->quota_slots ?? 1) }}" required
            class="w-full rounded-md border-gray-300 p-2 border">
    </div>
</div>

{{-- =========================================
PERIODE
========================================= --}}
<h4 class="text-lg font-semibold text-gray-700 mb-2">
    Periode Magang
</h4>

{{-- warning --}}
<div id="dateWarning" class="hidden bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
    <p class="text-sm text-yellow-700">
        ⚠️ Perhatian: Durasi pendek (<span id="dayCount">0</span> hari)
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Tanggal Mulai
        </label>
        <input type="date" name="start_date" id="startDate"
            value="{{ old('start_date', $vacancy->start_date ?? '') }}" required
            class="w-full rounded-md border-gray-300 p-2 border">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Tanggal Selesai
        </label>
        <input type="date" name="end_date" id="endDate" value="{{ old('end_date', $vacancy->end_date ?? '') }}"
            required class="w-full rounded-md border-gray-300 p-2 border">
    </div>
</div>

{{-- =========================================
DESKRIPSI
========================================= --}}
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Deskripsi / Syarat
    </label>
    <textarea name="description" rows="4" class="w-full rounded-md border-gray-300 p-2 border">{{ old('description', $vacancy->description ?? '') }}</textarea>
</div>
