{{-- resources/views/partials/sidebar.blade.php --}}

<aside class="w-64 bg-white border-r border-slate-200 shrink-0 hidden md:flex flex-col">

    {{-- LOGO --}}
    <div class="h-16 flex items-center justify-center border-b border-slate-200">
        <h1 class="text-lg font-bold tracking-wide text-blue-700">
            SINAKERTRANS
        </h1>
    </div>

    {{-- USER INFO --}}
    <div class="p-4 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="relative w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center overflow-hidden">

                {{-- FOTO PROFIL --}}
                @if ($user->profile_photo_path)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Foto Profil"
                        class="absolute inset-0 w-full h-full object-cover rounded-full">
                @else
                    {{-- ICON DEFAULT --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                @endif

            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">
                    {{ Auth::user()->name }}
                </p>

                @php
                    $user = Auth::user();
                    $hakAkses = \App\Models\MagangAccessRight::where('user_id', $user->id)->first();
                @endphp

                <p class="text-xs text-slate-500 uppercase">
                    {{ $hakAkses->role === 'superadmin' ? 'Super Admin' : 'Admin ' . $hakAkses->division_name }}
                </p>
            </div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto text-sm">

        {{-- DASHBOARD --}}
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-4 py-2 rounded-md font-medium transition
           {{ request()->routeIs('admin.dashboard')
               ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600'
               : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
            <i class="bi bi-speedometer2 mr-3"></i>
            Dashboard
        </a>

        {{-- KEPEGAWAIAN --}}
        @if ($hakAkses->role === 'superadmin')
            <div class="mt-6 mb-2 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                Kepegawaian
            </div>

            <a href="#"
                class="flex items-center px-4 py-2 rounded-md font-medium text-slate-600 hover:bg-slate-100">
                <i class="bi bi-people mr-3"></i>
                Data Pegawai
            </a>

            <a href="#"
                class="flex items-center px-4 py-2 rounded-md font-medium text-slate-600 hover:bg-slate-100">
                <i class="bi bi-cash-stack mr-3"></i>
                Penggajian
            </a>
        @endif

        {{-- MANAJEMEN MAGANG --}}
        <div class="mt-6 mb-2 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
            Manajemen Magang
        </div>

        <a href="{{ route('admin.vacancies.index') }}"
            class="flex items-center px-4 py-2 rounded-md font-medium transition
           {{ request()->routeIs('admin.vacancies*')
               ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600'
               : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
            <i class="bi bi-briefcase mr-3"></i>
            Lowongan
        </a>

        <a href="{{ route('admin.applications.index') }}"
            class="flex items-center px-4 py-2 rounded-md font-medium transition
           {{ request()->routeIs('admin.applications*')
               ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600'
               : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
            <i class="bi bi-file-earmark-check mr-3"></i>
            Verifikasi
        </a>

        <a href="{{ route('admin.assessments.index') }}"
            class="flex items-center px-4 py-2 rounded-md font-medium transition
           {{ request()->routeIs('admin.assessments*')
               ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600'
               : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
            <i class="bi bi-pencil-square mr-3"></i>
            Penilaian
        </a>
    </nav>

    {{-- AKUN --}}
    <div class="border-t border-slate-200 p-4">
        <a href="{{ route('admin.profile') }}"
            class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition
           {{ request()->routeIs('admin.profile')
               ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600'
               : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
            <i class="bi bi-person-circle mr-3"></i>
            Profil Saya
        </a>
    </div>

    {{-- LOGOUT --}}
    <div class="border-t border-slate-200 p-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center w-full px-4 py-2 text-sm font-medium text-red-600 rounded-md hover:bg-red-50 transition">
                <i class="bi bi-box-arrow-right mr-3"></i>
                Keluar
            </button>
        </form>
    </div>

</aside>
