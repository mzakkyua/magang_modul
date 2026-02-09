@extends('layouts.admin')

@section('title', 'Buat Lowongan Baru')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">
    
    <form id="createVacancyForm" action="{{ route('admin.vacancies.store') }}" method="POST">
        @csrf

        <h4 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Informasi Posisi</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Posisi</label>
                <input type="text" name="title" class="w-full rounded-md border-gray-300 shadow-sm p-2 border" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Divisi Penempatan</label>
                @php
                    $user = Auth::user();
                    $hakAkses = \App\Models\MagangAccessRight::where('user_id', $user->id)->first();
                @endphp
                @if($hakAkses->role === 'superadmin')
                    <input type="text" name="division_name" class="w-full rounded-md border-gray-300 shadow-sm p-2 border" required>
                @else
                    <input type="text" value="{{ $hakAkses->division_name }}" class="w-full bg-gray-100 rounded-md border-gray-300 text-gray-500 p-2 border cursor-not-allowed" disabled>
                    <input type="hidden" name="division_name" value="{{ $hakAkses->division_name }}">
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Program</label>
                <select name="type" id="typeSelect" class="w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    <option value="magang">Magang (PKL)</option>
                    <option value="penelitian">Penelitian (Skripsi/Riset)</option>
                </select>
                <p id="typeHint" class="text-xs text-orange-500 mt-1 hidden">
                    <i class="bi bi-info-circle"></i> Penelitian hanya untuk Individu.
                </p>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2 mt-8">Teknis Pendaftaran</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode Pendaftaran</label>
                <select name="registration_mode" id="regMode" class="w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    <option value="individu">Individu (Perorangan)</option>
                    <option value="kelompok">Kelompok (Wajib Group)</option>
                    <option value="hybrid">Hybrid (Bisa Keduanya)</option>
                </select>
            </div>

            <div id="minMemberDiv" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Min. Anggota</label>
                <input type="number" name="min_members" id="minInput" value="1" class="w-full rounded-md border-gray-300 shadow-sm p-2 border">
            </div>

            <div id="maxMemberDiv" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Max. Anggota</label>
                <input type="number" name="max_members" id="maxInput" value="1" class="w-full rounded-md border-gray-300 shadow-sm p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Kuota (Slot)</label>
                <input type="number" name="quota_slots" min="1" class="w-full rounded-md border-gray-300 shadow-sm p-2 border" required>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-gray-700 mb-2 mt-8">Periode Magang</h4>
        
        <div id="dateWarning" class="hidden bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
            <p class="text-sm text-yellow-700"><strong>Perhatian:</strong> Rentang waktu singkat (<span id="dayCount">0</span> hari).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" id="startDate" class="w-full rounded-md border-gray-300 shadow-sm p-2 border" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" id="endDate" class="w-full rounded-md border-gray-300 shadow-sm p-2 border" required>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan / Syarat</label>
            <textarea name="description" rows="4" class="w-full rounded-md border-gray-300 shadow-sm p-2 border"></textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.vacancies.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Batal</a>
            
            <button type="button" id="btnSave" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Lowongan</button>
        </div>

    </form>
</div>

@push('scripts')
    @vite(['resources/js/admin/vacancy-create.js'])
@endpush

@endsection