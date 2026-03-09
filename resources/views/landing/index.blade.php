<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SINAKERTRANS - Cari Magang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="bg-gray-50 font-sans text-gray-800">

    @include('layouts.layoutlanding')
    

    <!-- hero seciton -->
<div class="relative w-full h-80" id="home">
    <div class="absolute inset-0 opacity-70">
        <img src="https://image1.jdomni.in/banner/13062021/0A/52/CC/1AF5FC422867D96E06C4B7BD69_1623557926542.png" alt="Background Image" class="object-cover object-center w-full h-full" />

    </div>
    <div class="absolute inset-9 flex flex-col md:flex-row items-center justify-between">
        <div class="md:w-1/2 mb-4 md:mb-0">
            <h1 class="text-grey-700 font-medium text-3xl md:text-4xl leading-tight mb-2">Temukan Tempat Magang Impianmu</h1>
            <p class="font-regular text-xl mb-8 mt-4">Bergabunglah dengan Dinas Tenaga Kerja untuk pengalaman magang yang nyata</p>
            
        </div>
    </div>
</div>


    <!-- our services section -->
<section class="py-10" id="services">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Program Magang</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="https://image3.jdomni.in/banner/13062021/42/5C/B1/45AC18B7F8EE562BC3DDB95D34_1623559815667.png?output-format=webp" alt="wheat flour grinding"
                    class="w-full h-64 object-cover">
                <div class="p-6 text-center">
                    <h3 class="text-xl font-medium text-gray-800 mb-2">Wheat Flour Grinding</h3>
                    <p class="text-gray-700 text-base">Our wheat flour grinding service provides fresh, high-quality
                        flour to businesses and individuals in the area. We use state-of-the-art equipment to grind
                        wheat into flour, and we offer a variety of flours to meet the needs of our customers.</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="https://images.unsplash.com/photo-1606854428728-5fe3eea23475?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8Z3JhbSUyMGZsb3VyfGVufDB8fDB8fHww" alt="Coffee"
                    class="w-full h-64 object-cover">
                <div class="p-6 text-center">
                    <h3 class="text-xl font-medium text-gray-800 mb-2">Gram Flour Grinding</h3>
                    <p class="text-gray-700 text-base">Our gram flour is perfect for a variety of uses, including
                        baking, cooking, and making snacks. It is also a good source of protein and fiber.Our gram flour
                        grinding service is a convenient and affordable way to get the freshest gram flour possible.</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="https://image2.jdomni.in/banner/13062021/D2/99/0D/48D7F4AFC48C041DC8D80432E9_1623562146900.png?output-format=webp" alt="Coffee"
                    class="w-full h-64 object-cover">
                <div class="p-6 text-center">
                    <h3 class="text-xl font-medium text-gray-800 mb-2">Jowar Flour Grinding</h3>
                    <p class="text-gray-700 text-base">Our jowar grinding service is a convenient and affordable way to
                        get fresh, high-quality jowar flour. We use state-of-the-art equipment to grind jowar into a
                        fine powder, which is perfect for making roti, bread, and other dishes.
                    <details>
                        <summary>Read More</summary>
                        <p>Our jowar flour is also
                            a good source of protein and fiber, making it a healthy choice for your family.</p>
                    </details>
                    </p>

                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="https://images.unsplash.com/photo-1607672632458-9eb56696346b?q=80&w=1914&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Coffee"
                    class="w-full h-64 object-cover">
                <div class="p-6 text-center">
                    <h3 class="text-xl font-medium text-gray-800 mb-2">Chilli pounding</h3>
                    <p class="text-gray-700 text-base">We specializes in the production of high-quality chili powder.
                        Our chili powder is made from the finest, freshest chilies, and we use traditional pounding
                        methods to ensure that our chili powder retains its full flavor and aroma.
                    <details>
                        <summary>Read More</summary>
                        <p> We offer a variety of chili powder products, including mild, medium, and hot. We also offer
                            custom blends to meet the specific needs of our customers.</p>
                    </details>
                    </p>
                </div>
            </div>
            <!-- special card -->
            <div
                class="bg-white rounded-lg bg-linear-to-tr from-pink-300 to-blue-300 p-0.5 shadow-lg overflow-hidden min-h-full">
                <div class="text-center text-white font-medium">Special product</div>
                <img src="https://images.unsplash.com/photo-1556910110-a5a63dfd393c?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8cmF3JTIwc3BhZ2hldHRpfGVufDB8fDB8fHww" alt="Coffee"
                    class="w-full h-64 object-cover rounded-t-lg">
                <div class="p-6 bg-white text-center rounded-b-lg md:min-h-full">
                    <h3 class="text-xl font-medium text-gray-800 mb-2">Flavoured Spaghetti</h3>
                    <p class="text-gray-700 text-base"><span class="font-medium underline">Our speciality is</span>
                        Bappa Flour Mill offers a variety of flavored spaghetti dishes that are sure to tantalize your
                        taste
                        buds. We use only the freshest ingredients Our
                        flavors include: Mango, spinach
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="https://media.istockphoto.com/id/1265641298/photo/fried-papad.jpg?s=612x612&w=0&k=20&c=e_iEy4CTvU6Thn02zGgKt_TiSYAheCKmgfTF5j52ovU=" alt="papad"
                    class="w-full h-64 object-cover">
                <div class="p-6 text-center">
                    <h3 class="text-xl font-medium text-gray-800 mb-2">Rice Papad</h3>
                    <p class="text-gray-700 text-base">Our company produces high-quality rice papad that is made with
                        the finest ingredients. We use traditional methods to make our papad, which gives it a unique
                        flavor and texture. Our papad is also gluten-free and vegan.
                    <details>
                        <summary>Read More</summary>
                        <p> We offer a variety of rice papad flavors, including plain, salted, spicy, and flavored. We
                            also
                            offer a variety of sizes and shapes to choose from. Our papad is available in bulk or in
                            individual packages.</p>
                    </details>
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- about us -->
<section class="bg-gray-100" id="aboutus">
    <div class="container mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-8">
            <div class="max-w-lg">
                <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Tentang Kami</h2>
                <p class="mt-4 text-gray-600 text-lg">
                    Dinas Tenaga Kerja dan Provinsi Jawa Timur berfokus pada peningkatan kompetensi tenaga kerja, pengembangan lapangan kerja, 
                    perlindungan hak tenaga kerja, dan pengelolaan transmigrasi berkelanjutan untuk kesejahteraan masyarakat.</p>
            </div>
            <div class="mt-12 md:mt-0">
                <img src="https://kilasjatim.com/wp-content/uploads/2025/04/100-e1744800110750.webp" alt="About Us Image" class="object-cover rounded-lg shadow-md">
            </div>
        </div>
    </div>
</section>

<!-- why us  -->
<section class="text-gray-700 body-font mt-10">
    <div class="flex justify-center text-3xl font-bold text-gray-800 text-center">
        Why Us?
    </div>
    <div class="container px-5 py-12 mx-auto">
        <div class="flex flex-wrap text-center justify-center">
            <div class="p-4 md:w-1/4 sm:w-1/2">
                <div class="px-4 py-6 transform transition duration-500 hover:scale-110">
                    <div class="flex justify-center">
                        <img src="https://image3.jdomni.in/banner/13062021/58/97/7C/E53960D1295621EFCB5B13F335_1623567851299.png?output-format=webp" class="w-32 mb-3">
                    </div>
                    <h2 class="title-font font-regular text-2xl text-gray-900">Latest Milling Machinery</h2>
                </div>
            </div>

            <div class="p-4 md:w-1/4 sm:w-1/2">
                <div class="px-4 py-6 transform transition duration-500 hover:scale-110">
                    <div class="flex justify-center">
                        <img src="https://image2.jdomni.in/banner/13062021/3E/57/E8/1D6E23DD7E12571705CAC761E7_1623567977295.png?output-format=webp" class="w-32 mb-3">
                    </div>
                    <h2 class="title-font font-regular text-2xl text-gray-900">Reasonable Rates</h2>
                </div>
            </div>

            <div class="p-4 md:w-1/4 sm:w-1/2">
                <div class="px-4 py-6 transform transition duration-500 hover:scale-110">
                    <div class="flex justify-center">
                        <img src="https://image3.jdomni.in/banner/13062021/16/7E/7E/5A9920439E52EF309F27B43EEB_1623568010437.png?output-format=webp" class="w-32 mb-3">
                    </div>
                    <h2 class="title-font font-regular text-2xl text-gray-900">Time Efficiency</h2>
                </div>
            </div>

            <div class="p-4 md:w-1/4 sm:w-1/2">
                <div class="px-4 py-6 transform transition duration-500 hover:scale-110">
                    <div class="flex justify-center">
                        <img src="https://image3.jdomni.in/banner/13062021/EB/99/EE/8B46027500E987A5142ECC1CE1_1623567959360.png?output-format=webp" class="w-32 mb-3">
                    </div>
                    <h2 class="title-font font-regular text-2xl text-gray-900">Expertise in Industry</h2>
                </div>
            </div>

        </div>
    </div>
</section>

<div class="max-w-xl mx-auto px-4">
            <form action="{{ route('landing.index') }}" method="GET"
                class="flex gap-2 bg-white p-2 rounded-lg shadow-lg">
                <input type="text" name="search" placeholder="Cari posisi magang..."
                    class="flex-1 px-4 py-2 text-gray-800 outline-none rounded-md">
                <button type="submit"
                    class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-2 rounded-md font-semibold transition">
                    Cari
                </button>
            </form>
        </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-4 border-blue-600 pl-4">Lowongan Terbaru</h2>

        @if ($vacancies->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($vacancies as $job)
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $job->title }}</h3>
                                    <span class="text-sm text-blue-600 font-semibold bg-blue-50 px-2 py-1 rounded">
                                        Divisi {{ $job->division_name }}
                                    </span>
                                </div>
                                <span
                                    class="text-xs font-bold px-2 py-1 rounded {{ $job->type == 'magang' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ strtoupper($job->type) }}
                                </span>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ Str::limit($job->description, 100) }}
                            </p>

                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-6">
                                <span class="flex items-center gap-1"><i class="bi bi-people"></i> Kuota:
                                    {{ $job->quota_slots }}</span>
                                <span class="flex items-center gap-1"><i class="bi bi-calendar"></i>
                                    {{ \Carbon\Carbon::parse($job->start_date)->format('d M') }} -
                                    {{ \Carbon\Carbon::parse($job->end_date)->format('d M Y') }}
                                </span>
                            </div>

                            <a href="{{ route('landing.show', $job->id) }}"
                                class="block w-full text-center bg-gray-900 hover:bg-gray-800 text-white py-2 rounded-lg font-medium transition">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $vacancies->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                <i class="bi bi-emoji-frown text-4xl text-gray-400 mb-3 block"></i>
                <h3 class="text-lg font-medium text-gray-900">Belum ada lowongan dibuka</h3>
                <p class="text-gray-500">Silahkan kembali lagi nanti untuk info terbaru.</p>
            </div>
        @endif
    </div>


    <section class="bg-gray-100 py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

            <!-- kiri -->
            <div class="max-w-lg">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">
                    Timeline Magang
                </h2>

                <p class="text-gray-600 text-lg leading-relaxed">
                    Berikut jadwal kegiatan program magang.  
                    Peserta dapat melihat periode aktif, 
                    tanggal mulai hingga selesai, dan aktivitas penting.
                </p>

                <div class="mt-4 text-sm text-gray-500">
                    • Blok warna menunjukkan periode magang  
                    • Klik event untuk detail (admin)  
                </div>
            </div>

            <!-- kanan kalender -->
            <div class="bg-white rounded-xl shadow-md p-4">
                @include('calendar')
            </div>

        </div>
    </div>
</section>

<!-- gallery -->
<section class="text-gray-700 body-font" id="gallery">
    <div class="flex justify-center text-3xl font-bold text-gray-800 text-center py-10">
        Dokumentasi Magang
    </div>

    <div class="grid grid-cols-1 place-items-center sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4">

        <div class="group relative">
            <img
      src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-1.jpeg"
      alt="Image 1"
      class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105"
    />
        </div>

        <div class="group relative">
            <img
      src="https://www.suarasurabaya.net/wp-content/uploads/2023/01/Kegiatan-Uji-Kompetensi-Kejuruan-HMI-Berbasis-PLC-yang-dilakukan-UPT-Balai-Latihan-Kerja-Surabaya-735x493.jpg.webp"
      alt="Image 1"
      class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105"
    />
        </div>

        <div class="group relative">
            <img
      src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-2.jpeg"
      alt="Image 1"
      class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105"
    />
        </div>
        <div class="group relative">
            <img
      src="https://sinaker.disnakertrans.jatimprov.go.id/assets/landing/img/galeri/tik-3.jpeg"
      alt="Image 1"
      class="aspect-2/3 h-80 object-cover rounded-lg transition-transform transform scale-100 group-hover:scale-105"
    />
        </div>

        
        <!-- Repeat this div for each image -->
    </div>

</section>
@include('layouts.footer')
</body>

</html>
