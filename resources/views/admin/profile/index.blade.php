@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')

    <div class="max-w-5xl mx-auto">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- ===================== KOLOM KIRI: FOTO & INFO ===================== --}}
            <div class="col-span-1 space-y-4">

                {{-- Card Foto Profil --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">

                    {{-- Avatar --}}
                    <div class="mb-4 relative inline-block group">

                        <img id="photoPreview"
                            src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : '' }}"
                            @class([
                                'h-28 w-28 rounded-2xl mx-auto object-cover border-4 border-white shadow-lg',
                                'hidden' => !$user->profile_photo_path,
                            ]) alt="Foto Profil">

                        <div id="initialPlaceholder" @class([
                            'h-28 w-28 rounded-2xl bg-linear-to-br from-blue-500 to-blue-600 mx-auto flex items-center justify-center text-white text-4xl font-extrabold shadow-lg border-4 border-white',
                            'hidden' => $user->profile_photo_path,
                        ])>
                            {{ substr($user->name, 0, 1) }}
                        </div>

                        {{-- Tombol Hapus Foto --}}
                        <button type="button" id="btnDeletePhoto" @class([
                            'absolute -bottom-2 -right-2 w-8 h-8 bg-red-500 text-white rounded-xl flex items-center justify-center hover:bg-red-600 shadow-md transition',
                            'hidden' => !$user->profile_photo_path,
                        ]) title="Hapus Foto Profil">
                            <i class="bi bi-trash-fill text-xs"></i>
                        </button>
                    </div>

                    {{-- Nama & Email --}}
                    <h3 class="text-base font-extrabold text-gray-900 mt-3">{{ $user->name }}</h3>
                    <p class="text-gray-400 text-xs mt-0.5">{{ $user->email }}</p>

                    {{-- Badge Role --}}
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        @php
                            $hakAkses = \App\Models\MagangAccessRight::where('user_id', $user->id)->first();
                            $isSuperAdmin = $hakAkses && $hakAkses->role === 'superadmin';
                        @endphp

                        @if ($hakAkses)
                            <span @class([
                                'inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border',
                                'bg-blue-50 text-blue-600 border-blue-200' => $isSuperAdmin,
                                'bg-indigo-50 text-indigo-600 border-indigo-200' => !$isSuperAdmin,
                            ])>
                                <i class="bi bi-{{ $isSuperAdmin ? 'shield-fill-check' : 'building' }} text-[10px]"></i>
                                {{ strtoupper(str_replace('_', ' ', $hakAkses->role)) }}
                                @if ($hakAkses->division_name)
                                    — {{ $hakAkses->division_name }}
                                @endif
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                                <i class="bi bi-person text-[10px]"></i> PEGAWAI
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Card Info Singkat --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-3">Info Akun</p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2.5 text-sm text-gray-600">
                            <i class="bi bi-person-circle text-blue-400 text-base w-5 text-center"></i>
                            <span class="truncate">{{ $user->name }}</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-sm text-gray-600">
                            <i class="bi bi-envelope text-blue-400 text-base w-5 text-center"></i>
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-sm text-gray-600">
                            <i class="bi bi-calendar3 text-blue-400 text-base w-5 text-center"></i>
                            <span>Bergabung {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===================== KOLOM KANAN: FORM ===================== --}}
            <div class="col-span-1 md:col-span-2 space-y-5">

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl p-4">
                        <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                        <div>
                            <p class="font-bold text-emerald-700 text-sm">Berhasil!</p>
                            <p class="text-emerald-600 text-xs mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-2xl p-4">
                        <i class="bi bi-exclamation-circle-fill text-red-400 mt-0.5 shrink-0"></i>
                        <div>
                            <p class="font-bold text-red-700 text-sm mb-1">Terdapat kesalahan:</p>
                            <ul class="space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-500 text-xs">• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="delete_photo" id="deletePhotoInput" value="0">

                    {{-- ============ CARD: FOTO & INFO AKUN ============ --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
                        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100">
                            <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                                <i class="bi bi-person text-blue-600 text-xs"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 text-sm">Informasi Akun</h3>
                        </div>

                        <div class="p-6 space-y-5">
                            {{-- Upload Foto --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Profil</label>
                                <div class="flex items-center gap-3">
                                    <label
                                        class="cursor-pointer inline-flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-50 hover:border-blue-300 transition shadow-sm">
                                        <i class="bi bi-upload text-xs"></i> Upload Foto
                                        <input id="photoInput" name="photo" type="file" class="sr-only"
                                            accept="image/png, image/jpeg, image/jpg">
                                    </label>
                                    <span id="fileNameDisplay" class="text-xs text-gray-400">Tidak ada file dipilih</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5">Format: JPG, JPEG, PNG. Maks. 2MB.</p>
                            </div>

                            {{-- Nama Lengkap --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                        </div>
                    </div>

                    {{-- ============ CARD: GANTI PASSWORD ============ --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
                        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100">
                            <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
                                <i class="bi bi-shield-lock text-amber-500 text-xs"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm">Ganti Password</h3>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <p class="text-xs text-gray-400 -mt-1">Kosongkan semua field password jika tidak ingin
                                menggantinya.</p>

                            {{-- Password Lama --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Lama</label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="current_password"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition pr-11">
                                    <button type="button"
                                        class="absolute inset-y-0 right-0 px-3.5 flex items-center text-gray-400 hover:text-blue-600 toggle-password transition"
                                        data-target="current_password">
                                        <i class="bi bi-eye-slash text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Password Baru & Konfirmasi --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Baru</label>
                                    <div class="relative">
                                        <input type="password" name="new_password" id="new_password"
                                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition pr-11">
                                        <button type="button"
                                            class="absolute inset-y-0 right-0 px-3.5 flex items-center text-gray-400 hover:text-blue-600 toggle-password transition"
                                            data-target="new_password">
                                            <i class="bi bi-eye-slash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ulangi Password
                                        Baru</label>
                                    <div class="relative">
                                        <input type="password" name="new_password_confirmation"
                                            id="new_password_confirmation"
                                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition pr-11">
                                        <button type="button"
                                            class="absolute inset-y-0 right-0 px-3.5 flex items-center text-gray-400 hover:text-blue-600 toggle-password transition"
                                            data-target="new_password_confirmation">
                                            <i class="bi bi-eye-slash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ============ TOMBOL SIMPAN ============ --}}
                    <div class="flex justify-end">
                        <button type="button" id="btnSaveProfile" disabled
                            class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-md shadow-blue-600/20 transition opacity-50 cursor-not-allowed text-sm">
                            <span id="btnText">
                                <i class="bi bi-check-lg mr-1"></i> Simpan Perubahan
                            </span>
                            <span id="btnLoading" class="hidden items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ===================== SCRIPT ===================== --}}
    <script>
        const photoInput = document.getElementById('photoInput');
        const photoPreview = document.getElementById('photoPreview');
        const initialPlaceholder = document.getElementById('initialPlaceholder');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const btnDeletePhoto = document.getElementById('btnDeletePhoto');
        const deletePhotoInput = document.getElementById('deletePhotoInput');

        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                fileNameDisplay.innerText = file.name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreview.classList.remove('hidden');
                    initialPlaceholder.classList.add('hidden');

                    if (btnDeletePhoto) btnDeletePhoto.classList.remove('hidden');
                    deletePhotoInput.value = '0';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    @push('scripts')
        @vite(['resources/js/admin/profile.js'])
    @endpush

@endsection
