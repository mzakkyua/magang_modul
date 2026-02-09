@extends('layouts.admin')

@section('title', 'Edit Lowongan')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">
    
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Edit Data Lowongan</h2>
        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $vacancy->status == 'open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            STATUS: {{ strtoupper($vacancy->status) }}
        </span>
    </div>

    <form action="{{ route('admin.vacancies.update', $vacancy->id) }}" method="POST">
        @csrf
        @method('PUT') <h4 class="text-lg font-semibold text-gray-700 mb-4">Informasi Dasar</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Posisi</label>
                <input type="text" name="title" value="{{ old('title', $vacancy->title) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Divisi Penempatan</label>
                @php
                    $user = Auth::user();
                    $hakAkses = \App\Models\MagangAccessRight::where('user_id', $user->id)->first();
                @endphp

                @if($hakAkses->role === 'superadmin')
                    <input type="text" name="division_name" value="{{ old('division_name', $vacancy->division_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" required>
                @else
                    <input type="text" value="{{ $vacancy->division_name }}" class="w-full bg-gray-100 rounded-md border-gray-300 text-gray-500 p-2 border cursor-not-allowed" disabled>
                    <input type="hidden" name="division_name" value="{{ $vacancy->division_name }}">
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Program</label>
                <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="magang" {{ $vacancy->type == 'magang' ? 'selected' : '' }}>Magang (PKL)</option>
                    <option value="penelitian" {{ $vacancy->type == 'penelitian' ? 'selected' : '' }}>Penelitian (Skripsi/Riset)</option>
                </select>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-gray-700 mb-4 border-t pt-4">Teknis Pendaftaran</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode Pendaftaran</label>
                <select name="registration_mode" id="regMode" onchange="toggleMemberInputs()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                    <option value="individu" {{ $vacancy->registration_mode == 'individu' ? 'selected' : '' }}>Individu</option>
                    <option value="kelompok" {{ $vacancy->registration_mode == 'kelompok' ? 'selected' : '' }}>Kelompok</option>
                    <option value="hybrid" {{ $vacancy->registration_mode == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
            </div>

            <div id="minMemberDiv" class="{{ $vacancy->registration_mode == 'individu' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Min. Anggota</label>
                <input type="number" name="min_members" value="{{ old('min_members', $vacancy->min_members) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>

            <div id="maxMemberDiv" class="{{ $vacancy->registration_mode == 'individu' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Max. Anggota</label>
                <input type="number" name="max_members" value="{{ old('max_members', $vacancy->max_members) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Kuota (Slot)</label>
                <input type="number" name="quota_slots" value="{{ old('quota_slots', $vacancy->quota_slots) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ old('start_date', $vacancy->start_date) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ old('end_date', $vacancy->end_date) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" required>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan / Syarat</label>
            <textarea name="description" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">{{ old('description', $vacancy->description) }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.vacancies.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition shadow">
                <i class="bi bi-save mr-2"></i> Update Perubahan
            </button>
        </div>

    </form>
</div>

<script>
    function toggleMemberInputs() {
        const mode = document.getElementById('regMode').value;
        const minDiv = document.getElementById('minMemberDiv');
        const maxDiv = document.getElementById('maxMemberDiv');

        if (mode === 'individu') {
            minDiv.classList.add('hidden');
            maxDiv.classList.add('hidden');
        } else {
            minDiv.classList.remove('hidden');
            maxDiv.classList.remove('hidden');
        }
    }
    
    // Jalankan sekali saat halaman dimuat agar inputan menyesuaikan data yang ada di database
    document.addEventListener("DOMContentLoaded", function() {
        toggleMemberInputs();
    });
</script>

@endsection