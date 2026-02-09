@extends('layouts.admin')

@section('title', 'Verifikasi Pelamar')

@section('content')

<div class="flex gap-2 mb-6">
    <a href="{{ route('admin.applications.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded hover:bg-gray-50 text-sm {{ !request('status') ? 'font-bold text-blue-600 ring-2 ring-blue-100' : 'text-gray-600' }}">
        Semua
    </a>
    <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}" class="px-4 py-2 bg-yellow-50 border border-yellow-200 rounded hover:bg-yellow-100 text-sm text-yellow-700">
        Menunggu (Pending)
    </a>
    <a href="{{ route('admin.applications.index', ['status' => 'accepted']) }}" class="px-4 py-2 bg-green-50 border border-green-200 rounded hover:bg-green-100 text-sm text-green-700">
        Diterima
    </a>
    <a href="{{ route('admin.applications.index', ['status' => 'rejected']) }}" class="px-4 py-2 bg-red-50 border border-red-200 rounded hover:bg-red-100 text-sm text-red-700">
        Ditolak
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelamar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lowongan Dilamar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($data as $app)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                            <i class="bi bi-person-fill text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-bold text-gray-900">
                                {{ $app->leader->name ?? 'User Terhapus' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $app->leader->email ?? '-' }}
                            </div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 font-medium">{{ $app->vacancy->title }}</div>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        Divisi: {{ $app->vacancy->division_name }}
                    </span>
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($app->submission_date)->format('d M Y, H:i') }}
                </td>

                <td class="px-6 py-4 text-center">
                    @if($app->status == 'pending')
                        <span class="px-2 py-1 text-xs font-bold rounded bg-yellow-100 text-yellow-800">MENUNGGU</span>
                    @elseif($app->status == 'verified')
                        <span class="px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-800">DIVERIFIKASI</span>
                    @elseif($app->status == 'interview')
                        <span class="px-2 py-1 text-xs font-bold rounded bg-purple-100 text-purple-800">INTERVIEW</span>
                    @elseif($app->status == 'accepted')
                        <span class="px-2 py-1 text-xs font-bold rounded bg-green-100 text-green-800">DITERIMA</span>
                    @elseif($app->status == 'rejected')
                        <span class="px-2 py-1 text-xs font-bold rounded bg-red-100 text-red-800">DITOLAK</span>
                    @endif
                </td>

                <td class="px-6 py-4 text-center">
                    <a href="{{ route('admin.applications.show', $app->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm border border-blue-600 px-3 py-1 rounded hover:bg-blue-50 transition">
                        Periksa Berkas &rarr;
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                    <i class="bi bi-inbox text-4xl block mb-2"></i>
                    Belum ada lamaran masuk untuk divisi Anda.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t">
        {{ $data->links() }}
    </div>
</div>

@endsection