# 🖼️ Panduan Pengelolaan Gambar & Aset Visual (SINAKERTRANS)

Sistem _front-end_ aplikasi SINAKERTRANS dirancang agar **sangat fleksibel dan pintar**. Penggantian gambar statis, logo, penambahan foto divisi, hingga penambahan galeri dapat dilakukan secara langsung di level file folder `public` **tanpa perlu merombak struktur HTML/Blade**.

Sistem ini sudah dilengkapi dengan fitur **Auto Cache-Busting**. Artinya, setiap kali file gambar ditimpa dengan foto baru, sistem akan memaksa browser pengunjung untuk langsung memuat gambar baru tanpa harus menghapus _cache_ (riwayat browser).

---

## 📂 Struktur Direktori Aset

Seluruh gambar utama yang mengatur antarmuka UI tersimpan di dalam folder `public/assets/images/`. Pastikan struktur foldernya seperti ini:

    public/
    └── assets/
        └── images/
            ├── hero-bg.png           👉 Background utama atas (Landing & Dashboard)
            ├── kantor-disnaker.jpg   👉 Foto gedung di bagian "Tentang Kami" (About)
            ├── favicon.svg           👉 Ikon Tab Browser & Logo di Navbar
            ├── divisions/            👉 Folder khusus foto divisi (Otomatis)
            │   ├── default-1.jpg     (Cadangan 1)
            │   ├── default-2.jpg     (Cadangan 2)
            │   ├── default-3.jpg     (Cadangan 3)
            │   └── default-4.jpg     (Cadangan 4)
            └── gallery/              👉 Folder khusus foto komponen Galeri
                ├── foto-1.jpg
                └── ...

---

## 🛠️ 1. Cara Mengganti Gambar Statis (Background, About, Favicon)

Untuk mengganti gambar statis, kamu **TIDAK PERLU** menyentuh _source code_ (kodingan). Cukup lakukan sistem "Timpa/Replace" di folder.

1. Siapkan file gambar yang baru.
2. Pastikan format dan nama filenya **sama persis (Case-Sensitive)** dengan yang ada di direktori:
    - **Background Atas:** ubah nama foto baru jadi `hero-bg.png`
    - **Foto Tentang Kami:** ubah nama foto baru jadi `kantor-disnaker.jpg`
    - **Logo & Favicon:** ubah nama logo baru jadi `favicon.svg` (Wajib vektor SVG agar tidak pecah).
3. Pindahkan dan timpa (_Replace_) file lama di folder `public/assets/images/` dengan file barumu.
4. Refresh web, gambar akan langsung berubah!

---

## 🏢 2. Cara Mengganti Foto Divisi (Sistem Pintar)

Bagian "Kenali Bidang Kami" membaca data divisi dari Database. Sistem web akan otomatis mencari foto yang namanya sesuai dengan nama divisi tersebut.

- **Cara Menambah Foto Spesifik Divisi:**
  Jika di database ada divisi bernama "Hubungan Industrial", sistem akan otomatis mencari file foto bernama `hubungan-industrial.jpg` (atau `.png` / `.jpeg`) di dalam folder `public/assets/images/divisions/`. (Aturan: Spasi diubah menjadi tanda strip `-`, dan huruf kecil semua).
  👉 _Cukup upload foto dengan nama tersebut ke dalam folder `divisions/`, sistem akan otomatis memajangnya._

- **Sistem Fallback (Foto Cadangan):**
  Jika admin belum menyiapkan foto spesifik untuk sebuah divisi, sistem tidak akan _error_. Sistem akan otomatis mengambil salah satu dari 4 foto cadangan (`default-1.jpg` s/d `default-4.jpg`) secara acak namun konsisten.

---

## 📸 3. Cara Menambah & Mengelola Galeri (Bento Grid)

Sistem galeri diatur agar **Anti-Hancur**.
4 foto pertama akan selalu membentuk kotak _Bento Grid_ asimetris yang cantik. Jika kamu menambahkan foto ke-5, 6, 7, dan seterusnya, sistem akan otomatis membuat barisan baru (3 kolom sejajar) di bawahnya agar _layout_ tetap rapi.

**Langkah Menambah Foto Galeri:**

1. Masukkan foto kegiatan baru ke dalam folder `public/assets/images/gallery/` (misal beri nama: `kegiatan-baru.jpg`).
2. Buka kodingan komponen galeri di: `resources/views/components/gallery-landing.blade.php`.
3. Tambahkan data foto baru tersebut ke dalam _array_ `$photos` di bagian atas kode.

**Contoh Penulisan di Kode:**

```php
// $photos = [
//     // ... (Foto 1 sampai 4 yang sudah ada) ...

//     // Tambahkan foto ke-5 di sini:
//     [
//         'image' => 'assets/images/gallery/kegiatan-baru.jpg', // Path ke foto baru
//         'fallback' => '', // Kosongkan saja jika sudah pakai file lokal
//         'title' => 'Rapat Koordinasi Evaluasi', // Judul (Teks Tebal)
//         'subtitle' => 'Aula Disnakertrans Jatim' // Subjudul (Teks Kecil)
//     ],
// ];

Simpan file tersebut, dan kotak galeri akan otomatis bertambah ke bawah!

⚠️ Troubleshooting (Solusi Jika Terjadi Error)
Gambar Tidak Muncul / Rusak: Pastikan nama file menggunakan huruf kecil semua. Server sangat sensitif! Kantor.jpg akan dianggap berbeda dengan kantor.jpg.

Jangan Asal Rename Ekstensi: Mengubah nama file dari .png menjadi .jpg hanya lewat teks (tanpa convert gambar aslinya) bisa membuat gambar rusak di beberapa browser.

Penyakit Cache: Jika kamu sudah menimpa file di folder tapi gambar di web belum berubah, coba tekan CTRL + F5 (Hard Refresh) di browsermu. Namun dengan sistem ?v= yang kita gunakan, hal ini seharusnya sudah teratasi otomatis.
```
