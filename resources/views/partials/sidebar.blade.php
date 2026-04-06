{{-- resources/views/partials/sidebar.blade.php --}}

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

    {{-- ===================== USER INFO ===================== --}}
    @php
        $user = Auth::user();
        $hakAkses = \App\Models\MagangAccessRight::where('user_id', $user->id)->first();
        $isSuperAdmin = $hakAkses->role === 'superadmin';
        $roleLabel = $isSuperAdmin ? 'Super Admin' : 'Admin ' . $hakAkses->division_name;
    @endphp

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

        {{-- Dashboard --}}
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
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Kepegawaian</p>
            </div>

            <a href="{{ route('admin.pegawai.index') }}"
                class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.pegawai*')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="bi bi-people mr-2.5 text-base"></i>
                Data Pegawai
            </a>

            <a href="#"
                class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all duration-150">
                <i class="bi bi-cash-stack mr-2.5 text-base"></i>
                Penggajian
            </a>
        @endif

        {{-- ===================== SECTION: MANAJEMEN MAGANG ===================== --}}
        <div class="mt-5 mb-2 px-1">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Manajemen Magang</p>
        </div>

        <a href="{{ route('admin.vacancies.index') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.vacancies*')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <i class="bi bi-briefcase mr-2.5 text-base"></i>
            Lowongan
        </a>

        <a href="{{ route('admin.applications.index') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.applications*')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <i class="bi bi-file-earmark-check mr-2.5 text-base"></i>
            Verifikasi
        </a>

        <a href="{{ route('admin.assessments.index') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.assessments*')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <i class="bi bi-pencil-square mr-2.5 text-base"></i>
            Penilaian
        </a>

        {{-- BUGFIX: diseragamkan ke pattern yang sama --}}
        <a href="{{ route('admin.calendar.index') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.calendar*')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <i class="bi bi-calendar-check mr-2.5 text-base"></i>
            Jadwal Kegiatan
        </a>

        {{-- MENU SERTIFIKAT & NILAI --}}
        <a href="{{ route('admin.certificate.create') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 rounded-xl font-semibold transition-all duration-150
            {{ request()->routeIs('admin.certificate*')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <i class="bi bi-award mr-2.5 text-base"></i>
            Sertifikat & Nilai
        </a>

    </nav>

    {{-- ===================== FOOTER: PROFIL & LOGOUT ===================== --}}
    <div class="border-t border-slate-100 p-3">

        <a href="{{ route('admin.profile') }}"
            class="flex items-center px-3 py-2.5 mb-0.5 text-sm font-semibold rounded-xl transition-all duration-150
            {{ request()->routeIs('admin.profile')
                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <i class="bi bi-person-circle mr-2.5 text-base"></i>
            Profil Saya
        </a>

        <form action="{{ route('logout') }}" method="POST" onsubmit="this.querySelector('button').disabled = true;">
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
