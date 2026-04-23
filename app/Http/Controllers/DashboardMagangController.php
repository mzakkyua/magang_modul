<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VacancyMagang;
use App\Models\ApplicationMemberMagang;

class DashboardMagangController extends Controller
{

    /**
     * ============================================================
     * DASHBOARD PESERTA MAGANG
     * ============================================================
     *
     * Controller ini bertanggung jawab untuk menampilkan
     * dashboard utama peserta magang setelah login.
     *
     * Data yang ditampilkan pada dashboard:
     *
     * 1. Daftar lowongan MAGANG yang sedang open
     * 2. Daftar lowongan PENELITIAN yang sedang open
     * 3. Status apakah user adalah siswa SMK
     * 4. Hasil pencarian lowongan berdasarkan keyword
     *
     * Sistem menggunakan guard authentication:
     *
     * guard : magang
     *
     * sehingga user yang diambil adalah peserta magang
     * (mahasiswa, siswa SMK, peneliti, dll)
     *
     * ============================================================
     */

    /**
     * ============================================================
     * MENU NILAI
     * ============================================================
     * Menampilkan halaman nilai magang peserta yang sedang login.
     *
     * Relasi yang dipakai:
     * UserMagang → applicationMembers → application (accepted) → assessment
     */
    public function nilai()
    {
        $userId = Auth::guard('magang')->id();

        // STEP: Cari member record peserta yang lamarannya accepted/completed
        // Eager load assessment dan vacancy sekaligus agar tidak N+1
        $member = ApplicationMemberMagang::where('user_id', $userId)
            ->whereHas('application', function ($q) {
                $q->whereIn('status', ['accepted', 'completed']);
            })
            ->with([
                'assessment',
                'application.vacancy',
            ])
            ->first();

        // Mengarah ke folder resources/views/nilai/index.blade.php
        return view('magang.nilai.index', compact('member'));
    }

    public function index(Request $request)
    {

        /**
         * ============================================================
         * SECTION 1
         * USER LOGIN
         * ============================================================
         *
         * Mengambil user yang sedang login menggunakan guard magang.
         *
         * Guard magang digunakan untuk:
         * - mahasiswa
         * - siswa SMK
         * - peneliti
         * - peserta magang lainnya
         *
         * Middleware yang seharusnya digunakan pada route:
         *
         * auth:magang
         *
         * Sehingga dashboard hanya bisa diakses oleh user yang login.
         */

        $user = Auth::guard('magang')->user();


        /**
         * ============================================================
         * STEP 1
         * CEK STATUS SISWA SMK
         * ============================================================
         *
         * Dashboard memiliki beberapa perbedaan tampilan
         * khusus untuk siswa SMK.
         *
         * Oleh karena itu sistem perlu mengecek apakah user
         * memiliki education_level = siswa_smk.
         *
         * optional() digunakan untuk mencegah error jika
         * profile belum ada.
         */

        $isSMK = optional($user->profile)->education_level === 'siswa_smk';



        /**
         * ============================================================
         * SECTION 2
         * SEARCH INPUT
         * ============================================================
         *
         * Mengambil input pencarian dari user.
         *
         * Parameter:
         * ?search=keyword
         *
         * trim() digunakan untuk menghapus spasi di awal
         * dan akhir input agar query lebih akurat.
         *
         * Contoh:
         *
         * "   magang   "
         *
         * menjadi:
         *
         * "magang"
         */

        $search = trim($request->search);



        /**
         * ============================================================
         * SECTION 3
         * BASE QUERY LOWONGAN
         * ============================================================
         *
         * Membuat base query untuk vacancy.
         *
         * Tujuan:
         *
         * - Menghindari duplikasi query
         * - Membuat kode lebih maintainable
         * - Mempermudah penambahan filter di masa depan
         *
         * Filter dasar:
         *
         * status = open
         *
         * Artinya hanya lowongan yang masih dibuka
         * yang akan ditampilkan pada dashboard.
         *
         * ============================================================
         */

        $baseQuery = VacancyMagang::query()

            /**
             * ========================================================
             * FILTER STATUS
             * ========================================================
             *
             * Hanya menampilkan lowongan dengan status open.
             *
             * Status yang mungkin ada:
             *
             * open
             * closed
             * archived
             *
             * Dashboard hanya menampilkan lowongan aktif.
             */

            ->where('status', 'open')


            /**
             * ========================================================
             * SEARCH FILTER
             * ========================================================
             *
             * Filter pencarian hanya dijalankan jika
             * user memasukkan keyword search.
             *
             * Laravel method:
             *
             * when(condition, callback)
             *
             * Digunakan untuk menjalankan query secara conditional.
             */

            ->when($search, function ($query) use ($search) {

                /**
                 * ====================================================
                 * SEARCH LOGIC
                 * ====================================================
                 *
                 * Pencarian dilakukan pada beberapa kolom:
                 *
                 * - title
                 * - division_name
                 * - description
                 *
                 * Menggunakan LIKE untuk partial matching.
                 *
                 * Contoh:
                 *
                 * search = "data"
                 *
                 * akan cocok dengan:
                 *
                 * "Data Analyst"
                 * "Big Data Research"
                 * "Data Processing"
                 */

                $query->where(function ($q) use ($search) {

                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('division_name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            });



        /**
         * ============================================================
         * SECTION 4
         * QUERY LOWONGAN MAGANG
         * ============================================================
         *
         * Mengambil semua lowongan dengan type = magang.
         *
         * Data yang ditampilkan:
         *
         * - judul lowongan
         * - divisi
         * - deskripsi
         * - tanggal
         * - kuota
         *
         * Data diurutkan dari yang terbaru menggunakan latest().
         */

        $vacanciesMagang = (clone $baseQuery)

            ->where('type', 'magang')

            /**
             * ========================================================
             * SORTING DATA
             * ========================================================
             *
             * latest() secara default menggunakan
             * kolom created_at DESC.
             *
             * Artinya lowongan terbaru akan muncul di atas.
             */

            ->latest()

            ->get();



        /**
         * ============================================================
         * SECTION 5
         * QUERY LOWONGAN PENELITIAN
         * ============================================================
         *
         * Mengambil semua lowongan dengan type = penelitian.
         *
         * Sistem magang ini juga menerima pendaftaran
         * untuk kegiatan penelitian.
         */

        $vacanciesPenelitian = (clone $baseQuery)

            ->where('type', 'penelitian')

            ->latest()

            ->get();



        /**
         * ============================================================
         * SECTION 6
         * RETURN VIEW
         * ============================================================
         *
         * Mengirim data ke view dashboard peserta magang.
         *
         * View:
         *
         * resources/views/magang/dashboard/index.blade.php
         *
         * Data yang dikirim ke view:
         *
         * vacanciesMagang
         * vacanciesPenelitian
         * isSMK
         * search
         */

        return view('magang.dashboard.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'isSMK',
            'search'
        ));
    }

    public function show($id)
    {
        $vacancy = VacancyMagang::where('id', $id)
            ->where('status', 'open')
            ->firstOrFail();

        // Mengarah ke view gabungan yang ada di folder landing
        return view('landing.show', compact('vacancy'));
    }
}
