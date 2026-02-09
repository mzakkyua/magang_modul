@extends('layouts.admin')

@section('title', 'Dashboard Statistik')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="flex-shrink-0 p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="bi bi-briefcase text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 uppercase">Lowongan Aktif</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalLowongan }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500 cursor-pointer hover:bg-yellow-50 transition" onclick="window.location='{{ route('admin.applications.index') }}'">
        <div class="flex items-center">
            <div class="flex-shrink-0 p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="bi bi-file-earmark-person text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 uppercase">Verifikasi Masuk</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $perluVerifikasi }}</h3>
                <span class="text-xs text-yellow-600 font-semibold">Lihat Pendaftar &rarr;</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="flex-shrink-0 p-3 rounded-full bg-green-100 text-green-600">
                <i class="bi bi-people text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 uppercase">Sedang Magang</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $sedangMagang }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-gray-500">
        <div class="flex items-center">
            <div class="flex-shrink-0 p-3 rounded-full bg-gray-100 text-gray-600">
                <i class="bi bi-person-badge text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 uppercase">Total Akun</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalSiswa + $totalMahasiswa }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">
            <i class="bi bi-info-circle mr-2 text-blue-500"></i> Informasi Sistem
        </h3>
    </div>
    <div class="p-6 text-gray-700">
        <p class="mb-4">Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>.</p>
        
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded text-sm text-blue-700">
            @php
                $akses = \App\Models\MagangAccessRight::where('user_id', Auth::id())->first();
            @endphp

            @if($akses->role == 'superadmin')
                <p><strong>Status: SUPER ADMIN.</strong> Anda memiliki akses penuh ke seluruh data Lowongan dan Peserta dari semua divisi.</p>
            @else
                <p><strong>Status: ADMIN BIDANG ({{ $akses->division_name }}).</strong> Data yang ditampilkan pada Dashboard, Lowongan, dan Verifikasi telah difilter otomatis hanya untuk Divisi {{ $akses->division_name }}.</p>
            @endif
        </div>
    </div>
</div>

@endsection