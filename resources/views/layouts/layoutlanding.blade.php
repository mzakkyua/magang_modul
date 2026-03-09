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
                        <ul class="flex gap-8 items-center font-bold text-sm uppercase tracking-wider text-[#37517e]">
                            <li>
                                <a href="/"
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
                                    <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Peserta Magang</p>
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
    </div>
</nav>
