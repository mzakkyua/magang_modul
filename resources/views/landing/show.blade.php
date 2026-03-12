<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vacancy->title }} - SINAKERTRANS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
</head>

<body class="bg-gray-50 font-sans text-gray-800">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            {{-- LOGIKA TOMBOL KEMBALI DINAMIS --}}
            @if (Auth::guard('web')->check())
                {{-- Admin --}}
                <a href="{{ route('landing.index') }}"
                    class="flex items-center text-gray-600 hover:text-blue-600 transition">
                    <i class="bi bi-arrow-left mr-2"></i> Kembali
                </a>
            @elseif(Auth::guard('magang')->check())
                {{-- Peserta Magang yang sudah login --}}
                <a href="{{ route('dashboard.index') }}"
                    class="flex items-center text-gray-600 hover:text-blue-600 transition">
                    <i class="bi bi-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
            @else
                {{-- Tamu / Guest --}}
                <a href="{{ route('landing.index') }}"
                    class="flex items-center text-gray-600 hover:text-blue-600 transition">
                    <i class="bi bi-arrow-left mr-2"></i> Kembali ke Daftar
                </a>
            @endif
            <span class="font-bold text-blue-600 text-xl">SINAKERTRANS</span>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-10">

        {{-- SCRIPT SWEETALERT2 UNTUK POP-UP NOTIFIKASI --}}
        <script type="module">
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{!! session('success') !!}",
                    confirmButtonColor: '#2563EB', // Warna biru dari Tailwind
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{!! session('error') !!}",
                    confirmButtonColor: '#DC2626', // Warna merah dari Tailwind
                });
            @endif
        </script>

        {{-- KOTAK UTAMA (CARD) LOWONGAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 p-6 md:p-10">

            {{-- 1. HEADER --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $vacancy->title }}</h1>
                    <div class="flex gap-2 text-sm font-medium">
                        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-md">
                            Divisi: {{ $vacancy->division_name }}
                        </span>
                        <span class="bg-green-50 text-green-700 px-3 py-1 rounded-md uppercase tracking-wider">
                            {{ $vacancy->type }}
                        </span>
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-xs text-gray-500 mb-1">Batas Lamaran</p>
                    <p class="text-lg font-bold text-red-600">
                        {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d F Y') }}
                    </p>
                </div>
            </div>

            <hr class="border-gray-100 mb-6">

            {{-- 2. INFO SINGKAT (KUOTA & ANGGOTA) --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Periode Magang</p>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ \Carbon\Carbon::parse($vacancy->start_date)->format('d M') }} -
                        {{ \Carbon\Carbon::parse($vacancy->end_date)->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Sisa Kuota</p>
                    <p class="font-semibold text-blue-600 text-sm">
                        {{ $vacancy->getSisaKuota() }} <span class="text-gray-500">/ {{ $vacancy->quota_slots }}
                            Slot</span>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Pendaftaran</p>
                    <p class="font-semibold text-gray-800 text-sm capitalize">{{ $vacancy->registration_mode }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumlah Anggota</p>
                    <p class="font-semibold text-gray-800 text-sm">
                        @if ($vacancy->registration_mode === 'individu')
                            1 Orang
                        @else
                            {{ $vacancy->min_members }} - {{ $vacancy->max_members }} Orang
                        @endif
                    </p>
                </div>
            </div>

            {{-- 3. DESKRIPSI DAN KRITERIA (MENGAMBIL DARI DATABASE) --}}
            <div class="mb-10">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Deskripsi Pekerjaan & Persyaratan</h3>
                <div class="text-gray-700 leading-relaxed text-sm md:text-base">
                    @if ($vacancy->description)
                        {{-- Memanggil data asli dari database dengan aman --}}
                        {!! nl2br(e($vacancy->description)) !!}
                    @else
                        <p class="text-gray-400 italic">Belum ada deskripsi.</p>
                    @endif
                </div>
            </div>

            {{-- 4. TOMBOL LAMAR DENGAN LOGIKA AUTH ASLI --}}
            <div class="mt-8 border-t border-gray-100 pt-8">

                {{-- KONDISI 1: JIKA PENGUNJUNG ADALAH ADMIN --}}
                @if (Auth::guard('web')->check())
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-sm text-yellow-700">
                            Halo Admin! Anda sedang dalam mode pratinjau. Admin tidak bisa melamar.
                        </p>
                    </div>

                    {{-- KONDISI 2: JIKA SUDAH LOGIN SEBAGAI MAHASISWA --}}
                @elseif(Auth::guard('magang')->check())
                    <button type="button" data-modal-target="applicationModal" data-modal-toggle="applicationModal"
                        class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-1">
                        🚀 Lamar Posisi Ini Sekarang
                    </button>

                    {{-- KONDISI 3: JIKA BELUM LOGIN (TAMU) --}}
                @else
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 text-center">
                        <p class="text-gray-600 mb-3">Tertarik dengan posisi ini? Silakan login atau daftar akun
                            terlebih dahulu.</p>

                        <div class="flex justify-center gap-4">
                            <a href="{{ route('login') }}"
                                class="bg-white text-blue-600 border border-blue-600 font-bold py-2 px-6 rounded-lg hover:bg-blue-50 transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition shadow">
                                Daftar Akun
                            </a>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>

    {{-- MODAL HANYA UNTUK MAGANG --}}
    @if (Auth::guard('magang')->check())
        <div id="applicationModal" tabindex="-1" aria-hidden="true"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-start justify-between p-4 border-b rounded-t">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Konfirmasi Lamaran
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-red-600 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                            data-modal-hide="applicationModal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <div class="p-6 space-y-6">
                        <p class="text-base leading-relaxed text-gray-500">
                            Apakah Anda yakin ingin melamar untuk posisi <strong>{{ $vacancy->title }}</strong>?
                            Pastikan profil dan CV Anda sudah diperbarui.
                        </p>

                        <form action="{{ route('applications.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="vacancy_id" value="{{ $vacancy->id }}">

                            {{-- KONDISI KHUSUS UNTUK JALUR PENELITIAN --}}
                            @if ($vacancy->type === 'penelitian')
                                <div class="mb-4">
                                    <label for="research_title"
                                        class="block mb-2 text-sm font-medium text-gray-900">Judul Penelitian <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="research_title" name="research_title" maxlength="255"
                                        required
                                        class="block w-full p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Masukkan rencana judul penelitian Anda">
                                    {{-- Teks Penghitung Karakter Judul --}}
                                    <p id="title_counter" class="text-xs text-gray-500 text-right mt-1 font-medium">0 /
                                        255 karakter</p>
                                </div>

                                <div class="mb-4">
                                    <label for="research_abstract"
                                        class="block mb-2 text-sm font-medium text-gray-900">Abstrak Singkat <span
                                            class="text-red-500">*</span></label>
                                    <textarea id="research_abstract" name="research_abstract" rows="4" maxlength="1000" required
                                        class="block w-full p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Jelaskan secara singkat latar belakang dan tujuan penelitian Anda..."></textarea>
                                    {{-- Teks Penghitung Karakter Abstrak --}}
                                    <p id="abstract_counter"
                                        class="text-xs text-gray-500 text-right mt-1 font-medium">0
                                        / 1000 karakter</p>
                                </div>
                            @endif

                            {{-- KONDISI KHUSUS UNTUK JALUR KELOMPOK / HYBRID --}}
                            @if (in_array($vacancy->registration_mode, ['kelompok', 'hybrid']))
                                <div class="mb-4 bg-blue-50 p-4 rounded-lg border border-blue-100">

                                    {{-- PILIHAN MODE UNTUK HYBRID --}}
                                    @if ($vacancy->registration_mode === 'hybrid')
                                        <div class="mb-4">
                                            <label class="block mb-2 text-sm font-bold text-[#37517e]">Daftar
                                                Sebagai:</label>
                                            <select id="hybrid-mode-select"
                                                class="block w-full p-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="individu">Individu (Sendiri)</option>
                                                <option value="kelompok">Kelompok (Tim)</option>
                                            </select>
                                        </div>
                                    @endif

                                    {{-- AREA INPUT ANGGOTA (Sembunyi default jika hybrid-individu) --}}
                                    <div id="group-input-area"
                                        class="{{ $vacancy->registration_mode === 'hybrid' ? 'hidden' : '' }}">
                                        <label class="block mb-2 text-sm font-bold text-[#37517e]">
                                            Anggota Kelompok (Masukkan Email)
                                        </label>
                                        <p class="text-xs text-gray-600 mb-3">
                                            Anggota: Min {{ $vacancy->min_members }} orang, Maks
                                            {{ $vacancy->max_members }} orang (termasuk Anda sebagai Ketua).
                                        </p>

                                        <div id="members-container" class="space-y-2">
                                            <div class="flex gap-2 member-input">
                                                {{-- Jika hybrid, input ini awalnya dinonaktifkan (disabled) --}}
                                                <input type="email" name="member_emails[]"
                                                    {{ $vacancy->registration_mode === 'kelompok' ? 'required' : '' }}
                                                    {{ $vacancy->registration_mode === 'hybrid' ? 'disabled' : '' }}
                                                    class="block w-full p-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="email.anggota1@test.com">
                                            </div>
                                        </div>

                                        <button type="button" id="add-member-btn"
                                            class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1 transition">
                                            <i class="bi bi-plus-circle"></i> Tambah Anggota Lainnya
                                        </button>
                                        <p class="text-[10px] text-gray-500 mt-2 italic">* Pastikan email anggota sudah
                                            terdaftar di sistem dan profil mereka (CV) sudah lengkap.</p>
                                    </div>
                                </div>
                            @endif

                            {{-- CATATAN UMUM (OPSIONAL) --}}
                            <div class="mb-4">
                                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Catatan
                                    Singkat (Opsional)</label>
                                <textarea id="notes" name="notes" rows="2"
                                    class="block w-full p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Kenapa Anda tertarik?"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                Kirim Lamaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SCRIPT UNTUK SWEETALERT DAN PENGHITUNG KARAKTER --}}
    <script type="module">
        // 1. LOGIKA SWEETALERT (DENGAN PERBAIKAN TOMBOL TAILWIND)
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{!! session('success') !!}",
                buttonsStyling: false, // Matikan style bawaan
                customClass: {
                    confirmButton: 'bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{!! session('error') !!}",
                buttonsStyling: false, // Matikan style bawaan
                customClass: {
                    confirmButton: 'bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 transition'
                }
            });
        @endif

        // 2. LOGIKA PENGHITUNG KARAKTER REAL-TIME
        document.addEventListener('DOMContentLoaded', function() {

            // Elemen Judul
            const titleInput = document.getElementById('research_title');
            const titleCounter = document.getElementById('title_counter');

            if (titleInput && titleCounter) {
                titleInput.addEventListener('input', function() {
                    titleCounter.textContent = this.value.length + ' / 255 karakter';
                });
            }

            // Elemen Abstrak
            const abstractInput = document.getElementById('research_abstract');
            const abstractCounter = document.getElementById('abstract_counter');

            if (abstractInput && abstractCounter) {
                abstractInput.addEventListener('input', function() {
                    abstractCounter.textContent = this.value.length + ' / 1000 karakter';
                });
            }
        });

        // =======================================================
        // LOGIKA TAMBAH ANGGOTA KELOMPOK DINAMIS
        // =======================================================
        const container = document.getElementById('members-container');
        const addBtn = document.getElementById('add-member-btn');
        // Max member dikurangi 1 karena ketua (yang login) sudah dihitung 1
        const maxMembers = {{ $vacancy->max_members ?? 1 }} - 1;

        if (container && addBtn) {
            addBtn.addEventListener('click', function() {
                const currentInputs = container.querySelectorAll('.member-input').length;

                // Cek agar tidak melebihi kuota maksimal kelompok
                if (currentInputs >= maxMembers) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Batas Maksimal',
                        text: `Lowongan ini maksimal ${maxMembers + 1} orang (termasuk Anda).`
                    });
                    return;
                }

                // Buat elemen input baru
                const div = document.createElement('div');
                div.className = 'flex gap-2 member-input mt-2';
                div.innerHTML = `
                    <input type="email" name="member_emails[]" required
                        class="block w-full p-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="email.anggota@test.com">
                    <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg border border-red-200 hover:bg-red-100 transition" title="Hapus baris ini">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                container.appendChild(div);
            });
        }

        // =======================================================
        // LOGIKA HYBRID (TAMPIL/SEMBUNYIKAN ANGGOTA KELOMPOK)
        // =======================================================
        const hybridSelect = document.getElementById('hybrid-mode-select');
        const groupArea = document.getElementById('group-input-area');
        const membersContainerObj = document.getElementById('members-container');

        if (hybridSelect && groupArea && membersContainerObj) {
            hybridSelect.addEventListener('change', function() {
                const isKelompok = this.value === 'kelompok';
                const inputs = membersContainerObj.querySelectorAll('input[name="member_emails[]"]');

                if (isKelompok) {
                    // Tampilkan area kelompok dan aktifkan inputnya
                    groupArea.classList.remove('hidden');
                    inputs.forEach(input => {
                        input.disabled = false;
                        input.required = true; // Wajib diisi kalau milih kelompok
                    });
                } else {
                    // Sembunyikan area kelompok dan matikan inputnya
                    groupArea.classList.add('hidden');
                    inputs.forEach(input => {
                        input.disabled = true; // Agar tidak dikirim ke Controller
                        input.required = false;
                    });
                }
            });
        }
    </script>
</body>

</html>
