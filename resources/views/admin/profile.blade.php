@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <div class="col-span-1">
        <div class="bg-white rounded-lg shadow p-6 text-center border-t-4 border-blue-600">
            
            <div class="mb-4">
                <img id="photoPreview" src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : '' }}" 
                    class="{{ $user->profile_photo_path ? '' : 'hidden' }} h-32 w-32 rounded-full mx-auto object-cover border-4 border-gray-200 shadow-sm" 
                    alt="Foto Profil">

                <div id="initialPlaceholder" class="{{ $user->profile_photo_path ? 'hidden' : '' }} h-32 w-32 rounded-full bg-blue-100 mx-auto flex items-center justify-center text-blue-600 text-5xl font-bold border-4 border-white shadow-sm">
                    {{ substr($user->name, 0, 1) }}
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-800">{{ $user->name }}</h3>
            <p class="text-gray-500 text-sm">{{ $user->email }}</p>
            
            <div class="mt-4 pt-4 border-t text-left">
                <span class="text-xs font-bold text-gray-400 uppercase">Peran / Role</span>
                @php
                    $hakAkses = \App\Models\MagangAccessRight::where('user_id', $user->id)->first();
                @endphp
                <p class="text-sm font-semibold text-blue-600 mt-1">
                    @if($hakAkses)
                        {{ strtoupper(str_replace('_', ' ', $hakAkses->role)) }} 
                        @if($hakAkses->division_name)
                            ({{ $hakAkses->division_name }})
                        @endif
                    @else
                        PEGAWAI
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="col-span-1 md:col-span-2">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-bold text-gray-700">Edit Informasi Akun</h3>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        <ul class="list-disc ml-4">
                            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil Baru (Opsional)</label>
                        <div class="flex items-center">
                            <label class="cursor-pointer bg-white border border-gray-300 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-4 py-2 shadow-sm">
                                <span><i class="bi bi-upload mr-2"></i> Pilih File Foto</span>
                                <input id="photoInput" name="photo" type="file" class="sr-only" accept="image/png, image/jpeg, image/jpg">
                            </label>
                            <span id="fileNameDisplay" class="ml-3 text-sm text-gray-500">Tidak ada file dipilih</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">JPG, JPEG, PNG. Maksimal 2MB.</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 rounded-md shadow-sm p-2 border" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 rounded-md shadow-sm p-2 border" required>
                    </div>

                    <hr class="my-6 border-gray-200">
                    <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-shield-lock mr-2"></i> Ganti Password (Opsional)
                    </h4>

                    <div class="mb-4 relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" class="w-full border-gray-300 rounded-md shadow-sm p-2 border pr-10" placeholder="Kosongkan jika tidak ingin mengganti">
                            <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-blue-600 toggle-password" data-target="current_password"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <div class="relative">
                                <input type="password" name="new_password" id="new_password" class="w-full border-gray-300 rounded-md shadow-sm p-2 border pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-blue-600 toggle-password" data-target="new_password"><i class="bi bi-eye-slash"></i></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ulangi Password Baru</label>
                            <div class="relative">
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm p-2 border pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-blue-600 toggle-password" data-target="new_password_confirmation"><i class="bi bi-eye-slash"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" id="btnSaveProfile" disabled class="bg-blue-600 text-white font-bold py-2 px-6 rounded shadow transition opacity-50 cursor-not-allowed hover:bg-blue-600">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Script kecil untuk preview gambar real-time
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const initialPlaceholder = document.getElementById('initialPlaceholder');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    photoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            fileNameDisplay.innerText = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
                photoPreview.classList.remove('hidden');
                initialPlaceholder.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            fileNameDisplay.innerText = 'Tidak ada file dipilih';
             // Jika batal pilih, kembalikan ke kondisi semula (cek dari DB ada foto gak)
             // (Logic sederhana: anggap balik ke inisial untuk visual feedback cepat)
            @if(!$user->profile_photo_path)
                photoPreview.classList.add('hidden');
                initialPlaceholder.classList.remove('hidden');
            @endif
        }
    });
</script>

@push('scripts')
    @vite(['resources/js/admin/profile.js'])
@endpush

@endsection