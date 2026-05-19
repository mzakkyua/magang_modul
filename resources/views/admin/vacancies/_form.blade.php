@php
    $hakAkses = $hakAkses ?? \App\Models\MagangAccessRight::where('user_id', auth()->id())->first();
@endphp

{{-- =========================================
ERROR VALIDATION
=========================================
Semua logic @foreach $errors->all() tidak berubah.
Hanya tampilan alert diupgrade ke style pill dengan ikon.
========================================= --}}
@if ($errors->any())
    <div
        class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-600
                text-sm px-4 py-3.5 rounded-xl">
        <i class="bi bi-exclamation-circle-fill text-red-400 mt-0.5 shrink-0 text-base"></i>
        <ul class="space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- =========================================
SECTION: INFORMASI POSISI
========================================= --}}
<div class="mb-8">

    {{-- Section header --}}
    <div class="flex items-center gap-2.5 mb-5">
        <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
            <i class="bi bi-briefcase text-blue-500 text-xs"></i>
        </div>
        <h4 class="text-sm font-extrabold text-gray-800 tracking-wide">Informasi Posisi</h4>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Judul Posisi --}}
        <div class="col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Judul Posisi <span class="text-red-400">*</span>
            </label>
            <input type="text" name="title" value="{{ old('title', $vacancy->title ?? '') }}" required
                placeholder="Contoh: Program Magang Bidang IT & Digitalisasi"
                class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       transition-all duration-200 outline-none
                       border-gray-200 bg-white
                       hover:border-blue-300 hover:bg-blue-50/20
                       focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                       placeholder:text-gray-300
                       {{ $errors->has('title') ? 'border-red-300 bg-red-50/20 focus:ring-red-100' : '' }}">
        </div>

        {{-- Divisi Penempatan --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Divisi Penempatan <span class="text-red-400">*</span>
            </label>

            @if ($hakAkses->role === 'superadmin')
                {{-- Superadmin: input bebas --}}
                <div class="relative">

                    <select name="division_name" required
                        class="w-full px-3.5 py-2.5 pr-11
               rounded-xl border
               text-sm font-medium text-gray-800
               transition-all duration-200
               outline-none appearance-none

               border-gray-200 bg-white
               hover:border-blue-300
               focus:border-blue-400
               focus:ring-2 focus:ring-blue-100">

                        <option value="">
                            Pilih Divisi
                        </option>

                        @foreach ($divisions as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('division_name', $vacancy->division_name ?? '') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach

                    </select>

                    {{-- Arrow --}}
                    <div
                        class="absolute inset-y-0 right-3
               flex items-center
               pointer-events-none">

                        <i class="bi bi-chevron-down text-gray-400 text-xs"></i>
                    </div>

                </div>
            @else
                <div class="relative">

                    <div
                        class="w-full px-3.5 py-2.5
               rounded-xl border
               border-gray-200
               bg-gray-50
               flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-8 h-8 rounded-lg
                       bg-blue-100
                       flex items-center justify-center">

                                <i class="bi bi-building text-blue-600 text-sm"></i>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $hakAkses->division_name }}
                                </p>

                                <p class="text-[11px] text-gray-400">
                                    Divisi otomatis sesuai akun admin
                                </p>
                            </div>

                        </div>

                        <div
                            class="w-8 h-8 rounded-lg
                   bg-white border border-gray-200
                   flex items-center justify-center">

                            <i class="bi bi-lock-fill text-gray-400 text-xs"></i>
                        </div>

                    </div>

                    <input type="hidden" name="division_name" value="{{ $hakAkses->division_name }}">

                </div>
            @endif
        </div>

        {{-- Tipe Program --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Tipe Program <span class="text-red-400">*</span>
            </label>
            {{--
                id="typeSelect" WAJIB dipertahankan.
                JavaScript menggunakan ID ini untuk menampilkan/menyembunyikan typeHint
                dan mengunci mode pendaftaran saat tipe = penelitian.
            --}}
            <select name="type" id="typeSelect" required
                class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       transition-all duration-200 outline-none appearance-none
                       border-gray-200 bg-white
                       hover:border-blue-300
                       focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                       bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')]
                       bg-position-[right_12px_center] bg-no-repeat bg-size-[16px]">
                <option value="magang" {{ old('type', $vacancy->type ?? '') === 'magang' ? 'selected' : '' }}>
                    Magang
                </option>
                <option value="penelitian" {{ old('type', $vacancy->type ?? '') === 'penelitian' ? 'selected' : '' }}>
                    Penelitian
                </option>
            </select>

            {{--
                id="typeHint" WAJIB dipertahankan.
                Muncul via JavaScript saat tipe = penelitian.
            --}}
            <p id="typeHint" class="hidden text-[11px] font-medium text-amber-600 mt-1.5 items-center gap-1">
                <i class="bi bi-exclamation-triangle-fill text-amber-500 text-[10px]"></i>
                Penelitian hanya untuk pendaftaran individu.
            </p>
        </div>

    </div>
</div>

{{-- =========================================
SECTION: TEKNIS PENDAFTARAN
========================================= --}}
<div class="mb-8">

    <div class="flex items-center gap-2.5 mb-5">
        <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
            <i class="bi bi-people text-indigo-500 text-xs"></i>
        </div>
        <h4 class="text-sm font-extrabold text-gray-800 tracking-wide">Teknis Pendaftaran</h4>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Mode Pendaftaran --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Mode Pendaftaran <span class="text-red-400">*</span>
            </label>
            {{--
                id="regMode" WAJIB dipertahankan.
                JavaScript menggunakan ID ini untuk show/hide minMemberDiv & maxMemberDiv
                dan mengupdate hidden input registration_mode.
                {{ $hasApplicant ?? false ? 'disabled' : '' }} TIDAK DIUBAH.
            --}}
            <select id="regMode" {{ $hasApplicant ?? false ? 'disabled' : '' }}
                class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       transition-all duration-200 outline-none appearance-none
                       border-gray-200 bg-white
                       hover:border-blue-300
                       focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                       disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed disabled:border-gray-100
                       bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')]
                       bg-position-[right_12px_center] bg-no-repeat bg-size-[16px]">
                @foreach (['individu', 'kelompok', 'hybrid'] as $mode)
                    <option value="{{ $mode }}"
                        {{ old('registration_mode', $vacancy->registration_mode ?? '') === $mode ? 'selected' : '' }}>
                        {{ ucfirst($mode) }}
                    </option>
                @endforeach
            </select>

            {{--
                Hidden input ini wajib ada karena select di atas bisa disabled.
                Field disabled tidak ikut terkirim saat form submit.
                JavaScript harus tetap sync value hidden ini saat select berubah.
            --}}
            <input type="hidden" name="registration_mode"
                value="{{ old('registration_mode', $vacancy->registration_mode ?? 'individu') }}">

            @if ($hasApplicant ?? false)
                <p class="text-[10.5px] text-gray-400 mt-1.5 flex items-center gap-1">
                    <i class="bi bi-lock-fill text-gray-300 text-[10px]"></i>
                    Terkunci — sudah ada pendaftar
                </p>
            @endif
        </div>

        {{-- Minimal Anggota --}}
        {{--
            id="minMemberDiv" WAJIB dipertahankan — dikontrol JavaScript (show/hide).
            id="minInput" WAJIB dipertahankan — dikontrol JavaScript.
        --}}
        <div id="minMemberDiv" class="hidden">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Minimal Anggota
            </label>
            <input type="number" name="min_members" id="minInput" min="1"
                value="{{ old('min_members', $vacancy->min_members ?? 1) }}"
                class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       transition-all duration-200 outline-none
                       border-gray-200 bg-white
                       hover:border-blue-300 hover:bg-blue-50/20
                       focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>

        {{-- Maksimal Anggota --}}
        {{--
            id="maxMemberDiv" WAJIB dipertahankan — dikontrol JavaScript (show/hide).
            id="maxInput" WAJIB dipertahankan — dikontrol JavaScript.
        --}}
        <div id="maxMemberDiv" class="hidden">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Maksimal Anggota
            </label>
            <input type="number" name="max_members" id="maxInput" min="1"
                value="{{ old('max_members', $vacancy->max_members ?? 1) }}"
                class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       transition-all duration-200 outline-none
                       border-gray-200 bg-white
                       hover:border-blue-300 hover:bg-blue-50/20
                       focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>

        {{-- Total Kuota --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Total Kuota <span class="text-red-400">*</span>
            </label>
            <input type="number" name="quota_slots" min="1"
                value="{{ old('quota_slots', $vacancy->quota_slots ?? 1) }}" required
                class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       transition-all duration-200 outline-none
                       border-gray-200 bg-white
                       hover:border-blue-300 hover:bg-blue-50/20
                       focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                       {{ $errors->has('quota_slots') ? 'border-red-300 focus:ring-red-100' : '' }}">
        </div>

    </div>
</div>

{{-- =========================================
SECTION: PERIODE MAGANG
========================================= --}}
<div class="mb-8">

    <div class="flex items-center gap-2.5 mb-4">
        <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
            <i class="bi bi-calendar-range text-emerald-500 text-xs"></i>
        </div>
        <h4 class="text-sm font-extrabold text-gray-800 tracking-wide">Periode Magang</h4>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>

    {{--
        id="dateWarning" WAJIB dipertahankan — dikontrol JavaScript (show/hide).
        id="dayCount" WAJIB dipertahankan — diisi nilai oleh JavaScript.
    --}}
    <div id="dateWarning"
        class="hidden items-start gap-3 bg-amber-50 border border-amber-200 text-amber-700
                text-sm px-4 py-3 rounded-xl mb-4">
        <i class="bi bi-exclamation-triangle-fill text-amber-500 shrink-0 mt-0.5"></i>
        <p class="text-sm text-amber-700">
            Perhatian: Durasi magang sangat singkat
            (<span id="dayCount" class="font-bold">0</span> hari).
            Pastikan tanggal sudah benar.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Tanggal Mulai --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Tanggal Mulai <span class="text-red-400">*</span>
            </label>
            {{-- id="startDate" WAJIB dipertahankan — dikontrol JavaScript. --}}
            <input type="date" name="start_date" id="startDate"
                value="{{ old('start_date', isset($vacancy->start_date) ? $vacancy->start_date->format('Y-m-d') : '') }}"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       transition-all duration-200 outline-none
                       border-gray-200 bg-white
                       hover:border-blue-300
                       focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                       {{ $errors->has('start_date') ? 'border-red-300 focus:ring-red-100' : '' }}">
        </div>

        {{-- Tanggal Selesai --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                Tanggal Selesai <span class="text-red-400">*</span>
            </label>
            {{-- id="endDate" WAJIB dipertahankan — dikontrol JavaScript. --}}
            <input type="date" name="end_date" id="endDate"
                value="{{ old('end_date', isset($vacancy->end_date) ? $vacancy->end_date->format('Y-m-d') : '') }}"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                       transition-all duration-200 outline-none
                       border-gray-200 bg-white
                       hover:border-blue-300
                       focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                       {{ $errors->has('end_date') ? 'border-red-300 focus:ring-red-100' : '' }}">
        </div>

    </div>
</div>

{{-- =========================================
SECTION: DESKRIPSI
========================================= --}}
<div class="mb-2">

    <div class="flex items-center gap-2.5 mb-4">
        <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
            <i class="bi bi-card-text text-gray-400 text-xs"></i>
        </div>
        <h4 class="text-sm font-extrabold text-gray-800 tracking-wide">Deskripsi & Syarat</h4>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>

    <textarea name="description" rows="4"
        placeholder="Jelaskan deskripsi program, syarat peserta, dan informasi tambahan lainnya..."
        class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
               transition-all duration-200 outline-none resize-none
               border-gray-200 bg-white
               hover:border-blue-300 hover:bg-blue-50/10
               focus:border-blue-400 focus:ring-2 focus:ring-blue-100
               placeholder:text-gray-300 placeholder:font-normal">{{ old('description', $vacancy->description ?? '') }}</textarea>

    <p class="text-[10.5px] text-gray-400 mt-1.5">
        <i class="bi bi-info-circle mr-0.5"></i>
        Opsional. Mendukung teks biasa.
    </p>

</div>
