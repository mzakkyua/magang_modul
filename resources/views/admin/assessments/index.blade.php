@extends('layouts.admin')

@section('title', 'Penilaian Peserta Magang')

@section('content')

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Daftar Peserta Aktif</h3>
        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
            Hanya peserta berstatus "DITERIMA" yang muncul disini
        </span>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi / Divisi</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Nilai</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{-- PERBAIKAN: Gunakan $members (sesuai controller), bukan $participants --}}
            @forelse($members as $member)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            {{ substr($member->user->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-bold text-gray-900">{{ $member->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $member->user->email }}</div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 font-medium">{{ $member->application->vacancy->title }}</div>
                    <div class="text-xs text-gray-500">{{ $member->application->vacancy->division_name }}</div>
                </td>

                <td class="px-6 py-4 text-center">
                    {{-- Cek relasi assessment (sesuai model AssessmentMagang) --}}
                    @if($member->assessment)
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Sudah Dinilai ({{ number_format($member->assessment->final_score, 2) }})
                        </span>
                    @else
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Belum Dinilai
                        </span>
                    @endif
                </td>

                <td class="px-6 py-4 text-center">
                    {{-- Route arahkan ke assessments.create --}}
                    <a href="{{ route('admin.assessments.create', $member->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-4 rounded shadow transition">
                        <i class="bi bi-pencil-square mr-1"></i> 
                        {{ $member->assessment ? 'Edit Nilai' : 'Input Nilai' }}
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                    <i class="bi bi-people text-4xl mb-2 block"></i>
                    Belum ada peserta magang yang berstatus "Diterima".<br>
                    Silakan terima pelamar di menu <strong>Verifikasi Lamaran</strong> dulu.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t">
        {{ $members->links() }}
    </div>
</div>

@endsection