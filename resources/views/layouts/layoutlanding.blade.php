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
        

        {{-- Menu Navigasi --}}
           <ul class="flex gap-8 items-center font-bold text-sm uppercase tracking-wide text-blue-600">
                <li>
                    <a href="/" class="{{ request()->is('/') ? 'text-blue-600 border-b-2 border-blue-600' : '' }} hover:text-blue-600 transition pb-1"> Home
                    </a>
                </li>
            <li>
                    <a href="/tentang-kami" class="{{ request()->is('tentang-kami') ? 'text-blue-600 border-b-2 border-blue-600' : '' }} hover:text-blue-600 transition pb-1"> Tentang Kami
                    </a>
                </li>
            <li>
                    <a href="/status" class="{{ request()->is('status') ? 'text-blue-600 border-b-2 border-blue-600' : '' }} hover:text-blue-600 transition pb-1"> Status
                    </a>
                </li>
            <li>
                    <a href="/sertifikat" class="{{ request()->is('sertifikat') ? 'text-blue-600 border-b-2 border-blue-600' : '' }} hover:text-blue-600 transition pb-1"> Sertifikat
                    </a>
                </li>
            </ul>

                {{-- Profile Dropdown --}}
        <div class="relative group"> {{-- Tambahkan ml-4 untuk jarak dari menu --}}
            
                {{-- Tombol Pemicu --}}
            <div class="flex items-center gap-3 text-[#37517e] cursor-pointer px-3 py-1.5 rounded-lg transition-all duration-200 hover:bg-gray-50">
                <div class="bg-blue-100 p-1.5 rounded-full flex items-center justify-center shrink-0">
                    <i class="bi bi-person-circle text-lg text-blue-600"></i>
                </div>
                <div class="text-left leading-none shrink-0"> {{-- shrink-0 agar tidak gepeng --}}
                    <p class="text-[13px] font-bold">
                        {{ Auth::guard('magang')->user()->nama_peserta }}
                    </p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter mt-0.5">Peserta Magang</p>
                </div>
                <i class="bi bi-chevron-down text-[10px] transition-transform duration-200 group-hover:rotate-180 ml-1"></i>
            </div>

                {{-- Isi Menu Dropdown --}}
                {{-- w-44 adalah lebar tetap agar tidak stretch --}}
            <div class="absolute right-0 hidden group-hover:block pt-2 w-44 z-50">
                <div class="bg-white border border-gray-100 rounded-xl shadow-xl py-1.5 overflow-hidden">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-[13px] text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                        <i class="bi bi-person-vcard text-base text-gray-400"></i> Profil Saya
                    </a>

                    <div class="border-t border-gray-50 my-1"></div>

                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-[13px] text-red-600 hover:bg-red-50 transition text-left font-semibold">
                            <i class="bi bi-box-arrow-right text-base text-red-400"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>


                        {{-- CEK 3: JIKA BELUM LOGIN (TAMU) --}}
                    @else
                        <a href="{{ route('login') }}"
                            class="text-xl bg-white text-gray-700 px-5 py-2 rounded-3xl font-bold hover:bg-blue-600 hover:text-white transition">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="text-xl bg-white text-gray-700 px-5 py-2 rounded-3xl font-bold hover:bg-blue-600 hover:text-white transition">
                            Daftar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>


