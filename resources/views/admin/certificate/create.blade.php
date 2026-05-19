@extends('layouts.admin')

@section('title', 'Upload Sertifikat')

@section('content')
    @php
        $memberJsonData = $members->mapWithKeys(function ($m) {
            $v = $m->application->vacancy;
            return [
                $m->id => [
                    'id' => $m->id,
                    'name' => $m->user->profile->full_name ?? $m->user->username,
                    'email' => $m->user->email,
                    'divisi' => $v->division_name ?? '-',
                    'title' => $v->title ?? '-',
                    'start' => \Carbon\Carbon::parse($v->start_date)->format('d M Y'),
                    'end' => \Carbon\Carbon::parse($v->end_date)->format('d M Y'),
                    'score' => $m->assessment?->final_score,
                    'hasCert' => $m->certificate !== null,
                    'certTitle' => $m->certificate?->title,
                    'appStatus' => $m->application->status,
                ],
            ];
        });
    @endphp

    <div class="max-w-2xl mx-auto">

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <i class="bi bi-patch-check text-blue-500 text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-gray-900">Upload Sertifikat</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Sertifikat diterbitkan per periode magang — user yang magang 2x akan punya 2 sertifikat terpisah.
                    </p>
                </div>
            </div>

            @if (session('success'))
                <div
                    class="mx-6 mt-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl">
                    <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5 shrink-0"></i>
                    <div><span class="font-bold block">Berhasil!</span>{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="mx-6 mt-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                    <i class="bi bi-exclamation-triangle-fill text-red-500 mt-0.5 shrink-0"></i>
                    <div><span class="font-bold block">Gagal!</span>{{ session('error') }}</div>
                </div>
            @endif

            <form action="{{ route('admin.certificates.store') }}" method="POST" enctype="multipart/form-data"
                id="certForm">
                @csrf

                <div class="px-6 py-6 space-y-6">

                    {{-- ── 1. PILIH PERIODE MAGANG ── --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                            Peserta & Periode Magang <span class="text-red-400">*</span>
                        </label>

                        <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-200 rounded-xl px-3.5 py-3 mb-3">
                            <i class="bi bi-info-circle-fill text-blue-400 text-sm shrink-0 mt-0.5"></i>
                            <p class="text-xs text-blue-700 leading-relaxed">
                                Pilih <span class="font-bold">periode magang spesifik</span> dari peserta.
                                Hanya peserta dengan status <span class="font-bold text-blue-700">Selesai (Completed)</span>
                                yang dapat menerima sertifikat.
                                Sertifikat yang sudah diterbitkan ditandai
                                <span class="font-bold text-emerald-700">✓ sudah diterbitkan</span>.
                            </p>
                        </div>

                        <select name="application_member_id" id="memberSelect" required
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                                   transition-all outline-none appearance-none border-gray-200 bg-white
                                   hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                                   {{ $errors->has('application_member_id') ? 'border-red-300' : '' }}">

                            <option value="">— Pilih Peserta & Periode —</option>

                            @foreach ($members as $member)
                                @php
                                    $vacancy = $member->application->vacancy;
                                    $name = $member->user->profile->full_name ?? $member->user->username;
                                    $email = $member->user->email;
                                    $divisi = $vacancy->division_name ?? '-';
                                    $periode =
                                        \Carbon\Carbon::parse($vacancy->start_date)->format('M Y') .
                                        ' – ' .
                                        \Carbon\Carbon::parse($vacancy->end_date)->format('M Y');
                                    $hasCert = $member->certificate !== null;
                                    $label =
                                        "{$name} ({$email}) — {$divisi} | {$periode}" .
                                        ($hasCert ? ' ✓ sudah diterbitkan' : '');
                                @endphp
                                <option value="{{ $member->id }}"
                                    {{ old('application_member_id') == $member->id ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('application_member_id')
                            <p class="flex items-center gap-1.5 text-red-500 text-xs font-medium mt-1.5">
                                <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                            </p>
                        @enderror

                        <div id="memberPreview"
                            class="hidden mt-3 bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm space-y-2">
                        </div>
                    </div>

                    {{-- ── 2. JUDUL DOKUMEN ── --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                            Judul Dokumen <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            placeholder="Contoh: Sertifikat Penyelesaian Magang"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-medium text-gray-800
                                   transition-all outline-none border-gray-200 bg-white
                                   placeholder:text-gray-300 placeholder:font-normal
                                   hover:border-blue-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                                   {{ $errors->has('title') ? 'border-red-300' : '' }}">
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
                        <label for="fileInput"
                            class="group flex flex-col items-center justify-center gap-2 w-full px-4 py-8
                                   rounded-xl cursor-pointer border-2 border-dashed transition-all
                                   {{ $errors->has('file') ? 'border-red-200 bg-red-50/30' : 'border-gray-200 bg-gray-50/50 hover:border-blue-300 hover:bg-blue-50/30' }}">
                            <div
                                class="w-11 h-11 rounded-xl bg-white border border-gray-200 flex items-center justify-center shadow-sm group-hover:border-blue-200 transition-all">
                                <i
                                    class="bi bi-cloud-arrow-up text-gray-400 group-hover:text-blue-500 text-xl transition-colors"></i>
                            </div>
                            <div class="text-center" id="dropText">
                                <p class="text-sm font-semibold text-gray-600 group-hover:text-blue-600 transition-colors">
                                    Klik untuk pilih file
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">PDF, JPG, PNG — maks 5MB</p>
                            </div>
                            <div id="filePreview"
                                class="hidden items-center gap-2 bg-white border border-blue-200 rounded-lg px-3 py-2 text-sm font-semibold text-blue-600">
                                <i class="bi bi-file-earmark-check-fill text-blue-500"></i>
                                <span id="fileName"></span>
                            </div>
                            <input type="file" name="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png" required
                                class="hidden">
                        </label>
                        @error('file')
                            <p class="flex items-center gap-1.5 text-red-500 text-xs font-medium mt-1">
                                <i class="bi bi-exclamation-circle-fill text-[11px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between gap-3">
                    <a href="{{ route('admin.vacancies.index') }}"
                        class="text-sm text-gray-400 hover:text-gray-600 font-medium transition flex items-center gap-1.5">
                        <i class="bi bi-arrow-left text-xs"></i> Kembali
                    </a>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
                               text-white text-sm font-bold px-5 py-2.5 rounded-xl
                               shadow-md shadow-blue-600/25 transition-all hover:-translate-y-0.5">
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
        var memberData = {{ Js::from($memberJsonData) }};
        var memberSelect = document.getElementById('memberSelect');
        var memberPreview = document.getElementById('memberPreview');

        if (memberSelect) {
            memberSelect.addEventListener('change', function() {
                var id = this.value;
                var data = memberData[id];

                if (!data) {
                    memberPreview.classList.add('hidden');
                    return;
                }

                var scoreHtml = (data.score !== null && data.score !== undefined) ?
                    '<span class="font-bold ' + (data.score >= 70 ? 'text-emerald-600' : 'text-red-500') + '">' +
                    data.score + ' / 100</span>' :
                    '<span class="text-gray-400 italic">Belum dinilai</span>';

                var certHtml = data.hasCert ?
                    '<span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full"><i class="bi bi-arrow-repeat text-[10px]"></i> Replace: ' +
                    data.certTitle + '</span>' :
                    '<span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full"><i class="bi bi-plus-circle text-[10px]"></i> Penerbitan baru</span>';

                var statusBadge = data.appStatus === 'completed' ?
                    '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full"><i class="bi bi-check-circle-fill text-[10px]"></i> Selesai</span>' :
                    '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full"><i class="bi bi-exclamation-circle-fill text-[10px]"></i> Belum Selesai — tidak bisa diterbitkan</span>';

                memberPreview.innerHTML =
                    '<div class="flex items-start justify-between gap-4 flex-wrap">' +
                    '<div><p class="font-bold text-gray-800">' + data.name + '</p>' +
                    '<p class="text-xs text-gray-500">' + data.email + '</p>' +
                    '<div class="mt-1">' + statusBadge + '</div></div>' +
                    certHtml +
                    '</div>' +
                    '<div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600 mt-2 pt-2 border-t border-gray-200">' +
                    '<div><span class="text-gray-400">Lowongan</span><br><span class="font-semibold">' + data
                    .title + '</span></div>' +
                    '<div><span class="text-gray-400">Divisi</span><br><span class="font-semibold">' + data.divisi +
                    '</span></div>' +
                    '<div><span class="text-gray-400">Periode</span><br><span class="font-semibold">' + data.start +
                    ' – ' + data.end + '</span></div>' +
                    '<div><span class="text-gray-400">Nilai Akhir</span><br>' + scoreHtml + '</div>' +
                    '</div>';

                memberPreview.classList.remove('hidden');
            });

            // trigger preview jika ada old() value
            if (memberSelect.value) {
                memberSelect.dispatchEvent(new Event('change'));
            }
        }

        var fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    var n = this.files[0].name;
                    document.getElementById('fileName').textContent =
                        n.length > 36 ? n.substring(0, 33) + '...' : n;
                    document.getElementById('dropText').classList.add('hidden');
                    var fp = document.getElementById('filePreview');
                    fp.classList.remove('hidden');
                    fp.classList.add('flex');
                }
            });
        }

        var certForm = document.getElementById('certForm');
        if (certForm) {
            certForm.addEventListener('submit', function() {
                var btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
                document.getElementById('submitText').textContent = 'Mengupload...';
            });
        }
    </script>
@endpush
