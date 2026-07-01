@props([
    'path' => asset('assets/docs/guidebook-user.pdf'),
    'title' => 'Baca Panduan Sistem',
])

<div class="fixed bottom-6 right-6 z-50 group flex items-center gap-3 select-none">

    {{-- BALON TEKS (Tooltip) --}}
    <span
        class="bg-gray-900 text-white text-[11px] font-extrabold px-3 py-2 rounded-xl 
                 opacity-0 translate-x-3 pointer-events-none 
                 group-hover:opacity-100 group-hover:translate-x-0 
                 transition-all duration-300 shadow-lg shadow-gray-900/10 tracking-wide uppercase">
        {{ $title }}
    </span>

    {{-- TOMBOL BULAT MELAYANG (Menggunakan fungsi JS untuk cek ombak) --}}
    <button onclick="bukaFileAman('{{ $path }}')"
        class="w-13 h-13 bg-blue-600 hover:bg-blue-700 text-white rounded-full 
              flex items-center justify-center transition-all duration-300
              shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 
              hover:scale-110 active:scale-95 relative group/btn focus:outline-none">

        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-25"></span>
        <i class="bi bi-book-half text-lg relative z-10 transition-transform group-hover/btn:rotate-12"></i>
    </button>
</div>

<script>
    function bukaFileAman(url) {
        // Melakukan HEAD request kilat untuk cek kondisi file di server
        fetch(url, {
                method: 'HEAD'
            })
            .then(response => {
                // Jika server merespon 404 atau error lainnya
                if (!response.ok) {
                    throw new Error('File tidak ditemukan');
                }

                // Deteksi file corrupt (jika ukuran file di bawah 1 KB / kosong)
                const size = response.headers.get('Content-Length');
                if (size && parseInt(size) < 1024) {
                    throw new Error('File corrupt');
                }

                // JIKA AMAN: Buka di tab baru
                window.open(url, '_blank');
            })
            .catch(error => {
                // JIKA RUSAK/HILANG: Munculkan peringatan rapi, bukan halaman 404 kosong
                alert(
                    '⚠️ GAGAL MEMBUKA PANDUAN\n\nFile dokumen tidak ditemukan atau mengalami kerusakan (corrupt) di server.\nSilakan hubungi Tim Admin.');
            });
    }
</script>
