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
                        <span class="text-sm text-gray-600 mr-2">Halo,
                            <strong>{{ Auth::guard('magang')->user()->nama_peserta }}</strong></span>
                            <ul class="flex gap-6 items-center font-semibold text-sm uppercase tracking-wide text-[#37517e]">
                    <li><a href="/" >Home</a></li>
                    <li><a href="#about" >Tentang Kami</a></li>
                    <li><a href="#services" >Status Pengajuan</a></li>
                    <li><a href="#statistik" >Sertifikat</a></li>
                    <li><a href="{{ route('profile.edit')}}">Profil</a></li>
                </ul>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700 ml-2">Logout</button>
                        </form>

                        

                        {{-- CEK 3: JIKA BELUM LOGIN (TAMU) --}}
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 text-white px-5 py-2 rounded-full font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                            Daftar Sekarang
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>


