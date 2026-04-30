@extends('layouts.admin')

@section('title', 'Upload Sertifikat')

@push('header_actions')
@endpush

@section('content')
    <div class="max-w-2xl mx-auto">

        {{-- ========================= CARD FORM ========================= --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <i class="bi bi-patch-check text-blue-500 text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-gray-900">Upload Sertifikat</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Berikan sertifikat kepada peserta magang yang telah selesai.</p>
                </div>
            </div>

            {{-- Flash success --}}
            @if (session('success'))
                <div
                    class="mx-6 mt-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200
                            text-emerald-700 text-sm px-4 py-3 rounded-xl">
                    <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                    <div>
                        <span class="font-bold block">Berhasil!</span>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- FORM --}}
            {{-- enctype="multipart/form-data" WAJIB ada — tidak diubah --}}
            <form action="{{ route('admin.certificates.store') }}" method="POST" enctype="multipart/form-data"
                id="certForm">
                @csrf

                <div class="px-6 py-6 space-y-6">

                    {{-- ── 1. PILIH PESERTA ── --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                            Peserta Magang <span class="text-red-400">*</span>
                        </label>

                        {{-- Info: hanya peserta yang sudah dinilai yang muncul --}}
                        <div
                            class="flex items-start gap-2.5 bg-blue-50 border border-blue-200
                                    rounded-xl px-3.5 py-3 mb-3">
                            <i class="bi bi-info-circle-fill text-blue-400 text-sm shrink-0 mt-0.5"></i>
                            <p class="text-xs text-blue-700 leading-relaxed">
                                Daftar ini hanya menampilkan peserta yang <span class="font-bold">sudah dinilai</span>
                                oleh admin. Peserta yang belum menerima penilaian tidak akan muncul sebagai pilihan.
                            </p>
                        </div>
                        <select name="user_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                                   transition-all duration-200 outline-none appearance-none
                                   border-gray-200 bg-white
                                   hover:border-blue-300
                                   focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                                   {{ $errors->has('user_id') ? 'border-red-300 focus:ring-red-100' : '' }}
                                   bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')]
                                   bg-[right_12px_center] bg-no-repeat bg-[length:16px]">
                            <option value="">— Pilih Peserta —</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->profile->full_name ?? $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="flex items-center gap-1.5 text-red-500 text-xs font-medium mt-1.5">
                                <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ── 2. JUDUL DOKUMEN ── --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                            Judul Dokumen <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            placeholder="Contoh: Sertifikat Penyelesaian Magang"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                                   transition-all duration-200 outline-none
                                   border-gray-200 bg-white placeholder:text-gray-300 placeholder:font-normal
                                   hover:border-blue-300 hover:bg-blue-50/20
                                   focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                                   {{ $errors->has('title') ? 'border-red-300 focus:ring-red-100' : '' }}">
                        <p class="text-[10.5px] text-gray-400 mt-1.5">
                            <i class="bi bi-info-circle mr-0.5"></i>
                            Judul ini yang akan dilihat peserta di dashboard mereka.
                        </p>
                        @error('title')
                            <p class="flex items-center gap-1.5 text-red-500 text-xs font-medium mt-1">
                                <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ── 3. UPLOAD FILE ── --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                            File Sertifikat <span class="text-red-400">*</span>
                        </label>

                        {{--
                            Drop zone — klik untuk browse atau drag & drop.
                            JavaScript di bawah handle preview nama file saat dipilih.
                            name="file" dan accept TIDAK DIUBAH.
                        --}}
                        <label for="fileInput"
                            class="group flex flex-col items-center justify-center gap-2
                                      w-full px-4 py-8 rounded-xl cursor-pointer
                                      border-2 border-dashed transition-all duration-200
                                      {{ $errors->has('file') ? 'border-red-200 bg-red-50/30' : 'border-gray-200 bg-gray-50/50 hover:border-blue-300 hover:bg-blue-50/30' }}">

                            {{-- Ikon upload --}}
                            <div
                                class="w-11 h-11 rounded-xl bg-white border border-gray-200 flex items-center justify-center
                                        shadow-sm group-hover:border-blue-200 group-hover:shadow-blue-100/50 transition-all duration-200">
                                <i
                                    class="bi bi-cloud-arrow-up text-gray-400 group-hover:text-blue-500 text-xl transition-colors duration-200"></i>
                            </div>

                            {{-- Teks default --}}
                            <div class="text-center" id="dropText">
                                <p class="text-sm font-semibold text-gray-600 group-hover:text-blue-600 transition-colors">
                                    Klik untuk pilih file
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">atau drag & drop ke sini</p>
                            </div>

                            {{-- Preview nama file (hidden by default) --}}
                            <div id="filePreview"
                                class="hidden items-center gap-2 bg-white border border-blue-200
                                                          rounded-lg px-3 py-2 text-sm font-semibold text-blue-600">
                                <i class="bi bi-file-earmark-check-fill text-blue-500"></i>
                                <span id="fileName"></span>
                            </div>

                            {{-- Input file tersembunyi --}}
                            <input type="file" name="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png" required
                                class="hidden">
                        </label>

                        <p class="text-[10.5px] text-gray-400 mt-1.5">
                            <i class="bi bi-paperclip mr-0.5"></i>
                            Format: PDF, JPG, PNG — Maksimal 2MB
                        </p>
                        @error('file')
                            <p class="flex items-center gap-1.5 text-red-500 text-xs font-medium mt-1">
                                <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                {{-- ── FOOTER TOMBOL ── --}}
                <div
                    class="px-6 py-4 bg-gray-50/50 border-t border-gray-100
                            flex items-center justify-between gap-3">
                    <a href="{{ route('admin.vacancies.index') }}"
                        class="text-sm text-gray-400 hover:text-gray-600 font-medium transition-colors flex items-center gap-1.5">
                        <i class="bi bi-arrow-left text-xs"></i> Kembali
                    </a>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
                               text-white text-sm font-bold px-5 py-2.5 rounded-xl
                               shadow-md shadow-blue-600/25 hover:shadow-blue-600/40
                               transition-all duration-200 hover:-translate-y-0.5">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                        <span id="submitText">Upload Sekarang</span>
                    </button>
                </div>

            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // SECTION: File drop zone — preview nama file saat dipilih
        const fileInput = document.getElementById('fileInput');
        const dropText = document.getElementById('dropText');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');

        fileInput?.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const name = this.files[0].name;
                fileName.textContent = name.length > 36 ? name.substring(0, 33) + '...' : name;
                dropText.classList.add('hidden');
                filePreview.classList.remove('hidden');
                filePreview.classList.add('flex');
            }
        });

        // SECTION: Loading state saat submit
        document.getElementById('certForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            const text = document.getElementById('submitText');
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            text.textContent = 'Mengupload...';
        });
    </script>
@endpush
