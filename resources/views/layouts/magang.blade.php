<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
<body>
    <header id="header" class="fixed top-0 w-full z-20 transition-all duration-300 bg-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            
            <a href="/" class="flex items-center gap-3 group">
                <images src="{{ asset('') }}" alt="Logo" class="max-h-14 group-hover:scale-105 transition-transform">
                </a>

            <nav class="hidden lg:flex items-center gap-8">
                <ul class="flex gap-6 items-center font-semibold text-sm uppercase tracking-wide text-[#37517e]">
                    <li><a href="/" class="hover:text-blue-500 transition">Home</a></li>
                    <li><a href="#about" class="hover:text-blue-500 transition">Tentang Kami</a></li>
                    <li><a href="#services" class="hover:text-blue-500 transition">Layanan</a></li>
                    <li><a href="#statistik" class="hover:text-blue-500 transition">Statistik</a></li>
                    <li><a href="#team" class="hover:text-blue-500 transition">Team</a></li>
                </ul>
                <a href="/login" class="px-5 py-2 bg-[#37517e] text-white rounded-full text-sm font-medium hover:bg-blue-600 transition shadow-lg transform hover:-translate-y-0.5">
                    Login
                </a>
            </nav>

            <button id="mobile-menu-btn" class="lg:hidden text-[#37517e] focus:outline-none">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t absolute w-full left-0 shadow-lg">
            <ul class="flex flex-col p-4 gap-2 font-medium text-[#37517e]">
                <li><a href="/" class="block px-4 py-2 hover:bg-gray-50 rounded">Home</a></li>
                <li><a href="#about" class="block px-4 py-2 hover:bg-gray-50 rounded">Tentang Kami</a></li>
                <li><a href="#services" class="block px-4 py-2 hover:bg-gray-50 rounded">Layanan</a></li>
                <li><a href="#statistik" class="block px-4 py-2 hover:bg-gray-50 rounded">Statistik</a></li>
                <li><a href="#team" class="block px-4 py-2 hover:bg-gray-50 rounded">Team</a></li>
                <li><a href="/login" class="block px-4 py-2 bg-[#37517e] text-white rounded text-center mt-2">Login</a></li>
            </ul>
        </div>
    </header>

     <main class="flex-grow mt-16">
        @yield('content')
    </main>


    <footer id="footer" class="bg-slate-900 text-white pt-16 pb-8 text-sm">
        
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
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[#37517e] transition duration-300 border border-slate-700 hover:border-blue-400">
                            <i class="fa-brands fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[#37517e] transition duration-300 border border-slate-700 hover:border-blue-400">
                            <i class="fa-brands fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[#37517e] transition duration-300 border border-slate-700 hover:border-blue-400">
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
                    <p class="text-gray-400 mb-4">Berlangganan untuk mendapatkan informasi terbaru seputar kepegawaian.</p>
                    <form action="#" class="flex">
                        <input type="email" placeholder="Email Anda" class="w-full px-4 py-2 rounded-l-md bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-blue-500">
                        <button type="submit" class="bg-blue-600 px-4 py-2 rounded-r-md hover:bg-blue-700 transition">Sub</button>
                    </form>
                </div>

            </div>
            
            <div class="border-t border-slate-800 pt-8 text-center text-gray-400">
                <p>&copy; Copyright <strong>KEPEGAWAIAN</strong> 2025. All Rights Reserved</p>
                <div class="text-xs mt-2">
                    Designed by <span class="text-white">BootstrapMade</span> (Re-coded with Laravel & Tailwind)
                </div>
            </div>
        </div>
    </footer>
</body>
</html>