<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SINAKERTRANS - Magang</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-50">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 item items-center"> {{-- Tinggi disesuaikan agar proporsional --}}
                
                {{-- Logo --}}
                <div class="flex-shrink-0">
                    <a href="/" class="text-2xl font-bold text-blue-600">SINAKERTRANS</a>
                </div>

                <div class="hidden md:flex items-center space-x-3">
                    {{-- CEK 1: ADMIN --}}
                    @if (Auth::guard('web')->check())
                        <span class="text-sm text-gray-600 mr-2">Halo, <strong>Admin</strong></span>
                        <a href="{{ route('admin.dashboard') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Ke Dashboard Admin
                        </a>

                    {{-- CEK 2: PESERTA MAGANG --}}
                    @elseif(Auth::guard('magang')->check())
                        <div class="hidden md:flex items-center space-x-6">

                                {{-- 1. Menu Navigasi --}}
                                <ul class="flex gap-6 items-center font-bold text-[11px] uppercase tracking-widest text-[#37517e]">
                                    <li><a href="/" class="hover:text-blue-600 transition">Home</a></li>
                                    <li><a href="/#aboutus" class="hover:text-blue-600 transition">Tentang Kami</a></li>
                                    <li>
                                        <a href="{{ route('status') }}" 
                                        class="{{ request()->routeIs('status') ? 'text-blue-600 border-b-2 border-blue-600' : '' }} hover:text-blue-600 transition pb-1">
                                            Status
                                        </a>
                                    </li>
                                    <li><a href="/sertifikat" class="hover:text-blue-600 transition">Sertifikat</a></li>
                                </ul>

                                {{-- 2. Dropdown Profile --}}
                                <div class="relative group border-l pl-4 ml-2" tabindex="0"> {{-- Tambahkan tabindex agar bisa menerima focus klik --}}
                                    <button class="flex items-center gap-2 text-[#37517e] focus:outline-none py-1 px-2 rounded-lg hover:bg-gray-50 transition-all pointer-events-none">
                                        <div class="bg-blue-100 p-1 rounded-full flex items-center justify-center border border-blue-200">
                                            <i class="bi bi-person-circle text-base text-blue-600"></i>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-[13px] font-bold leading-tight">
                                                {{ Auth::guard('magang')->user()->nama_peserta }}
                                            </p>
                                            <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Peserta Magang</p>
                                        </div>
                                        <i class="bi bi-chevron-down text-[9px] transition-transform group-hover:rotate-180 group-focus-within:rotate-180"></i>
                                    </button>

                                {{-- Isi Menu Dropdown --}}
                                <div class="absolute right-0 w-40 pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[9999]"">
                    
                                {{-- Invisible Bridge --}}
                                <div class="h-2 w-full bg-transparent"></div>
                    
                                    <div class="bg-white border border-gray-100 rounded-xl shadow-2xl py-2 overflow-hidden ring-1 ring-black ring-opacity-5">
                                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-[12px] font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                            <i class="bi bi-person-vcard text-base"></i> Profil Saya
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                                            @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-[12px] font-bold text-red-600 hover:bg-red-50 transition text-left">
                                            <i class="bi bi-box-arrow-right text-base"></i> Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div> 
                        </div> 

                    {{-- CEK 3: TAMU (BELUM LOGIN) --}}
                    @else
                        <div class="flex gap-3">
                            <a href="{{ route('login') }}" class="bg-white text-blue-600 border border-blue-600 px-6 py-2 rounded-full font-bold hover:bg-blue-50 transition">Masuk</a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-2 rounded-full font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">Daftar</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-white py-8 mt-12 text-center text-sm">
        <p>&copy; Copyright KEPEGAWAIAN 2026 Dinas Tenaga Kerja. Sistem Manajemen Magang.</p>
    </footer>

</body>
</html>