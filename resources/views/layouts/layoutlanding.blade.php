<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SINAKERTRANS - Cari Magang')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="bg-gray-50 font-sans text-gray-800">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-blue-600">SINAKERTRANS</span>
                </div>

                <div class="hidden md:flex items-center space-x-3">
                    {{-- CEK 1: APAKAH YANG LOGIN ITU ADMIN? --}}
                    @if (Auth::guard('web')->check())
                        <span class="text-sm text-gray-600 mr-2">Halo, <strong>Admin</strong></span>
                        <a href="{{ route('admin.dashboard') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Ke Dashboard Admin
                        </a>
                    @elseif(Auth::guard('magang')->check())
                        <div class="flex items-center gap-10"> {{-- Container utama untuk menu + profile --}}

                            {{-- 1. Menu Navigasi (DI KIRI) --}}
                            <ul
                                class="flex gap-8 items-center font-bold text-sm uppercase tracking-wider text-[#37517e]">
                                <li>
                                    <a href="{{route('dashboard.index')}}"
                                        class="{{ request()->is('/') ? 'text-blue-600 border-b-2 border-blue-600' : '' }} hover:text-blue-600 transition pb-1">
                                        Home
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ request()->is('/') ? '#aboutus' : '/#aboutus' }}"
                                        class="hover:text-blue-600 transition pb-1">
                                        Tentang Kami
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('status') }}"
                                        class="{{ request()->is('status*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }} hover:text-blue-600 transition pb-1">
                                        Status
                                    </a>
                                </li>
                                <li>
                                    <a href="/sertifikat"
                                        class="{{ request()->is('sertifikat') ? 'text-blue-600 border-b-2 border-blue-600' : '' }} hover:text-blue-600 transition pb-1">
                                        Sertifikat
                                    </a>
                                </li>
                            </ul>

                            {{-- 2. Profile Dropdown (DI KANAN) --}}
                            <div class="relative group"> {{-- Tambahkan class 'group' di sini --}}
                                {{-- Tombol Pemicu --}}
                                <div
                                    class="flex items-center gap-3 text-[#37517e] cursor-pointer px-3 py-2 rounded-lg transition-all duration-200">
                                    <div class="bg-blue-100 p-1.5 rounded-full flex items-center justify-center">
                                        <i class="bi bi-person-circle text-lg text-blue-600"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[13px] font-bold leading-tight">
                                            {{ Auth::guard('magang')->user()->nama_peserta }}
                                        </p>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Peserta Magang
                                        </p>
                                    </div>
                                    <i
                                        class="bi bi-chevron-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                                </div>

                                {{-- Isi Menu Dropdown (Muncul saat group di-hover) --}}
                                <div class="absolute right-0 hidden group-hover:block pt-2 w-48 z-9999">
                                    <div class="bg-white border border-gray-100 rounded-xl shadow-xl py-2">
                                        <a href="{{ route('profile.edit') }}"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                            <i class="bi bi-person-vcard text-base"></i> Profil Saya
                                        </a>

                                        <div class="border-t border-gray-100 my-1"></div>

                                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition text-left font-semibold">
                                                <i class="bi bi-box-arrow-right text-base"></i> Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- CEK 3: JIKA BELUM LOGIN (TAMU) --}}
                        @else
                            <a href="{{ route('login') }}"
                                class="bg-blue-600 text-white px-5 py-2 rounded-full font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/30"">Masuk</a>
                            <a href="{{ route('register') }}"
                                class="bg-blue-600 text-white px-5 py-2 rounded-full font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                                Daftar Sekarang
                            </a>
                    @endif
                </div>
            </div>
    </nav>

    @yield('content')

@if (!isset($hideFooter))
    

    <footer id="footer" class="bg-gray-800 text-white pt-16 pb-8 text-sm">

        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">

                <div>
                    <a href="/" class="flex items-center mb-4">
                        <span class="text-2xl font-bold tracking-wider">KEPEGAWAIAN</span>
                    </a>
                    <div class="text-gray-400 space-y-2">
                        <p>Jln. Dukuh Menanggal 124 - 126, Gayungan</p>
                        <p>Surabaya 60234, Jawa Timur</p>
                        <p class="mt-4"><strong class="text-white">Phone:</strong> (031) 8290005</p>
                        <p><strong class="text-white">Email:</strong> disnakertrans@jatimprov.go.id</p>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[#37517e] transition duration-300 border border-slate-700 hover:border-blue-400">
                            <i class="fa-brands fa-twitter text-white"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[#37517e] transition duration-300 border border-slate-700 hover:border-blue-400">
                            <i class="fa-brands fa-facebook-f text-white"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[#37517e] transition duration-300 border border-slate-700 hover:border-blue-400">
                            <i class="fa-brands fa-instagram text-white"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 border-b-2 border-blue-500 inline-block pb-1">Useful Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-blue-400 transition">Home</a></li>
                        <li><a href="#about" class="hover:text-blue-400 transition">Tentang Kami</a></li>
                        <li><a href="#services" class="hover:text-blue-400 transition">Layanan</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition">Terms of service</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition">Privacy policy</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 border-b-2 border-blue-500 inline-block pb-1">Layanan Kami</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-blue-400 transition">Usulan Kepegawaian</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition">Dokumen Digital</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition">Simulasi Kredit</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition">Arsip & Surat</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 border-b-2 border-blue-500 inline-block pb-1">Newsletter</h4>
                    <p class="text-gray-400 mb-4">Berlangganan untuk mendapatkan informasi terbaru seputar kepegawaian.
                    </p>
                    <form action="#" class="flex">
                        <input type="email" placeholder="Email Anda"
                            class="w-full px-4 py-2 rounded-l-md bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-blue-500">
                        <button type="submit"
                            class="bg-blue-600 px-4 py-2 rounded-r-md hover:bg-blue-700 transition">Sub</button>
                    </form>
                </div>

            </div>

            <div class="border-t border-slate-800 pt-8 text-center text-gray-400">
                <p>&copy; Copyright <strong>KEPEGAWAIAN</strong> {{ date('Y') }} Dinas Tenaga Kerja. Sistem
                    Manajemen Magang.</p>
            </div>
        </div>
    </footer> 
    @endif
    @stack('script')
    
</body>

</html>
