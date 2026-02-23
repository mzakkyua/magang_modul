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

                        {{-- CEK 2: APAKAH YANG LOGIN ITU ANAK MAGANG? --}}
@elseif(Auth::guard('magang')->check())
    <div class="flex items-center gap-6">
        
        {{-- Sapaan User --}}
        <div class="flex items-center gap-2 text-gray-600">
            <i class="bi bi-person-circle text-lg text-blue-600"></i>
            <span class="text-sm">Halo, <strong>{{ Auth::guard('magang')->user()->nama_peserta }}</strong></span>
        </div>

        {{-- Menu Navigasi --}}
        <ul class="flex gap-6 items-center font-semibold text-xs uppercase tracking-widest text-[#37517e]">
            <li><a href="/" class="hover:text-blue-600 transition">Home</a></li>
            <li><a href="#about" class="hover:text-blue-600 transition">Tentang Kami</a></li>
            <li><a href="#services" class="hover:text-blue-600 transition">Status</a></li>
            <li><a href="#statistik" class="hover:text-blue-600 transition">Sertifikat</a></li>
            <li><a href="{{ route('profile.edit') }}" class="hover:text-blue-600 transition">Profil</a></li>
        </ul>

        {{-- Tombol Logout Baru --}}
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" 
                class="px-4 py-1.5 border border-red-200 text-red-500 text-[10px] font-bold uppercase tracking-tighter rounded-md hover:bg-red-50 hover:text-red-700 hover:border-red-500 transition-all duration-200 shadow-sm">
                <i class="bi bi-box-arrow-right mr-1"></i> Logout
            </button>
        </form>
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


