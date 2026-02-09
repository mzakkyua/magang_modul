<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js','resources/js/admin/delete-confirm.js','resources/js/admin/vacancy-create.js'])
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-slate-800 text-white shrink-0 hidden md:flex flex-col">
            <div class="h-16 flex items-center justify-center bg-slate-900 shadow-md">
                <h1 class="text-xl font-bold tracking-wider">SINAKERTRANS</h1>
            </div>

            <div class="p-4 border-b border-slate-700 bg-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-600 flex items-center justify-center">
                        <i class="bi bi-person-fill text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                        @php
                            $user = Auth::user();
                            $hakAkses = \App\Models\MagangAccessRight::where('user_id', $user->id)->first();
                        @endphp
                        <p class="text-xs text-slate-400 uppercase">
                            {{ $hakAkses->role == 'superadmin' ? 'Super Admin' : 'Admin ' . $hakAkses->division_name }}
                        </p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                
                <a href="{{ route('admin.dashboard') }}" 
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors 
                    {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i class="bi bi-speedometer2 mr-3"></i> Dashboard
                </a>

                @if($hakAkses->role === 'superadmin')
                    <div class="mt-6 mb-2 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Kepegawaian
                    </div>
                    <a href="#" class="flex items-center px-4 py-2 text-sm font-medium text-slate-300 rounded-md hover:bg-slate-700 hover:text-white">
                        <i class="bi bi-people mr-3"></i> Data Pegawai
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-sm font-medium text-slate-300 rounded-md hover:bg-slate-700 hover:text-white">
                        <i class="bi bi-cash-stack mr-3"></i> Penggajian
                    </a>
                @endif

                <div class="mt-6 mb-2 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Manajemen Magang
                </div>

                <a href="{{ route('admin.vacancies.index') }}" 
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors
                    {{ request()->routeIs('admin.vacancies*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i class="bi bi-briefcase mr-3"></i> Lowongan
                </a>

                <a href="{{ route('admin.applications.index') }}" 
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors
                    {{ request()->routeIs('admin.applications*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i class="bi bi-file-earmark-check mr-3"></i> Verifikasi
                </a>

                <a href="{{ route('admin.assessments.index') }}" 
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors
                    {{ request()->routeIs('admin.assessments*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i class="bi bi-pencil-square mr-3"></i> Penilaian
                </a>
            </nav>

            <div class="p-4 border-t border-slate-700">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm font-medium text-red-400 rounded-md hover:bg-slate-700 hover:text-red-300 transition-colors">
                        <i class="bi bi-box-arrow-right mr-3"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="h-16 flex items-center justify-between px-6 bg-white shadow border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    @yield('title')
                </h2>
                </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200" role="alert">
                        <span class="font-medium">Berhasil!</span> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200" role="alert">
                        <span class="font-medium">Error!</span> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
@stack('scripts')
</html>