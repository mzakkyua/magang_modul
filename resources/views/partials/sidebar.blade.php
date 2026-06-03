{{-- resources/views/partials/sidebar.blade.php --}}

{{-- ===================== DEFINISI VARIABEL (harus di atas segalanya) ===================== --}}
@php
    $user = Auth::user();
    $hakAkses = request()->attributes->get('magang_access');
    $isSuperAdmin = $hakAkses?->role === 'superadmin';
    $roleLabel = $isSuperAdmin ? 'Super Admin' : 'Admin ' . ($hakAkses?->division_name ?? '');
@endphp

{{-- ===================== MOBILE DRAWER OVERLAY ===================== --}}
<div id="mobile-overlay" onclick="toggleMobileMenu()"
    class="fixed inset-0 bg-black/40 z-40 hidden md:hidden backdrop-blur-sm transition-opacity">
</div>

{{-- ===================== MOBILE DRAWER ===================== --}}
<div id="mobile-drawer"
    class="fixed top-0 left-0 h-full w-72 bg-white shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden overflow-y-auto">

    {{-- Header drawer --}}
    <div class="h-14 flex items-center justify-between px-4 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="bi bi-briefcase-fill text-white text-xs"></i>
            </div>
            <span class="text-sm font-extrabold text-gray-900">SINAKERTRANS</span>
        </div>
        <button onclick="toggleMobileMenu()"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100">
            <i class="bi bi-x-lg text-sm"></i>
        </button>
    </div>

    {{-- Isi drawer: copy konten dari sidebar --}}
    <div class="px-4 py-3 border-b border-slate-100">
        {{-- User info --}}
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center border-2 border-blue-200 shrink-0">
                @if ($user->profile_photo_path)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                        class="w-full h-full object-cover rounded-xl">
                @else
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-gray-800 truncate">{{ $user->name }}</p>
                <span
                    class="inline-flex items-center mt-0.5
                    {{ $isSuperAdmin ? 'text-blue-600 bg-blue-50 border-blue-200' : 'text-indigo-600 bg-indigo-50 border-indigo-200' }}
                    text-[10px] font-bold uppercase tracking-wide border px-2 py-0.5 rounded-full">
                    • {{ $roleLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- Navigasi (sama persis dengan sidebar desktop) --}}
    <nav class="px-3 py-4 text-sm space-y-0.5">

        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
            {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="bi bi-speedometer2 mr-2.5 text-base"></i> Dashboard
        </a>

        @if ($isSuperAdmin)
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest px-1 pt-4 pb-1">Kepegawaian
            </p>
            <a href="{{ route('admin.pegawai.index') }}"
                class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
                {{ request()->routeIs('admin.pegawai*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="bi bi-people mr-2.5 text-base"></i> Data Pegawai
            </a>
            <a href="#"
                class="flex items-center px-3 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100">
                <i class="bi bi-cash-stack mr-2.5 text-base"></i> Penggajian
            </a>
        @endif

        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest px-1 pt-4 pb-1">Manajemen Magang
        </p>

        <a href="{{ route('admin.vacancies.index') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
            {{ request()->routeIs('admin.vacancies*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="bi bi-briefcase mr-2.5 text-base"></i> Lowongan
        </a>
        <a href="{{ route('admin.applications.index') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
            {{ request()->routeIs('admin.applications*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="bi bi-file-earmark-check mr-2.5 text-base"></i> Verifikasi
        </a>
        <a href="{{ route('admin.assessments.index') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
            {{ request()->routeIs('admin.assessments*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="bi bi-pencil-square mr-2.5 text-base"></i> Penilaian
        </a>
        <a href="{{ route('admin.peserta.index') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
            {{ request()->routeIs('admin.peserta.index') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="bi bi-person-lines-fill mr-2.5 text-base"></i> Rekap Peserta
        </a>

        @if ($isSuperAdmin)
            <a href="{{ route('admin.calendar.index') }}"
                class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
                {{ request()->routeIs('admin.calendar*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="bi bi-calendar-check mr-2.5 text-base"></i> Jadwal Kegiatan
            </a>
        @endif

        <a href="{{ route('admin.certificates.create') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
            {{ request()->routeIs('admin.certificates.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="bi bi-cloud-arrow-up mr-2.5 text-base"></i> Upload Sertifikat
        </a>

        @if ($isSuperAdmin)
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest px-1 pt-4 pb-1">Pengaturan</p>
            <a href="{{ route('admin.divisions.index') }}"
                class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
                {{ request()->routeIs('admin.divisions*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="bi bi-diagram-3 mr-2.5 text-base"></i> Master Divisi
            </a>
            <a href="{{ route('admin.division-settings.index') }}"
                class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
                {{ request()->routeIs('admin.division-settings*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="bi bi-sliders mr-2.5 text-base"></i> Kuota Divisi
            </a>
        @endif

        {{-- Footer nav --}}
        <div class="border-t border-slate-100 pt-3 mt-3 space-y-0.5">
            <a href="{{ route('admin.profile') }}"
                class="flex items-center px-3 py-2.5 rounded-xl font-semibold transition-all
                {{ request()->routeIs('admin.profile') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="bi bi-person-circle mr-2.5 text-base"></i> Profil Saya
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center w-full px-3 py-2.5 rounded-xl font-semibold text-red-500 hover:bg-red-50 transition-all text-sm text-left">
                    <i class="bi bi-box-arrow-right mr-2.5 text-base"></i> Keluar
                </button>
            </form>
        </div>

    </nav>
</div>
{{-- ===================== SIDEBAR ADMIN ===================== --}}
<aside class="w-64 bg-white border-r border-slate-100 shrink-0 hidden md:flex flex-col shadow-sm">

    {{-- ===================== LOGO ===================== --}}
    <div class="h-16 flex items-center px-5 border-b border-slate-100">
        <div
            class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center shadow-md shadow-blue-600/30 mr-3 shrink-0">
            <i class="bi bi-briefcase-fill text-white text-sm"></i>
        </div>
        <div>
            <h1 class="text-sm font-extrabold tracking-tight text-gray-900 leading-none">SINAKERTRANS</h1>
            <p class="text-[10px] text-gray-400 mt-0.5 font-medium">Management System</p>
        </div>
    </div>


    <div class="px-4 py-3.5 border-b border-slate-100">
        <div class="flex items-center">

            {{-- Avatar --}}
            <div
                class="relative w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center overflow-hidden shrink-0 border-2 border-blue-200 mr-3">
                @if ($user->profile_photo_path)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Foto Profil"
                        class="absolute inset-0 w-full h-full object-cover rounded-xl">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                @endif
            </div>

            {{-- Info user --}}
            <div class="min-w-0">
                <p class="text-sm font-bold text-gray-800 leading-tight truncate">
                    {{ $user->name }}
                </p>

                <span
                    class="inline-flex items-center mt-1
                    {{ $isSuperAdmin ? 'text-blue-600 bg-blue-50 border-blue-200' : 'text-indigo-600 bg-indigo-50 border-indigo-200' }}
                    text-[10px] font-bold uppercase tracking-wide border px-2 py-0.5 rounded-full">
                    • {{ $roleLabel }}
                </span>
            </div>

        </div>
    </div>

    {{-- ===================== NAVIGATION ===================== --}}
    <nav class="flex-1 px-3 py-4 overflow-y-auto text-sm">

        {{-- ===================== DASHBOARD ===================== --}}
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.dashboard')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

            <i class="bi bi-speedometer2 mr-2.5 text-base"></i>
            Dashboard
        </a>

        {{-- ===================== SECTION: KEPEGAWAIAN ===================== --}}
        @if ($isSuperAdmin)
            <div class="mt-5 mb-2 px-1">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">
                    Kepegawaian
                </p>
            </div>

            {{-- Data Pegawai --}}
            <a href="{{ route('admin.pegawai.index') }}"
                class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
                {{ request()->routeIs('admin.pegawai*')
                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

                <i class="bi bi-people mr-2.5 text-base"></i>
                Data Pegawai
            </a>

            {{-- Penggajian --}}
            <a href="#"
                class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
                text-slate-600 hover:bg-slate-100 hover:text-slate-900">

                <i class="bi bi-cash-stack mr-2.5 text-base"></i>
                Penggajian
            </a>
        @endif

        {{-- ===================== SECTION: MANAJEMEN MAGANG ===================== --}}
        <div class="mt-5 mb-2 px-1">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">
                Manajemen Magang
            </p>
        </div>

        {{-- Lowongan --}}
        <a href="{{ route('admin.vacancies.index') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.vacancies*')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

            <i class="bi bi-briefcase mr-2.5 text-base"></i>
            Lowongan
        </a>

        {{-- Verifikasi --}}
        <a href="{{ route('admin.applications.index') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.applications*')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

            <i class="bi bi-file-earmark-check mr-2.5 text-base"></i>
            Verifikasi
        </a>

        {{-- Penilaian --}}
        <a href="{{ route('admin.assessments.index') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.assessments*')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

            <i class="bi bi-pencil-square mr-2.5 text-base"></i>
            Penilaian
        </a>

        {{-- Rekap Peserta --}}
        <a href="{{ route('admin.peserta.index') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.peserta.index')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

            <i class="bi bi-person-lines-fill mr-2.5 text-base"></i>
            Rekap Peserta
        </a>

        {{-- Jadwal Kegiatan --}}
        @if ($isSuperAdmin)
            <a href="{{ route('admin.calendar.index') }}"
                class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
                {{ request()->routeIs('admin.calendar*')
                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

                <i class="bi bi-calendar-check mr-2.5 text-base"></i>
                Jadwal Kegiatan
            </a>
        @endif

        {{-- Upload Sertifikat --}}
        <a href="{{ route('admin.certificates.create') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.certificates.create') || request()->routeIs('admin.certificates.store')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

            <i class="bi bi-cloud-arrow-up mr-2.5 text-base"></i>
            Upload Sertifikat
        </a>

        {{-- ===================== SECTION: PENGATURAN MAGANG ===================== --}}
        @if ($isSuperAdmin)
            <div class="mt-6 mb-2 px-1">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">
                    Pengaturan Magang
                </p>
            </div>

            {{-- Master Divisi --}}
            <a href="{{ route('admin.divisions.index') }}"
                class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
                {{ request()->routeIs('admin.divisions*')
                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

                <i class="bi bi-diagram-3 mr-2.5 text-base"></i>
                Master Divisi
            </a>

            {{-- Kuota Divisi --}}
            <a href="{{ route('admin.division-settings.index') }}"
                class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
                {{ request()->routeIs('admin.division-settings*')
                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

                <i class="bi bi-sliders mr-2.5 text-base"></i>
                Kuota Divisi
            </a>
        @endif

    </nav>

    {{-- ===================== FOOTER ===================== --}}
    <div class="border-t border-slate-100 p-3">

        {{-- Profil --}}
        <a href="{{ route('admin.profile') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 text-sm font-semibold rounded-xl transition-all duration-150
            {{ request()->routeIs('admin.profile')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

            <i class="bi bi-person-circle mr-2.5 text-base"></i>
            Profil Saya
        </a>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST"
            onsubmit="this.querySelector('button').disabled = true;">

            @csrf

            <button type="submit"
                class="flex items-center w-full px-3 py-2.5 text-sm font-semibold rounded-xl
                text-red-500 hover:bg-red-50 hover:text-red-600 transition-all duration-150
                disabled:opacity-50 disabled:cursor-not-allowed text-left">

                <i class="bi bi-box-arrow-right mr-2.5 text-base"></i>
                Keluar
            </button>

        </form>

    </div>

</aside>

<script>
    function toggleMobileMenu() {
        const drawer = document.getElementById('mobile-drawer');
        const overlay = document.getElementById('mobile-overlay');
        const isOpen = !drawer.classList.contains('-translate-x-full');

        if (isOpen) {
            drawer.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        } else {
            drawer.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // cegah scroll background
        }
    }

    // Tutup drawer kalau layar diperbesar ke desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            document.getElementById('mobile-drawer').classList.add('-translate-x-full');
            document.getElementById('mobile-overlay').classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
</script>
