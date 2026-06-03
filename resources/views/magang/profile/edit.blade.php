@extends('partials.navprofile')

@section('title', 'Profil Saya')

@section('content')

    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">

            {{-- ===================== HERO BANNER ===================== --}}
            <div class="relative rounded-2xl overflow-hidden mb-6 bg-linear-to-r from-blue-900 via-blue-800 to-blue-700">
                <div
                    class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full translate-x-1/3 -translate-y-1/3 pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-1/3 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 pointer-events-none">
                </div>

                <div
                    class="relative z-10 p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-white/15 border-2 border-white/20 flex items-center justify-center font-extrabold text-2xl text-white shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-white leading-tight">
                                {{ auth()->user()->name ?? 'Peserta' }}
                            </h1>
                            <p class="text-sm text-blue-200 mt-1 flex items-center gap-1.5">
                                <span
                                    class="w-1.5 h-1.5 bg-emerald-400 rounded-full inline-block animate-pulse shrink-0"></span>
                                Akun Peserta Magang Aktif
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0 bg-white/10 border border-white/20 rounded-xl px-4 py-2.5 backdrop-blur-sm">
                        <p class="text-[10px] text-blue-300 uppercase tracking-widest font-bold">Login sebagai</p>
                        <p class="text-sm font-bold text-white mt-0.5">{{ auth()->user()->email ?? '-' }}</p>
                    </div>
                </div>

                {{-- Stats bar kelengkapan profil --}}
                <div
                    class="relative z-10 border-t border-white/10 px-6 sm:px-8 py-3 grid grid-cols-3 divide-x divide-white/10">
                    <div class="text-center pr-4">
                        <p @class([
                            'font-extrabold text-base leading-none',
                            'text-emerald-400' => $profile->full_name ?? false,
                            'text-white/40' => !($profile->full_name ?? false),
                        ])>
                            {{ $profile->full_name ?? false ? '✓' : '—' }}
                        </p>
                        <p class="text-blue-300 text-[10px] mt-0.5">Data Diri</p>
                    </div>
                    <div class="text-center px-4">
                        <p @class([
                            'font-extrabold text-base leading-none',
                            'text-emerald-400' => $profile->cv_file_path ?? false,
                            'text-white/40' => !($profile->cv_file_path ?? false),
                        ])>
                            {{ $profile->cv_file_path ?? false ? '✓' : '—' }}
                        </p>
                        <p class="text-blue-300 text-[10px] mt-0.5">CV</p>
                    </div>
                    <div class="text-center pl-4">
                        <p @class([
                            'font-extrabold text-base leading-none',
                            'text-emerald-400' => $profile->proposal_file_path ?? false,
                            'text-white/40' => !($profile->proposal_file_path ?? false),
                        ])>
                            {{ $profile->proposal_file_path ?? false ? '✓' : '—' }}
                        </p>
                        <p class="text-blue-300 text-[10px] mt-0.5">Proposal</p>
                    </div>
                </div>
            </div>

            {{-- ===================== FLASH MESSAGES ===================== --}}
            @if (session('success'))
                <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-5">
                    <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                    <div>
                        <p class="font-bold text-emerald-700 text-sm">Berhasil!</p>
                        <p class="text-emerald-600 text-xs mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-2xl p-4 mb-5">
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

            {{-- ============================================================ --}}
            {{-- ✅ FIX: DELETE CV FORM DIPINDAH KE SINI — DI LUAR profileForm --}}
            {{-- Root cause bug: form bersarang (nested form) menyebabkan       --}}
            {{-- @method('DELETE') menimpa @method('PUT') di form utama,        --}}
            {{-- sehingga setiap submit profileForm terbaca sebagai DELETE.      --}}
            {{-- ============================================================ --}}
            @if ($profile->cv_file_path ?? false)
                <form id="delete-cv-form" action="{{ route('profile.delete.cv') }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif

            {{-- ===================== FORM UTAMA ===================== --}}
            <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid lg:grid-cols-[200px_1fr] gap-5">

                    {{-- ===================== SIDENAV KIRI ===================== --}}
                    <div class="hidden lg:flex flex-col gap-1">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest px-3 mb-2">Pengaturan
                        </p>

                        <a href="#sec-info"
                            class="sidenav-link flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold bg-white text-blue-600 shadow-sm border border-gray-100 transition-all">
                            <div class="w-6 h-6 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                <i class="bi bi-person-fill text-blue-500 text-xs"></i>
                            </div>
                            Informasi
                        </a>

                        <a href="#sec-docs"
                            class="sidenav-link flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:bg-white hover:text-gray-800 hover:shadow-sm transition-all">
                            <div class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                <i class="bi bi-paperclip text-gray-400 text-xs"></i>
                            </div>
                            Dokumen
                        </a>

                        <div class="h-px bg-gray-100 my-2 mx-1"></div>

                        <a href="#sec-security"
                            class="sidenav-link flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:bg-white hover:text-gray-800 hover:shadow-sm transition-all">
                            <div class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                <i class="bi bi-shield-lock-fill text-gray-400 text-xs"></i>
                            </div>
                            Keamanan
                        </a>

                        <div class="mt-6 bg-amber-50 border border-amber-100 rounded-xl px-3 py-3 text-center">
                            <i class="bi bi-info-circle text-amber-500 text-sm mb-1 block"></i>
                            <p class="text-[10px] text-amber-600 leading-relaxed">
                                Pastikan data sesuai identitas resmi untuk kelancaran sertifikasi.
                            </p>
                        </div>
                    </div>

                    {{-- ===================== KONTEN KANAN ===================== --}}
                    <div class="space-y-5">

                        {{-- ── SEKSI 1: INFORMASI PRIBADI ── --}}
                        <div id="sec-info"
                            class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden scroll-mt-6">

                            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                    <i class="bi bi-person-badge-fill text-blue-500 text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm font-extrabold text-gray-900">Informasi Pribadi</h2>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Data identitas sesuai dokumen resmi</p>
                                </div>
                            </div>

                            <div class="p-5">
                                <div class="grid md:grid-cols-2 gap-4">

                                    @php
                                        $fields = [
                                            [
                                                'name' => 'full_name',
                                                'label' => 'Nama Lengkap',
                                                'val' => $profile->full_name ?? '',
                                                'span' => 'md:col-span-2',
                                                'required' => true,
                                            ],
                                            [
                                                'name' => 'nim_nisn',
                                                'label' => 'NIM / NISN',
                                                'val' => $profile->nim_nisn ?? '',
                                                'required' => true,
                                            ],
                                            [
                                                'name' => 'institution_name',
                                                'label' => 'Asal Instansi',
                                                'val' => $profile->institution_name ?? '',
                                                'required' => true,
                                            ],
                                            [
                                                'name' => 'major',
                                                'label' => 'Jurusan',
                                                'val' => $profile->major ?? '',
                                                'required' => true,
                                            ],
                                            [
                                                'name' => 'phone_number',
                                                'label' => 'No. WhatsApp',
                                                'val' => $profile->phone_number ?? '',
                                                'required' => true,
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($fields as $index => $field)
                                        <div class="{{ $field['span'] ?? '' }}">
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                                                {{ $field['label'] }} @if ($field['required'])
                                                    <span class="text-red-400">*</span>
                                                @endif
                                            </label>
                                            <input type="text" name="{{ $field['name'] }}"
                                                value="{{ old($field['name'], $field['val']) }}"
                                                @class([
                                                    'w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800 outline-none transition-all duration-200 bg-white placeholder:text-gray-300 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100',
                                                    'border-gray-200' => !$errors->has($field['name']),
                                                    'border-red-300 focus:ring-red-100' => $errors->has($field['name']),
                                                ])>
                                            @error($field['name'])
                                                <p class="flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5">
                                                    <i class="bi bi-exclamation-circle-fill text-[11px]"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        @if ($index == 2)
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                                                    Jenjang <span class="text-red-400">*</span>
                                                </label>
                                                <select name="education_level" @class([
                                                    'w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800 outline-none appearance-none transition-all duration-200 bg-white hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100',
                                                    'border-gray-200' => !$errors->has('education_level'),
                                                    'border-red-300 focus:ring-red-100' => $errors->has('education_level'),
                                                ])>
                                                    <option value="">Pilih Jenjang</option>
                                                    <option value="SMA" @selected(old('education_level', $profile->education_level ?? '') == 'SMA')>SMA</option>
                                                    <option value="SMK" @selected(old('education_level', $profile->education_level ?? '') == 'SMK')>SMK</option>
                                                    <option value="D3" @selected(old('education_level', $profile->education_level ?? '') == 'D3')>D3</option>
                                                    <option value="S1" @selected(old('education_level', $profile->education_level ?? '') == 'S1')>S1 / D4</option>
                                                    <option value="S2" @selected(old('education_level', $profile->education_level ?? '') == 'S2')>S2</option>
                                                </select>
                                                @error('education_level')
                                                    <p class="flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5">
                                                        <i class="bi bi-exclamation-circle-fill text-[11px]"></i>
                                                        {{ $message }}
                                                    </p>
                                                @enderror
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- Alamat --}}
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                                            Alamat Lengkap <span class="text-red-400">*</span>
                                        </label>
                                        <textarea name="address" rows="3" @class([
                                            'w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800 outline-none resize-none transition-all duration-200 bg-white placeholder:text-gray-300 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100',
                                            'border-gray-200' => !$errors->has('address'),
                                            'border-red-300 focus:ring-red-100' => $errors->has('address'),
                                        ])>{{ old('address', $profile->address ?? '') }}</textarea>
                                        @error('address')
                                            <p class="flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5">
                                                <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ── SEKSI 2: DOKUMEN LAMPIRAN ── --}}
                        <div id="sec-docs"
                            class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden scroll-mt-6">
                            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                                    <i class="bi bi-paperclip text-amber-500 text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm font-extrabold text-gray-900">Dokumen Lampiran</h2>
                                    <p class="text-[10px] text-gray-400 mt-0.5">CV wajib. Proposal hanya untuk program
                                        penelitian.</p>
                                </div>
                            </div>

                            <div class="p-5 grid md:grid-cols-2 gap-5">

                                {{-- ── CV ── --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                        Curriculum Vitae (CV) @if (!($profile->cv_file_path ?? false))
                                            <span class="text-red-400">*</span>
                                        @endif
                                    </label>

                                    @if ($profile->cv_file_path ?? false)
                                        <div
                                            class="flex items-center justify-between gap-2 mb-3 bg-blue-50 border border-blue-100 rounded-xl px-3 py-2.5">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div
                                                    class="w-7 h-7 bg-white rounded-lg border border-blue-100 flex items-center justify-center shrink-0">
                                                    <i class="bi bi-file-earmark-pdf-fill text-blue-500 text-xs"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-blue-800 truncate">CV_Tersimpan.pdf
                                                    </p>
                                                    <p class="text-[10px] text-blue-400">Sudah terunggah</p>
                                                </div>
                                            </div>
                                            <div class="flex gap-1.5 shrink-0">
                                                <a href="{{ route('profile.cv.view') }}" target="_blank"
                                                    class="w-7 h-7 bg-white rounded-lg border border-blue-200 flex items-center justify-center text-blue-500 hover:bg-blue-600 hover:text-white transition-all text-xs">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                {{-- ✅ FIX: Tombol hapus CV tetap di sini, form-nya sudah dipindah ke luar --}}
                                                <button type="button" onclick="confirmHapusCV()"
                                                    class="w-7 h-7 bg-white rounded-lg border border-red-200 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all text-xs">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                        {{-- ✅ FIX: Form delete-cv TIDAK lagi di sini. Sudah dipindah ke luar profileForm. --}}
                                        <p class="text-[10px] text-gray-400 mb-2">
                                            <i class="bi bi-info-circle mr-0.5"></i> Pilih file baru untuk mengganti:
                                        </p>
                                    @endif

                                    <label for="cvInput" @class([
                                        'group flex flex-col items-center gap-2 w-full py-8 rounded-xl cursor-pointer border-2 border-dashed transition-all duration-200',
                                        'border-red-200 bg-red-50/30' => $errors->has('cv_file'),
                                        'border-gray-200 bg-gray-50/40 hover:border-blue-300 hover:bg-blue-50/30' => !$errors->has(
                                            'cv_file'),
                                    ])>
                                        <div
                                            class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center group-hover:border-blue-200 transition-all shadow-sm">
                                            <i
                                                class="bi bi-cloud-arrow-up text-gray-400 group-hover:text-blue-500 text-lg transition-colors"></i>
                                        </div>
                                        <div class="text-center" id="cvDropText">
                                            <p
                                                class="text-sm font-semibold text-gray-500 group-hover:text-blue-600 transition-colors">
                                                Pilih file CV</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">PDF, maks 2MB</p>
                                        </div>
                                        <div id="cvPreview"
                                            class="hidden items-center gap-1.5 bg-white border border-blue-200 rounded-lg px-3 py-1.5 text-xs font-bold text-blue-600">
                                            <i class="bi bi-file-earmark-check-fill"></i>
                                            <span id="cvFileName"></span>
                                        </div>
                                        <input type="file" name="cv_file" id="cvInput" accept=".pdf"
                                            class="hidden">
                                    </label>
                                    @error('cv_file')
                                        <p class="flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5">
                                            <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- ── PROPOSAL ── --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                        Proposal <span
                                            class="text-[10px] normal-case font-medium text-gray-400 ml-1">(Penelitian)</span>
                                    </label>

                                    @if ($profile->proposal_file_path ?? false)
                                        <div
                                            class="flex items-center gap-2 bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2.5 mb-3">
                                            <i class="bi bi-check-circle-fill text-emerald-500 text-sm shrink-0"></i>
                                            <div>
                                                <p class="text-xs font-bold text-emerald-800">File tersimpan</p>
                                                <p class="text-[10px] text-emerald-500">Pilih baru untuk mengganti</p>
                                            </div>
                                        </div>
                                    @endif

                                    <label for="proposalInput" @class([
                                        'group flex flex-col items-center gap-2 w-full py-8 rounded-xl cursor-pointer border-2 border-dashed transition-all duration-200',
                                        'border-red-200 bg-red-50/30' => $errors->has('proposal_file'),
                                        'border-gray-200 bg-gray-50/40 hover:border-blue-300 hover:bg-blue-50/30' => !$errors->has(
                                            'proposal_file'),
                                    ])>
                                        <div
                                            class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center group-hover:border-blue-200 transition-all shadow-sm">
                                            <i
                                                class="bi bi-cloud-arrow-up text-gray-400 group-hover:text-blue-500 text-lg transition-colors"></i>
                                        </div>
                                        <div class="text-center" id="proposalDropText">
                                            <p
                                                class="text-sm font-semibold text-gray-500 group-hover:text-blue-600 transition-colors">
                                                Pilih file Proposal</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">PDF, maks 5MB</p>
                                        </div>
                                        <div id="proposalPreview"
                                            class="hidden items-center gap-1.5 bg-white border border-blue-200 rounded-lg px-3 py-1.5 text-xs font-bold text-blue-600">
                                            <i class="bi bi-file-earmark-check-fill"></i>
                                            <span id="proposalFileName"></span>
                                        </div>
                                        <input type="file" name="proposal_file" id="proposalInput" accept=".pdf"
                                            class="hidden">
                                    </label>
                                    @error('proposal_file')
                                        <p class="flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5">
                                            <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        {{-- ── SEKSI 3: KEAMANAN AKUN ── --}}
                        <div id="sec-security"
                            class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden scroll-mt-6">
                            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                                    <i class="bi bi-shield-lock-fill text-amber-500 text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm font-extrabold text-gray-900">Keamanan Akun</h2>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Kosongkan semua field jika tidak ingin
                                        mengganti password</p>
                                </div>
                            </div>

                            <div class="p-5 grid md:grid-cols-3 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Password
                                        Lama</label>
                                    <input type="password" name="current_password" placeholder="••••••••"
                                        @class([
                                            'w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800 outline-none transition-all duration-200 bg-white placeholder:text-gray-300 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100',
                                            'border-gray-200' => !$errors->has('current_password'),
                                            'border-red-300 focus:ring-red-100' => $errors->has('current_password'),
                                        ])>
                                    @error('current_password')
                                        <p class="flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5">
                                            <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Password
                                        Baru</label>
                                    <input type="password" name="password" placeholder="••••••••"
                                        @class([
                                            'w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800 outline-none transition-all duration-200 bg-white placeholder:text-gray-300 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100',
                                            'border-gray-200' => !$errors->has('password'),
                                            'border-red-300 focus:ring-red-100' => $errors->has('password'),
                                        ])>
                                    @error('password')
                                        <p class="flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5">
                                            <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Ulangi
                                        Password</label>
                                    <input type="password" name="password_confirmation" placeholder="••••••••"
                                        class="w-full px-3.5 py-2.5 rounded-xl border-gray-200 border text-sm font-medium text-gray-800 outline-none transition-all duration-200 bg-white placeholder:text-gray-300 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                            </div>
                        </div>

                        {{-- ── TOMBOL SIMPAN ── --}}
                        <div class="flex items-center justify-between gap-4 pt-1">
                            <button type="submit" id="btnSubmit"
                                class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-sm font-bold py-3 px-5 rounded-xl shadow-md shadow-blue-600/25 hover:-translate-y-0.5 transition-all duration-200">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <span id="btnText">Simpan Perubahan</span>
                            </button>
                        </div>

                        <p id="noChangeAlert"
                            class="hidden text-red-500 text-xs font-semibold text-center -mt-2 items-center justify-center gap-1">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Tidak ada perubahan. Silakan ubah data terlebih dahulu.
                        </p>

                    </div>
                </div>
            </form>
            {{-- ===================== END FORM UTAMA ===================== --}}

        </div>
    </div>

    {{-- ===================== SCRIPT ===================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const btnSubmit = document.getElementById('btnSubmit');
            const btnText = document.getElementById('btnText');
            const noChangeAlert = document.getElementById('noChangeAlert');
            let hasChanged = false;

            form.querySelectorAll('input, textarea, select').forEach(input => {
                const markChanged = () => {
                    hasChanged = true;
                    noChangeAlert.classList.add('hidden');
                };
                input.addEventListener('input', markChanged);
                input.addEventListener('change', markChanged);
            });

            form.addEventListener('submit', function(e) {
                if (!hasChanged) {
                    e.preventDefault();
                    noChangeAlert.classList.remove('hidden');
                    noChangeAlert.classList.add('flex', 'animate-pulse');
                    setTimeout(() => {
                        noChangeAlert.classList.remove('animate-pulse');
                        noChangeAlert.classList.add('hidden');
                        noChangeAlert.classList.remove('flex');
                    }, 3000);
                    return false;
                }
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-70', 'cursor-not-allowed');
                btnText.textContent = 'Menyimpan...';
            });

            // ── Preview file CV setelah dipilih ──
            const cvInput = document.getElementById('cvInput');
            const cvPreview = document.getElementById('cvPreview');
            const cvDropText = document.getElementById('cvDropText');
            const cvFileName = document.getElementById('cvFileName');

            cvInput?.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const name = this.files[0].name;
                    cvFileName.textContent = name.length > 26 ? name.substring(0, 23) + '...' : name;
                    cvDropText.classList.add('hidden');
                    cvPreview.classList.remove('hidden');
                    cvPreview.classList.add('flex');
                }
            });

            // ── Preview file Proposal setelah dipilih ──
            const proposalInput = document.getElementById('proposalInput');
            const proposalPreview = document.getElementById('proposalPreview');
            const proposalDropText = document.getElementById('proposalDropText');
            const proposalFileName = document.getElementById('proposalFileName');

            proposalInput?.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const name = this.files[0].name;
                    proposalFileName.textContent = name.length > 26 ? name.substring(0, 23) + '...' : name;
                    proposalDropText.classList.add('hidden');
                    proposalPreview.classList.remove('hidden');
                    proposalPreview.classList.add('flex');
                }
            });

            // ── Highlight sidenav berdasarkan scroll ──
            const sections = ['sec-info', 'sec-docs', 'sec-security'];
            const navLinks = document.querySelectorAll('.sidenav-link');
            const activeClass = ['bg-white', 'text-blue-600', 'shadow-sm', 'border', 'border-gray-100'];
            const inactiveClass = ['text-gray-500'];

            window.addEventListener('scroll', () => {
                let current = sections[0];
                sections.forEach(id => {
                    const el = document.getElementById(id);
                    if (el && window.scrollY >= el.offsetTop - 120) current = id;
                });
                navLinks.forEach(link => {
                    const href = link.getAttribute('href')?.replace('#', '');
                    if (href === current) {
                        link.classList.add(...activeClass);
                        link.classList.remove('text-gray-500');
                    } else {
                        link.classList.remove(...activeClass);
                        link.classList.add(...inactiveClass);
                    }
                });
            });
        });

        function confirmHapusCV() {
            Swal.fire({
                title: 'Hapus CV?',
                text: 'File CV Anda akan dihapus permanen dan tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-cv-form').submit();
                }
            });
        }
    </script>
@endsection
