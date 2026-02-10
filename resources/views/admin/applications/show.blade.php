@extends('layouts.admin')

@section('title', 'Detail Lamaran')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ================= LEFT SIDE ================= --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- POSISI DILAMAR --}}
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm uppercase tracking-wider font-bold mb-1">Posisi Dilamar</h3>
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $application->vacancy->title }}
                </h2>

                <div class="mt-2 flex gap-3 text-sm">
                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-600">
                        <i class="bi bi-building mr-1"></i>
                        Divisi: {{ $application->vacancy->division_name }}
                    </span>

                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-600">
                        <i class="bi bi-calendar mr-1"></i>
                        Daftar: {{ \Carbon\Carbon::parse($application->submission_date)->format('d M Y') }}
                    </span>
                </div>
            </div>

            {{-- DATA PELAMAR --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                    Data Pelamar / Anggota Kelompok
                </h3>

                @foreach ($application->members as $member)
                    <div class="mb-4 p-4 border rounded-lg bg-gray-50">
                        <h4 class="font-bold text-gray-900">
                            {{ $member->user->name }}
                        </h4>

                        <p class="text-sm text-gray-600">
                            {{ $member->user->email }}
                        </p>

                        <div class="grid grid-cols-2 gap-2 text-sm mt-2">
                            <div>
                                <span class="text-xs text-gray-500">Instansi</span>
                                <div class="font-medium">
                                    {{ $member->user->profile->institution_name ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500">Jurusan</span>
                                <div class="font-medium">
                                    {{ $member->user->profile->major ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ================= RIGHT SIDE ================= --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow sticky top-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    Keputusan Verifikasi
                </h3>

                {{-- STATUS SAAT INI --}}
                <div class="mb-5 text-center p-4 bg-gray-50 rounded border">
                    <p class="text-xs text-gray-500 uppercase">Status Saat Ini</p>

                    <h2
                        class="text-xl font-bold
                    {{ $application->status === 'accepted' ? 'text-green-600' : '' }}
                    {{ $application->status === 'rejected' ? 'text-red-600' : '' }}
                    {{ in_array($application->status, ['pending', 'verified', 'interview']) ? 'text-yellow-600' : '' }}">
                        {{ strtoupper($application->status) }}
                    </h2>

                    @if ($application->admin_feedback)
                        <div class="mt-2 text-xs bg-red-50 text-red-700 p-2 rounded border">
                            <strong>Catatan Admin:</strong><br>
                            {{ $application->admin_feedback }}
                        </div>
                    @endif
                </div>

                {{-- ======== APPROVE ======== --}}
                <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST"
                    class="form-approve mb-3"
                    data-name="{{ $application->members->first()->user->name ?? 'Pelamar ini' }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="accepted">

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded">
                        ✅ TERIMA LAMARAN
                    </button>
                </form>

                {{-- ======== REJECT ======== --}}
                <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST"
                    class="form-reject" data-name="{{ $application->members->first()->user->name ?? 'Pelamar ini' }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <input type="hidden" name="admin_feedback">

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded">
                        ❌ TOLAK LAMARAN
                    </button>
                </form>

                <div class="mt-4 pt-4 border-t text-center">
                    <a href="{{ route('admin.applications.index') }}" class="text-sm text-gray-500 hover:text-gray-800">
                        ← Kembali ke Daftar Lamaran
                    </a>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/delete-confirm.js'])
@endpush
