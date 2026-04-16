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

        /* ── NAVBAR: animated gradient bottom border ── */
        #main-navbar::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.35) 30%, rgba(99, 102, 241, 0.35) 60%, transparent);
        }

        /* ── NAV LINKS: sliding underline on hover ── */
        .nav-slide-link {
            position: relative;
            padding-bottom: 2px;
        }

        .nav-slide-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #2563eb;
            border-radius: 2px;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-slide-link:hover::after,
        .nav-slide-link.active::after {
            width: 100%;
        }

        /* ── LOGO: subtle pulse ring ── */
        .logo-pulse-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 14px;
            border: 1px solid rgba(37, 99, 235, 0.25);
            animation: logo-pulse 3s ease-in-out infinite;
        }

        @keyframes logo-pulse {

            0%,
            100% {
                opacity: 0.5;
                transform: scale(1);
            }

            50% {
                opacity: 0;
                transform: scale(1.18);
            }
        }

        /* ── PROFILE DROPDOWN: CSS hover ── */
        .profile-dropdown-group:hover .profile-dropdown-panel {
            display: block;
        }

        /* ── PRIMARY BUTTON: shimmer on hover ── */
        .btn-shimmer {
            position: relative;
            overflow: hidden;
        }

        .btn-shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transition: left 0.4s;
        }

        .btn-shimmer:hover::before {
            left: 100%;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    {{-- ===================== NAVBAR ===================== --}}
    <nav id="main-navbar"
        class="bg-white/90 backdrop-blur-xl shadow-sm shadow-gray-200/60 sticky top-0 z-50 border-b border-gray-100/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">


                {{-- LOGIKA LINK LOGO DINAMIS --}}
                @php
                    $logoUrl = url('/'); // Default ke halaman depan (Landing Page)

                    if (Auth::guard('magang')->check()) {
                        $logoUrl = route('dashboard.index'); // Ke Dashboard Mahasiswa
                    } elseif (Auth::guard('web')->check()) {
                        $logoUrl = route('admin.dashboard'); // Ke Dashboard Admin
                    }
                @endphp

                <a href="{{ $logoUrl }}" class="flex items-center gap-2.5 shrink-0 transition hover:opacity-80">
                    <div class="logo-pulse-ring relative w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center"
                        style="box-shadow: 0 0 0 3px rgba(37,99,235,0.15), 0 4px 12px rgba(37,99,235,0.3);">
                        <i class="bi bi-briefcase-fill text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-extrabold text-blue-600 tracking-tight">SINAKERTRANS</span>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center gap-3">

                    {{-- CEK 1: APAKAH YANG LOGIN ITU ADMIN? --}}
                    @if (Auth::guard('web')->check())
                        <span class="text-sm text-gray-500 mr-1">Halo, <strong
                                class="text-gray-800">Admin</strong></span>
                        <a href="{{ route('admin.dashboard') }}"
                            class="btn-shimmer flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-700 transition-all duration-200 shadow-md shadow-blue-600/30 hover:shadow-blue-600/50 hover:-translate-y-0.5">
                            <i class="bi bi-speedometer2"></i> Dashboard Admin
                        </a>

                        {{-- CEK 2: APAKAH YANG LOGIN ITU PESERTA MAGANG? --}}
                    @elseif(Auth::guard('magang')->check())
                        <div class="flex items-center gap-8">

                            {{-- Menu Navigasi --}}
                            <ul class="flex gap-6 items-center font-semibold text-sm text-[#37517e]">
                                <li>
                                    <a href="{{ route('dashboard.index') }}"
                                        class="nav-slide-link transition-colors duration-200 hover:text-blue-600 {{ request()->is('/') ? 'text-blue-600 active' : '' }}">
                                        Home
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ request()->is('/') ? '#aboutus' : '/#aboutus' }}"
                                        class="nav-slide-link transition-colors duration-200 hover:text-blue-600">
                                        Tentang Kami
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('status') }}"
                                        class="nav-slide-link transition-colors duration-200 hover:text-blue-600 {{ request()->is('status*') ? 'text-blue-600 active' : '' }}">
                                        Status
                                    </a>
                                </li>
                                <li>
                                    <a href="/sertifikat"
                                        class="nav-slide-link transition-colors duration-200 hover:text-blue-600 {{ request()->is('sertifikat') ? 'text-blue-600 active' : '' }}">
                                        Sertifikat
                                    </a>
                                </li>
                            </ul>

                            {{-- Divider vertikal tipis --}}
                            <div class="w-px h-5 bg-gray-200"></div>

                            {{-- Profile Dropdown --}}
                            <div class="profile-dropdown-group relative">
                                {{-- Trigger Button --}}
                                <div
                                    class="flex items-center gap-2.5 cursor-pointer px-3 py-2 rounded-xl border border-transparent hover:bg-blue-50/70 hover:border-blue-100 transition-all duration-200">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center border-2 border-blue-100 group-hover:border-blue-500 transition-all duration-200">
                                        <i class="bi bi-person-fill text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[13px] font-bold text-gray-800 leading-tight">
                                            {{ Auth::guard('magang')->user()->nama_peserta }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">Peserta Magang
                                        </p>
                                    </div>
                                    <i
                                        class="bi bi-chevron-down text-[10px] text-gray-400 transition-transform duration-200"></i>
                                </div>

                                {{-- Dropdown Panel --}}
                                <div class="profile-dropdown-panel absolute right-0 hidden pt-2 w-56 z-50">
                                    <div
                                        class="bg-white/95 backdrop-blur-sm border border-gray-100 rounded-2xl shadow-2xl shadow-gray-200/80 py-1.5 overflow-hidden">
                                        <div class="px-4 py-3 border-b border-gray-50">
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Login
                                                sebagai</p>
                                            <p class="text-sm font-bold text-gray-800 truncate">
                                                {{ Auth::guard('magang')->user()->nama_peserta }}
                                            </p>
                                        </div>
                                        <a href="{{ route('profile.edit') }}"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                            <i class="bi bi-person-vcard text-base"></i> Profil Saya
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors duration-150 text-left font-semibold">
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
                            class="text-blue-600 border border-blue-200 px-5 py-2 rounded-full font-semibold text-sm hover:bg-blue-50 hover:border-blue-400 transition-all duration-200">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                            class="btn-shimmer bg-blue-600 text-white px-5 py-2 rounded-full font-bold text-sm hover:bg-blue-700 transition-all duration-200 shadow-md shadow-blue-600/30 hover:shadow-blue-600/50 hover:-translate-y-0.5">
                            Daftar Sekarang
                        </a>
                    @endif

                </div>

                {{-- Mobile Hamburger Button --}}
                <button id="mobile-menu-btn"
                    class="md:hidden flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                    <i class="bi bi-list text-2xl text-gray-700" id="hamburger-icon"></i>
                </button>

            </div>
        </div>

        {{-- ===================== MOBILE MENU ===================== --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100/80 bg-white/95 backdrop-blur-xl">
            <div class="px-4 py-4 space-y-1">

                @if (Auth::guard('web')->check())
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-sm">
                        <i class="bi bi-speedometer2"></i> Dashboard Admin
                    </a>
                @elseif(Auth::guard('magang')->check())
                    {{-- User Info Card --}}
                    <div class="flex items-center gap-3 px-3 py-3 mb-3 rounded-2xl border border-blue-100"
                        style="background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);">
                        <div
                            class="w-10 h-10 rounded-full bg-white flex items-center justify-center border-2 border-blue-200 shrink-0 shadow-sm">
                            <i class="bi bi-person-fill text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">
                                {{ Auth::guard('magang')->user()->nama_peserta }}
                            </p>
                            <p class="text-xs text-blue-500 font-medium uppercase tracking-wide">Peserta Magang</p>
                        </div>
                    </div>

                    <a href="{{ route('dashboard.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">
                        <i class="bi bi-house text-blue-500"></i> Home
                    </a>
                    <a href="{{ request()->is('/') ? '#aboutus' : '/#aboutus' }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">
                        <i class="bi bi-info-circle text-blue-500"></i> Tentang Kami
                    </a>
                    <a href="{{ route('status') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">
                        <i class="bi bi-card-checklist text-blue-500"></i> Status
                    </a>
                    <a href="/sertifikat"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">
                        <i class="bi bi-award text-blue-500"></i> Sertifikat
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">
                        <i class="bi bi-person-vcard text-blue-500"></i> Profil Saya
                    </a>
                    <div class="border-t border-gray-100 pt-2 mt-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 text-sm font-semibold transition-colors">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="block text-center px-4 py-2.5 rounded-xl border border-blue-200 text-blue-600 font-semibold text-sm hover:bg-blue-50 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                        class="block text-center px-4 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition-colors mt-1">
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
        <footer id="footer" class="text-white pt-14 pb-8 text-sm relative overflow-hidden"
            style="background-color: #060d1f;">

            {{-- Top gradient accent line --}}
            <div class="absolute top-0 left-0 right-0 h-px"
                style="background: linear-gradient(90deg, transparent, #2563eb 30%, #6366f1 60%, transparent);"></div>

            {{-- Background ambient glow --}}
            <div class="absolute top-0 right-0 w-96 h-96 pointer-events-none opacity-100"
                style="background: radial-gradient(circle, rgba(37,99,235,0.05) 0%, transparent 70%); transform: translate(20%, -30%);">
            </div>
            <div class="absolute bottom-0 left-0 w-64 h-64 pointer-events-none"
                style="background: radial-gradient(circle, rgba(99,102,241,0.04) 0%, transparent 70%); transform: translate(-20%, 30%);">
            </div>

            <div class="container mx-auto px-4 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

                    {{-- Kolom 1: Identitas --}}
                    <div>
                        <a href="/" class="flex items-center gap-2.5 mb-4 w-fit">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0"
                                style="box-shadow: 0 0 16px rgba(37,99,235,0.45);">
                                <i class="bi bi-briefcase-fill text-white text-sm"></i>
                            </div>
                            <span class="text-lg font-extrabold text-white tracking-tight">SINAKERTRANS</span>
                        </a>
                        <p class="text-xs leading-relaxed mb-5" style="color: #475569;">
                            Sistem Manajemen Magang — Dinas Tenaga Kerja & Transmigrasi Prov. Jawa Timur
                        </p>
                        <div class="space-y-1.5" style="color: #475569;">
                            <p class="text-xs">Jln. Dukuh Menanggal 124 - 126, Gayungan</p>
                            <p class="text-xs">Surabaya 60234, Jawa Timur</p>
                            <p class="text-xs mt-3">
                                <span style="color: #64748b;">Phone:</span> (031) 8290005
                            </p>
                            <p class="text-xs">
                                <span style="color: #64748b;">Email:</span> disnakertrans@jatimprov.go.id
                            </p>
                        </div>
                        <div class="flex gap-2 mt-5">
                            <a href="#"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5"
                                style="border: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.04); color: #475569;"
                                onmouseover="this.style.borderColor='rgba(37,99,235,0.4)'; this.style.background='rgba(37,99,235,0.12)'; this.style.color='#60a5fa';"
                                onmouseout="this.style.borderColor='rgba(255,255,255,0.07)'; this.style.background='rgba(255,255,255,0.04)'; this.style.color='#475569';">
                                <i class="bi bi-twitter text-sm"></i>
                            </a>
                            <a href="#"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5"
                                style="border: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.04); color: #475569;"
                                onmouseover="this.style.borderColor='rgba(37,99,235,0.4)'; this.style.background='rgba(37,99,235,0.12)'; this.style.color='#60a5fa';"
                                onmouseout="this.style.borderColor='rgba(255,255,255,0.07)'; this.style.background='rgba(255,255,255,0.04)'; this.style.color='#475569';">
                                <i class="bi bi-facebook text-sm"></i>
                            </a>
                            <a href="#"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5"
                                style="border: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.04); color: #475569;"
                                onmouseover="this.style.borderColor='rgba(37,99,235,0.4)'; this.style.background='rgba(37,99,235,0.12)'; this.style.color='#60a5fa';"
                                onmouseout="this.style.borderColor='rgba(255,255,255,0.07)'; this.style.background='rgba(255,255,255,0.04)'; this.style.color='#475569';">
                                <i class="bi bi-instagram text-sm"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Kolom 2: Useful Links --}}
                    <div>
                        <h4 class="text-[10px] font-bold uppercase tracking-widest mb-4 pb-3"
                            style="color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            Useful Links
                        </h4>
                        <ul class="space-y-2.5">
                            <li><a href="#" class="text-xs transition-colors duration-150 hover:text-blue-400"
                                    style="color: #475569;">Home</a></li>
                            <li><a href="#about" class="text-xs transition-colors duration-150 hover:text-blue-400"
                                    style="color: #475569;">Tentang Kami</a></li>
                            <li><a href="#services" class="text-xs transition-colors duration-150 hover:text-blue-400"
                                    style="color: #475569;">Layanan</a></li>
                            <li><a href="#" class="text-xs transition-colors duration-150 hover:text-blue-400"
                                    style="color: #475569;">Terms of Service</a></li>
                            <li><a href="#" class="text-xs transition-colors duration-150 hover:text-blue-400"
                                    style="color: #475569;">Privacy Policy</a></li>
                        </ul>
                    </div>

                    {{-- Kolom 3: Layanan Kami --}}
                    <div>
                        <h4 class="text-[10px] font-bold uppercase tracking-widest mb-4 pb-3"
                            style="color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            Layanan Kami
                        </h4>
                        <ul class="space-y-2.5">
                            <li><a href="#" class="text-xs transition-colors duration-150 hover:text-blue-400"
                                    style="color: #475569;">Usulan Kepegawaian</a></li>
                            <li><a href="#" class="text-xs transition-colors duration-150 hover:text-blue-400"
                                    style="color: #475569;">Dokumen Digital</a></li>
                            <li><a href="#" class="text-xs transition-colors duration-150 hover:text-blue-400"
                                    style="color: #475569;">Simulasi Kredit</a></li>
                            <li><a href="#" class="text-xs transition-colors duration-150 hover:text-blue-400"
                                    style="color: #475569;">Arsip & Surat</a></li>
                        </ul>
                    </div>

                    {{-- Kolom 4: Newsletter --}}
                    <div>
                        <h4 class="text-[10px] font-bold uppercase tracking-widest mb-4 pb-3"
                            style="color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            Newsletter
                        </h4>
                        <p class="text-xs leading-relaxed mb-4" style="color: #475569;">
                            Berlangganan untuk mendapatkan informasi terbaru seputar kepegawaian.
                        </p>
                        <form action="#" class="flex rounded-xl overflow-hidden transition-all duration-200"
                            style="border: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.03);">
                            <input type="email" placeholder="Email Anda"
                                class="flex-1 px-3 py-2.5 bg-transparent border-none outline-none text-white text-xs placeholder-gray-600 font-medium">
                            <button type="submit"
                                class="bg-blue-600 px-4 py-2.5 text-white text-xs font-bold hover:bg-blue-500 transition-colors duration-150 shrink-0">
                                Sub
                            </button>
                        </form>
                    </div>

                </div>

                {{-- Footer Bottom --}}
                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3"
                    style="border-top: 1px solid rgba(255,255,255,0.04);">
                    <p class="text-xs" style="color: #334155;">
                        &copy; {{ date('Y') }} <strong style="color: #475569;">SINAKERTRANS</strong> — Dinas
                        Tenaga Kerja dan Transmigrasi Jawa Timur
                    </p>
                    <span class="text-[10px] px-3 py-1 rounded-md font-medium"
                        style="color: #334155; border: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
                        Sistem Manajemen Magang
                    </span>
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
    @stack('style')

</body>

</html>
