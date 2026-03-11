@extends('layouts.layoutlanding')
@section('title','Riwayat Pengajuan')
@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Judul --}}
        <div class="text-center mb-12">
            <p class="text-blue-600 font-bold uppercase tracking-widest text-sm mb-2">Pendaftaran Magang</p>
            <h1 class="text-4xl font-extrabold text-[#37517e]">Riwayat Pengajuan</h1>
        </div>

        {{-- Tabel Riwayat --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#2d3748] text-white">
                        <th class="px-6 py-4 font-bold uppercase text-xs">No.</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs">Posisi Magang</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs text-center">Tanggal Pengajuan</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs text-center">Status Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($applications as $index => $app)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800 block">{{ $app->vacancy->title ?? 'Posisi Terhapus' }}</span>
                            <span class="text-xs text-gray-400 italic">Dinas Tenaga Kerja Prov. Jatim</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-center">
                            {{ $app->submission_date->format('d F Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($app->status == 'accepted')
                                <span class="bg-emerald-100 text-emerald-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase border border-emerald-200">Telah Disetujui</span>
                            @elseif($app->status == 'rejected')
                                <span class="bg-red-100 text-red-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase border border-red-200">Ditolak</span>
                            @else
                                <span class="bg-amber-100 text-amber-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase border border-amber-200">Menunggu Review</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">
                            Belum ada riwayat pengajuan magang.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection