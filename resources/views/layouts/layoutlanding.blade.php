<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SINAKERTRANS - Cari Magang')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    {{-- ===================== NAVBAR ===================== --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2 shrink-0">
                    <div
                        class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-md shadow-blue-600/30">
                        <i class="bi bi-briefcase-fill text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-blue-600 tracking-tight">SINAKERTRANS</span>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center space-x-3">

                    {{-- CEK 1: APAKAH YANG LOGIN ITU ADMIN? --}}
                    @if (Auth::guard('web')->check())
                        <span class="text-sm text-gray-500 mr-1">Halo, <strong
                                class="text-gray-800">Admin</strong></span>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 transition shadow-md shadow-blue-600/30">
                            <i class="bi bi-speedometer2"></i> Dashboard Admin
                        </a>

                        {{-- CEK 2: APAKAH YANG LOGIN ITU PESERTA MAGANG? --}}
                    @elseif(Auth::guard('magang')->check())
                        <div class="flex items-center gap-8">

                            {{-- Menu Navigasi --}}
                            <ul class="flex gap-6 items-center font-semibold text-sm text-[#37517e]">
                                <li>
                                    <a href="{{ route('dashboard.index') }}"
                                        class="{{ request()->is('/') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'hover:text-blue-600 pb-1' }} transition">
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
                                        class="{{ request()->is('status*') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'hover:text-blue-600 pb-1' }} transition">
                                        Status
                                    </a>
                                </li>
                                <li>
                                    <a href="/sertifikat"
                                        class="{{ request()->is('sertifikat') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'hover:text-blue-600 pb-1' }} transition">
                                        Sertifikat
                                    </a>
                                </li>
                            </ul>

                            {{-- Profile Dropdown --}}
                            <div class="relative group">
                                {{-- Trigger Button --}}
                                <div
                                    class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-xl hover:bg-blue-50 transition-all duration-200">
                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center border-2 border-blue-200 group-hover:border-blue-500 transition-all duration-200">
                                        <i class="bi bi-person-fill text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[13px] font-bold text-gray-800 leading-tight">
                                            {{ Auth::guard('magang')->user()->nama_peserta }}
                                        </p>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">Peserta Magang</p>
                                    </div>
                                    <i
                                        class="bi bi-chevron-down text-xs text-gray-500 transition-transform duration-200 group-hover:rotate-180"></i>
                                </div>

                                {{-- Dropdown Panel --}}
                                <div class="absolute right-0 hidden group-hover:block pt-1 w-56 z-9999">
                                    <div
                                        class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-gray-200/80 py-2 overflow-hidden">
                                        <div class="px-4 py-3 border-b border-gray-50 bg-blue-50/50">
                                            <p class="text-[11px] text-gray-500 mb-0.5">Login sebagai</p>
                                            <p class="text-sm font-bold text-gray-800 truncate">
                                                {{ Auth::guard('magang')->user()->nama_peserta }}
                                            </p>
                                        </div>
                                        <a href="{{ route('profile.edit') }}"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                            <i class="bi bi-person-vcard text-base"></i> Profil Saya
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition text-left font-semibold">
                                                <i class="bi bi-box-arrow-right text-base"></i> Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- CEK 3: JIKA BELUM LOGIN (TAMU) --}}
                    @else
                        <a href="{{ route('login') }}"
                            class="text-blue-600 border border-blue-200 px-5 py-2 rounded-full font-semibold text-sm hover:bg-blue-50 hover:border-blue-400 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 text-white px-5 py-2 rounded-full font-semibold text-sm hover:bg-blue-700 transition shadow-md shadow-blue-600/30">
                            Daftar Sekarang
                        </a>
                    @endif

                </div>

                {{-- Mobile Hamburger Button --}}
                <button id="mobile-menu-btn"
                    class="md:hidden flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 transition">
                    <i class="bi bi-list text-2xl text-gray-700" id="hamburger-icon"></i>
                </button>

            </div>
        </div>

        {{-- ===================== MOBILE MENU ===================== --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-4 space-y-1">

                @if (Auth::guard('web')->check())
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm">
                        <i class="bi bi-speedometer2"></i> Dashboard Admin
                    </a>
                @elseif(Auth::guard('magang')->check())
                    {{-- User Info Card --}}
                    <div class="flex items-center gap-3 px-3 py-3 mb-2 bg-blue-50 rounded-2xl border border-blue-100">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center border-2 border-blue-200 shrink-0">
                            <i class="bi bi-person-fill text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">
                                {{ Auth::guard('magang')->user()->nama_peserta }}</p>
                            <p class="text-xs text-blue-600 font-medium">Peserta Magang</p>
                        </div>
                    </div>

                    <a href="{{ route('dashboard.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium">
                        <i class="bi bi-house text-blue-500"></i> Home
                    </a>
                    <a href="{{ request()->is('/') ? '#aboutus' : '/#aboutus' }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium">
                        <i class="bi bi-info-circle text-blue-500"></i> Tentang Kami
                    </a>
                    <a href="{{ route('status') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium">
                        <i class="bi bi-card-checklist text-blue-500"></i> Status
                    </a>
                    <a href="/sertifikat"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium">
                        <i class="bi bi-award text-blue-500"></i> Sertifikat
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium">
                        <i class="bi bi-person-vcard text-blue-500"></i> Profil Saya
                    </a>
                    <div class="border-t border-gray-100 pt-2 mt-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 text-sm font-semibold transition">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="block text-center px-4 py-2.5 rounded-xl border border-blue-200 text-blue-600 font-semibold text-sm hover:bg-blue-50 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                        class="block text-center px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition">
                        Daftar Sekarang
                    </a>
                @endif

            </div>
        </div>
    </nav>

    {{-- ===================== PAGE CONTENT ===================== --}}
    @yield('content')

    {{-- ===================== FOOTER ===================== --}}
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
                                <i class="bi bi-twitter text-white"></i>
                            </a>
                            <a href="#"
                                class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[#37517e] transition duration-300 border border-slate-700 hover:border-blue-400">
                                <i class="bi bi-facebook text-white"></i>
                            </a>
                            <a href="#"
                                class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[#37517e] transition duration-300 border border-slate-700 hover:border-blue-400">
                                <i class="bi bi-instagram text-white"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-4 border-b-2 border-blue-500 inline-block pb-1">Useful Links
                        </h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-blue-400 transition">Home</a></li>
                            <li><a href="#about" class="hover:text-blue-400 transition">Tentang Kami</a></li>
                            <li><a href="#services" class="hover:text-blue-400 transition">Layanan</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Terms of service</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Privacy policy</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-4 border-b-2 border-blue-500 inline-block pb-1">Layanan Kami
                        </h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-blue-400 transition">Usulan Kepegawaian</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Dokumen Digital</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Simulasi Kredit</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Arsip & Surat</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-4 border-b-2 border-blue-500 inline-block pb-1">Newsletter</h4>
                        <p class="text-gray-400 mb-4">Berlangganan untuk mendapatkan informasi terbaru seputar
                            kepegawaian.</p>
                        <form action="#" class="flex">
                            <input type="email" placeholder="Email Anda"
                                class="w-full px-4 py-2 rounded-l-md bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-blue-500">
                            <button type="submit"
                                class="bg-blue-600 px-4 py-2 rounded-r-md hover:bg-blue-700 transition font-semibold">
                                Sub
                            </button>
                        </form>
                    </div>

                </div>

                <div class="border-t border-slate-700 pt-8 text-center text-gray-400">
                    <p>&copy; Copyright <strong class="text-white">KEPEGAWAIAN</strong> {{ date('Y') }} Dinas
                        Tenaga Kerja. Sistem Manajemen Magang.</p>
                </div>
            </div>
        </footer>
    @endif

    {{-- ===================== MOBILE MENU SCRIPT ===================== --}}
    <script>
        // SECTION: Mobile hamburger toggle
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const hamburgerIcon = document.getElementById('hamburger-icon');

        if (mobileBtn) {
            mobileBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                hamburgerIcon.classList.toggle('bi-list');
                hamburgerIcon.classList.toggle('bi-x-lg');
            });
        }
    </script>

    @stack('script')

</body>

</html>
