@extends('layouts.admin')

@section('title', 'Manajemen Lowongan Magang')

@section('content')

<div class="flex justify-between items-center mb-6">
    <p class="text-gray-600">Kelola daftar lowongan magang yang tersedia.</p>
    <a href="{{ route('admin.vacancies.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
        <i class="bi bi-plus-lg mr-2"></i> Buat Lowongan Baru
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul & Divisi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe & Mode</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kuota</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($vacancies as $vacancy)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $vacancy->title }}</div>
                    <div class="text-xs text-gray-500 bg-gray-100 inline-block px-2 py-1 rounded mt-1">
                        Divisi: {{ $vacancy->division_name }}
                    </div>
                </td>

                <td class="px-6 py-4">
                    <span class="block text-sm text-gray-700 capitalize">{{ $vacancy->type }}</span>
                    <span class="text-xs text-gray-500 capitalize">Mode: {{ $vacancy->registration_mode }}</span>
                </td>

                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $vacancy->quota_slots }} Slot
                    </span>
                </td>

                <td class="px-6 py-4 text-center text-sm text-gray-500">
                    <div>{{ \Carbon\Carbon::parse($vacancy->start_date)->format('d M Y') }}</div>
                    <div class="text-xs">s/d</div>
                    <div>{{ \Carbon\Carbon::parse($vacancy->end_date)->format('d M Y') }}</div>
                </td>

                <td class="px-6 py-4 text-center">
                    @if($vacancy->status == 'open')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            DIBUKA
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            DITUTUP
                        </span>
                    @endif
                </td>

                <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                    <a href="{{ route('admin.vacancies.edit', $vacancy->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </a>

                    <form action="{{ route('admin.vacancies.toggle', $vacancy->id) }}" method="POST" class="inline-block">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-gray-500 hover:text-gray-900" title="Ubah Status (Buka/Tutup)">
                            @if($vacancy->status == 'open')
                                <i class="bi bi-toggle-on text-green-600 text-xl"></i>
                            @else
                                <i class="bi bi-toggle-off text-gray-400 text-xl"></i>
                            @endif
                        </button>
                    </form>

                    <form action="{{ route('admin.vacancies.destroy', $vacancy->id) }}" method="POST" class="inline-block form-delete" data-name="{{ $vacancy->title }}">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus Permanen">
                            <i class="bi bi-trash text-lg"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                    Belum ada lowongan dibuat. Silakan klik tombol "Buat Lowongan Baru".
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $vacancies->links() }}
    </div>
</div>

@push('scripts')
    @vite(['resources/js/admin/delete-confirm.js'])
@endpush

@endsection