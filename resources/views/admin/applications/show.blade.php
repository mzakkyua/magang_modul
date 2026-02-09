@extends('layouts.admin')

@section('title', 'Detail Lamaran')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="text-gray-500 text-sm uppercase tracking-wider font-bold mb-1">Posisi Dilamar</h3>
            <h2 class="text-2xl font-bold text-gray-800">{{ $application->vacancy->title }}</h2>
            <div class="mt-2 flex gap-3 text-sm">
                <span class="bg-gray-100 px-2 py-1 rounded text-gray-600">
                    <i class="bi bi-building mr-1"></i> Divisi: {{ $application->vacancy->division_name }}
                </span>
                <span class="bg-gray-100 px-2 py-1 rounded text-gray-600">
                    <i class="bi bi-calendar mr-1"></i> Daftar: {{ \Carbon\Carbon::parse($application->submission_date)->format('d M Y') }}
                </span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Data Pelamar / Anggota Kelompok</h3>
            
            @foreach($application->members as $index => $member)
            <div class="mb-4 p-4 border rounded-lg bg-gray-50 hover:bg-white hover:shadow transition">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 bg-blue-100 text-blue-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">
                        {{ $loop->iteration }}
                    </div>
                    
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ $member->user->name }}</h4>
                        <p class="text-sm text-gray-600 mb-2">{{ $member->user->email }}</p>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm mt-2">
                            <div>
                                <span class="text-gray-500 block text-xs">Asal Instansi:</span>
                                <span class="font-medium">{{ $member->user->profile->institution_name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-xs">Jurusan:</span>
                                <span class="font-medium">{{ $member->user->profile->major ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="mt-3 flex gap-2">
                            @if($member->user->profile->cv_path)
                                <a href="{{ asset('storage/' . $member->user->profile->cv_path) }}" target="_blank" class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                    <i class="bi bi-download mr-1"></i> Lihat CV
                                </a>
                            @else
                                <span class="text-xs text-red-500 italic">CV Belum diupload</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-lg shadow sticky top-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Keputusan Verifikasi</h3>

            <div class="mb-6 text-center p-4 bg-gray-50 rounded border">
                <p class="text-xs text-gray-500 uppercase">Status Saat Ini</p>
                <h2 class="text-xl font-bold 
                    {{ $application->status == 'accepted' ? 'text-green-600' : '' }}
                    {{ $application->status == 'rejected' ? 'text-red-600' : '' }}
                    {{ $application->status == 'pending' ? 'text-yellow-600' : '' }}
                ">
                    {{ strtoupper($application->status) }}
                </h2>
                @if($application->admin_feedback)
                    <div class="mt-2 text-xs text-left bg-red-50 p-2 rounded text-red-700 border border-red-200">
                        <strong>Catatan Admin:</strong><br>
                        {{ $application->admin_feedback }}
                    </div>
                @endif
            </div>

            <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ubah Status Menjadi:</label>
                    <select name="status" id="statusSelect" onchange="toggleFeedback()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="verified" {{ $application->status == 'verified' ? 'selected' : '' }}>Verifikasi Berkas (Lolos Admin)</option>
                        <option value="interview" {{ $application->status == 'interview' ? 'selected' : '' }}>Panggil Interview</option>
                        <option value="accepted" {{ $application->status == 'accepted' ? 'selected' : '' }}>DITERIMA (Lolos Magang)</option>
                        <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>DITOLAK (Tidak Lolos)</option>
                    </select>
                </div>

                <div id="feedbackBox" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-red-600 mb-1">Alasan Penolakan (Wajib):</label>
                    <textarea name="admin_feedback" rows="3" class="w-full border-red-300 bg-red-50 rounded-md shadow-sm p-2 text-sm" placeholder="Contoh: Maaf, jurusan tidak sesuai dengan kualifikasi..."></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 shadow transition">
                    Simpan Keputusan
                </button>
            </form>

            <div class="mt-4 pt-4 border-t text-center">
                <a href="{{ route('admin.applications.index') }}" class="text-sm text-gray-500 hover:text-gray-800">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

</div>

<script>
    function toggleFeedback() {
        const status = document.getElementById('statusSelect').value;
        const box = document.getElementById('feedbackBox');
        
        if (status === 'rejected') {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }
    // Jalankan saat load (kali aja statusnya udah rejected)
    toggleFeedback();
</script>

@endsection