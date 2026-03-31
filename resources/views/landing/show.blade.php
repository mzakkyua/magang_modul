<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vacancy->title }} - SINAKERTRANS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

    {{-- Flowbite untuk modal --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    {{-- ===================== NAVBAR ===================== --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-3.5 flex justify-between items-center">

            {{-- LOGIKA TOMBOL KEMBALI DINAMIS --}}
            @if (Auth::guard('web')->check())
                <a href="{{ route('landing.index') }}"
                    class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition font-semibold group">
                    <span
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 group-hover:bg-blue-50 transition">
                        <i class="bi bi-arrow-left text-sm"></i>
                    </span>
                    Kembali
                </a>
            @elseif(Auth::guard('magang')->check())
                <a href="{{ route('dashboard.index') }}"
                    class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition font-semibold group">
                    <span
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 group-hover:bg-blue-50 transition">
                        <i class="bi bi-arrow-left text-sm"></i>
                    </span>
                    Kembali ke Dashboard
                </a>
            @else
                <a href="{{ route('landing.index') }}"
                    class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition font-semibold group">
                    <span
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 group-hover:bg-blue-50 transition">
                        <i class="bi bi-arrow-left text-sm"></i>
                    </span>
                    Kembali ke Daftar
                </a>
            @endif

            <span class="font-extrabold text-blue-600 text-xl tracking-tight">SINAKERTRANS</span>

        </div>
    </nav>

    {{-- ===================== SWEETALERT NOTIFIKASI ===================== --}}
    <script type="module">
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{!! session('success') !!}",
                confirmButtonColor: '#2563EB',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{!! session('error') !!}",
                confirmButtonColor: '#DC2626',
            });
        @endif
    </script>

    {{-- ===================== HERO VACANCY ===================== --}}
    @php
        $isTypeMagang = $vacancy->type === 'magang';
        $heroBg = $isTypeMagang
            ? 'from-blue-900 via-blue-800 to-blue-700'
            : 'from-purple-900 via-purple-800 to-violet-700';
        $badgeBg = $isTypeMagang
            ? 'bg-blue-500/20 text-blue-200 border-blue-400/30'
            : 'bg-purple-500/20 text-purple-200 border-purple-400/30';
    @endphp

    <section class="bg-linear-to-br {{ $heroBg }} py-14 md:py-16">
        <div class="max-w-5xl mx-auto px-6">

            {{-- Badge type --}}
            <span
                class="inline-flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-full border {{ $badgeBg }} mb-5 uppercase tracking-widest">
                <i class="bi bi-{{ $isTypeMagang ? 'briefcase' : 'journal-text' }}"></i>
                {{ $vacancy->type }}
            </span>

            <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-4">
                {{ $vacancy->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-4 text-sm text-blue-100">
                <span class="flex items-center gap-1.5">
                    <i class="bi bi-building"></i>
                    {{ $vacancy->division_name }}
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="bi bi-calendar3"></i>
                    {{ \Carbon\Carbon::parse($vacancy->start_date)->format('d M') }}
                    —
                    {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d M Y') }}
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="bi bi-people"></i>
                    {{ $vacancy->getSisaKuota() }} slot tersisa
                </span>
            </div>

        </div>
    </section>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM UTAMA: Deskripsi --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Card: Deskripsi --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-file-text text-blue-600 text-sm"></i>
                        </span>
                        Deskripsi & Persyaratan
                    </h2>
                    <div class="text-gray-600 leading-relaxed text-sm md:text-base prose max-w-none">
                        @if ($vacancy->description)
                            {!! nl2br(e($vacancy->description)) !!}
                        @else
                            <p class="text-gray-400 italic">Belum ada deskripsi untuk lowongan ini.</p>
                        @endif
                    </div>
                </div>

                {{-- Card: Admin Notice --}}
                @if (Auth::guard('web')->check())
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center shrink-0">
                            <i class="bi bi-shield-exclamation text-amber-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-800 text-sm mb-0.5">Mode Pratinjau Admin</p>
                            <p class="text-amber-700 text-xs">Anda sedang login sebagai Admin. Tombol lamaran tidak
                                tersedia untuk role ini.</p>
                        </div>
                    </div>
                @endif

                {{-- Card: CTA Guest --}}
                @if (!Auth::guard('web')->check() && !Auth::guard('magang')->check())
                    <div
                        class="bg-linear-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-person-lock text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-1">Tertarik dengan posisi ini?</h3>
                        <p class="text-gray-500 text-sm mb-5">Silakan login atau daftar akun terlebih dahulu untuk
                            melamar.</p>
                        <div class="flex justify-center gap-3 flex-wrap">
                            <a href="{{ route('login') }}"
                                class="px-5 py-2.5 border border-blue-600 text-blue-600 font-semibold text-sm rounded-xl hover:bg-blue-50 transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-5 py-2.5 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 transition shadow-md shadow-blue-600/30">
                                Daftar Sekarang
                            </a>
                        </div>
                    </div>
                @endif

            </div>

            {{-- KOLOM SIDEBAR: Info & Tombol Lamar --}}
            <div class="space-y-5">

                {{-- Card: Info Lowongan --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-info-circle text-gray-600 text-xs"></i>
                        </span>
                        Informasi Lowongan
                    </h3>

                    <div class="space-y-4">

                        {{-- Batas Lamaran --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center shrink-0">
                                <i class="bi bi-clock text-red-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wide mb-0.5">Batas
                                    Lamaran</p>
                                <p class="text-sm font-bold text-red-600">
                                    {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d F Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Periode Magang --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                                <i class="bi bi-calendar-range text-blue-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wide mb-0.5">Periode
                                    Magang</p>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($vacancy->start_date)->format('d M') }}
                                    —
                                    {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Kuota dengan progress --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center shrink-0">
                                <i class="bi bi-people text-green-500 text-xs"></i>
                            </div>
                            <div class="w-full">
                                <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wide mb-0.5">Sisa
                                    Kuota</p>
                                <p class="text-sm font-bold text-blue-600 mb-1.5">
                                    {{ $vacancy->getSisaKuota() }}
                                    <span class="text-gray-400 font-normal">/ {{ $vacancy->quota_slots }} slot</span>
                                </p>
                                @php
                                    $filled = $vacancy->quota_slots - $vacancy->getSisaKuota();
                                    $percentage =
                                        $vacancy->quota_slots > 0 ? ($filled / $vacancy->quota_slots) * 100 : 0;
                                    $barColor =
                                        $percentage >= 80
                                            ? 'bg-red-400'
                                            : ($percentage >= 50
                                                ? 'bg-yellow-400'
                                                : 'bg-blue-500');
                                @endphp
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $barColor }}"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Mode Pendaftaran --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center shrink-0">
                                <i class="bi bi-person-badge text-purple-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wide mb-0.5">Mode
                                    Pendaftaran</p>
                                <p class="text-sm font-semibold text-gray-800 capitalize">
                                    {{ $vacancy->registration_mode }}</p>
                            </div>
                        </div>

                        {{-- Jumlah Anggota --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center shrink-0">
                                <i class="bi bi-person-plus text-indigo-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wide mb-0.5">Jumlah
                                    Anggota</p>
                                <p class="text-sm font-semibold text-gray-800">
                                    @if ($vacancy->registration_mode === 'individu')
                                        1 Orang
                                    @else
                                        {{ $vacancy->min_members }} – {{ $vacancy->max_members }} Orang
                                    @endif
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Tombol Lamar (Hanya untuk peserta) --}}
                @if (Auth::guard('magang')->check())
                    @if ($vacancy->getSisaKuota() > 0)
                        <button type="button" data-modal-target="applicationModal"
                            data-modal-toggle="applicationModal"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg shadow-blue-600/30 transition-all duration-200 hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm">
                            <i class="bi bi-send-fill"></i>
                            Lamar Posisi Ini
                        </button>
                    @else
                        <div
                            class="w-full bg-red-50 border border-red-200 text-red-500 font-bold py-3.5 px-6 rounded-2xl text-center text-sm flex items-center justify-center gap-2">
                            <i class="bi bi-slash-circle"></i> Kuota Sudah Penuh
                        </div>
                    @endif
                @endif

            </div>

        </div>
    </div>

    {{-- ===================== MODAL LAMARAN ===================== --}}
    {{-- Logic Blade tidak berubah, hanya visual modal yang diperbaiki --}}
    @if (Auth::guard('magang')->check())
        <div id="applicationModal" tabindex="-1" aria-hidden="true"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/60 backdrop-blur-sm">
            <div class="relative w-full max-w-lg max-h-full mx-auto mt-10">
                <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden">

                    {{-- Modal Header --}}
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-linear-to-r from-blue-600 to-blue-500">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="bi bi-send text-white text-sm"></i>
                            </div>
                            <h3 class="text-base font-bold text-white">Konfirmasi Lamaran</h3>
                        </div>
                        <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-red-500 text-white transition"
                            data-modal-hide="applicationModal">
                            <i class="bi bi-x-lg text-sm"></i>
                            <span class="sr-only">Tutup</span>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6">

                        {{-- Konfirmasi info --}}
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 flex items-start gap-3">
                            <i class="bi bi-info-circle-fill text-blue-500 mt-0.5 shrink-0"></i>
                            <p class="text-sm text-blue-800 leading-relaxed">
                                Anda akan melamar untuk posisi
                                <strong>{{ $vacancy->title }}</strong>.
                                Pastikan profil dan CV Anda sudah diperbarui sebelum mengirim lamaran.
                            </p>
                        </div>

                        <form action="{{ route('applications.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="vacancy_id" value="{{ $vacancy->id }}">

                            {{-- Penelitian: Judul & Abstrak --}}
                            @if ($vacancy->type === 'penelitian')
                                <div>
                                    <label for="research_title"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">
                                        Judul Penelitian <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="research_title" name="research_title" maxlength="255"
                                        required
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="Masukkan rencana judul penelitian Anda">
                                    <p id="title_counter" class="text-xs text-gray-400 text-right mt-1">0 / 255
                                        karakter</p>
                                </div>

                                <div>
                                    <label for="research_abstract"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">
                                        Abstrak Singkat <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="research_abstract" name="research_abstract" rows="4" maxlength="1000" required
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-none"
                                        placeholder="Jelaskan latar belakang dan tujuan penelitian Anda..."></textarea>
                                    <p id="abstract_counter" class="text-xs text-gray-400 text-right mt-1">0 / 1000
                                        karakter</p>
                                </div>
                            @endif

                            {{-- Kelompok / Hybrid --}}
                            @if (in_array($vacancy->registration_mode, ['kelompok', 'hybrid']))
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">

                                    @if ($vacancy->registration_mode === 'hybrid')
                                        <div class="mb-4">
                                            <label class="block mb-1.5 text-sm font-semibold text-gray-700">Daftar
                                                Sebagai:</label>
                                            <select id="hybrid-mode-select"
                                                class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                                <option value="individu">Individu (Sendiri)</option>
                                                <option value="kelompok">Kelompok (Tim)</option>
                                            </select>
                                        </div>
                                    @endif

                                    <div id="group-input-area"
                                        class="{{ $vacancy->registration_mode === 'hybrid' ? 'hidden' : '' }}">
                                        <label class="block mb-1 text-sm font-semibold text-gray-700">
                                            Anggota Kelompok (Email)
                                        </label>
                                        <p class="text-xs text-gray-500 mb-3">
                                            Min {{ $vacancy->min_members }} — Maks {{ $vacancy->max_members }} orang
                                            (termasuk Anda sebagai Ketua).
                                        </p>

                                        <div id="members-container" class="space-y-2">
                                            <div class="flex gap-2 member-input">
                                                <input type="email" name="member_emails[]"
                                                    {{ $vacancy->registration_mode === 'kelompok' ? 'required' : '' }}
                                                    {{ $vacancy->registration_mode === 'hybrid' ? 'disabled' : '' }}
                                                    class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                                    placeholder="email.anggota1@contoh.com">
                                            </div>
                                        </div>

                                        <button type="button" id="add-member-btn"
                                            class="mt-3 text-xs text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1.5 transition">
                                            <i class="bi bi-plus-circle-fill"></i> Tambah Anggota Lainnya
                                        </button>
                                        <p class="text-[10px] text-gray-400 mt-2">
                                            * Pastikan email anggota sudah terdaftar di sistem.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- Catatan --}}
                            <div>
                                <label for="notes" class="block mb-1.5 text-sm font-semibold text-gray-700">
                                    Catatan Singkat <span class="text-gray-400 font-normal">(Opsional)</span>
                                </label>
                                <textarea id="notes" name="notes" rows="2"
                                    class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-none"
                                    placeholder="Kenapa Anda tertarik dengan posisi ini?"></textarea>
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-all flex items-center justify-center gap-2 shadow-md shadow-blue-600/30">
                                <i class="bi bi-send-fill"></i> Kirim Lamaran
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ===================== SCRIPTS ===================== --}}
    <script type="module">
        // SECTION: Character counter untuk form penelitian
        document.addEventListener('DOMContentLoaded', function() {

            // STEP: Counter judul penelitian
            const titleInput = document.getElementById('research_title');
            const titleCounter = document.getElementById('title_counter');
            if (titleInput && titleCounter) {
                titleInput.addEventListener('input', function() {
                    titleCounter.textContent = this.value.length + ' / 255 karakter';
                });
            }

            // STEP: Counter abstrak penelitian
            const abstractInput = document.getElementById('research_abstract');
            const abstractCounter = document.getElementById('abstract_counter');
            if (abstractInput && abstractCounter) {
                abstractInput.addEventListener('input', function() {
                    abstractCounter.textContent = this.value.length + ' / 1000 karakter';
                });
            }
        });

        // SECTION: Tambah anggota kelompok
        const container = document.getElementById('members-container');
        const addBtn = document.getElementById('add-member-btn');
        const maxMembers = {{ $vacancy->max_members ?? 1 }} - 1;

        if (container && addBtn) {
            addBtn.addEventListener('click', function() {
                const currentInputs = container.querySelectorAll('.member-input').length;
                if (currentInputs >= maxMembers) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Batas Maksimal',
                        text: `Lowongan ini maksimal ${maxMembers + 1} orang (termasuk Anda).`,
                        confirmButtonColor: '#2563EB',
                    });
                    return;
                }
                const div = document.createElement('div');
                div.className = 'flex gap-2 member-input';
                div.innerHTML = `
                    <input type="email" name="member_emails[]" required
                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="email.anggota@contoh.com">
                    <button type="button" onclick="this.parentElement.remove()"
                        class="px-3 py-2.5 bg-red-50 text-red-500 rounded-xl border border-red-100 hover:bg-red-100 transition flex-shrink-0"
                        title="Hapus anggota ini">
                        <i class="bi bi-trash text-sm"></i>
                    </button>
                `;
                container.appendChild(div);
            });
        }

        // SECTION: Hybrid mode toggle (individu/kelompok)
        const hybridSelect = document.getElementById('hybrid-mode-select');
        const groupArea = document.getElementById('group-input-area');
        const membersContainerEl = document.getElementById('members-container');

        if (hybridSelect && groupArea && membersContainerEl) {
            hybridSelect.addEventListener('change', function() {
                const isKelompok = this.value === 'kelompok';
                const inputs = membersContainerEl.querySelectorAll('input[name="member_emails[]"]');
                if (isKelompok) {
                    groupArea.classList.remove('hidden');
                    inputs.forEach(input => {
                        input.disabled = false;
                        input.required = true;
                    });
                } else {
                    groupArea.classList.add('hidden');
                    inputs.forEach(input => {
                        input.disabled = true;
                        input.required = false;
                    });
                }
            });
        }
    </script>

</body>

</html>
