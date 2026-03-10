<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VacancyMagang;
use App\Models\ApplicationMagang;
use App\Models\UserMagang;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * =====================================================================
 * CONTROLLER: AdminDashboardController
 * =====================================================================
 *
 * TUJUAN UTAMA:
 * Controller ini bertanggung jawab untuk menyediakan seluruh data
 * statistik yang ditampilkan pada halaman Dashboard Admin.
 *
 * Data yang ditampilkan meliputi:
 * - Jumlah lowongan magang aktif
 * - Jumlah pendaftaran yang perlu diverifikasi
 * - Jumlah peserta yang sedang magang
 * - Statistik jumlah siswa SMK
 * - Statistik jumlah mahasiswa
 * - Daftar 5 pendaftaran terbaru
 *
 * ---------------------------------------------------------------------
 * SISTEM ROLE ADMIN
 * ---------------------------------------------------------------------
 * Sistem admin magang memiliki dua jenis role:
 *
 * 1. SUPER ADMIN
 *    - Dapat melihat seluruh data dari semua divisi.
 *
 * 2. ADMIN DIVISI
 *    - Hanya dapat melihat data yang berkaitan dengan divisinya saja.
 *
 * Contoh:
 * Jika admin berasal dari divisi "IT",
 * maka dashboard hanya menampilkan data lowongan dan pendaftaran
 * yang berasal dari divisi IT.
 *
 * ---------------------------------------------------------------------
 * OPTIMISASI PERFORMANCE
 * ---------------------------------------------------------------------
 * Untuk mengurangi beban query database, controller ini menggunakan:
 *
 * - Cache::remember()
 *
 * Sehingga data dashboard tidak selalu di-query ke database setiap
 * halaman dibuka. Cache disimpan selama 1 menit agar data tetap cukup
 * realtime namun tetap efisien.
 *
 * ---------------------------------------------------------------------
 * CATATAN UNTUK DEVELOPER SELANJUTNYA
 * ---------------------------------------------------------------------
 * Beberapa bagian logic di controller ini bisa dipindahkan ke tempat
 * lain jika sistem berkembang:
 *
 * 1. Validasi hak akses admin
 *    → sebaiknya dipindahkan ke Middleware khusus admin magang
 *
 * 2. Query statistik dashboard
 *    → bisa dipindahkan ke Service Layer jika logic semakin kompleks
 *
 * =====================================================================
 */
class AdminDashboardController extends Controller
{

    /**
     * =================================================================
     * METHOD: index()
     * =================================================================
     *
     * Fungsi:
     * Menampilkan halaman Dashboard Admin beserta seluruh data statistik
     * yang diperlukan oleh view.
     *
     * ALUR PROSES METHOD INI:
     *
     * 1. Mengambil user yang sedang login.
     * 2. Memastikan user memiliki hak akses sebagai Admin Magang.
     * 3. Menentukan apakah user adalah Super Admin atau Admin Divisi.
     * 4. Membuat cache key berdasarkan role admin.
     * 5. Mengambil data statistik dashboard (menggunakan cache).
     * 6. Mengirim seluruh data tersebut ke view dashboard.
     *
     * =================================================================
     */
    public function index()
    {

        /**
         * -------------------------------------------------------------
         * STEP 1
         * MENGAMBIL USER YANG SEDANG LOGIN
         * -------------------------------------------------------------
         *
         * Auth::user() mengambil user yang saat ini terautentikasi
         * melalui guard default Laravel.
         *
         * Pada sistem ini:
         * - Guard default digunakan oleh Admin.
         */
        $user = Auth::user();


        /**
         * -------------------------------------------------------------
         * STEP 2
         * VALIDASI HAK AKSES ADMIN MAGANG
         * -------------------------------------------------------------
         *
         * Tidak semua user yang login otomatis menjadi admin magang.
         * Oleh karena itu sistem mengecek apakah user memiliki record
         * pada tabel "magang_access_rights".
         *
         * Jika user tidak ditemukan pada tabel tersebut,
         * maka akses dashboard langsung ditolak.
         */
        $hakAkses = MagangAccessRight::where('user_id', $user->id)->first();

        if (!$hakAkses) {
            abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai Admin Magang.');
        }


        /**
         * -------------------------------------------------------------
         * STEP 3
         * MENENTUKAN ROLE ADMIN
         * -------------------------------------------------------------
         *
         * Sistem menentukan apakah user merupakan:
         *
         * - Super Admin
         * - Admin Divisi
         *
         * Super Admin dapat melihat seluruh data dari semua divisi.
         * Admin Divisi hanya dapat melihat data dari divisinya sendiri.
         */
        $isSuperAdmin = $hakAkses->role === 'superadmin';

        /**
         * division_name menyimpan nama divisi admin.
         * Contoh:
         * - IT
         * - HR
         * - DATA
         */
        $division = $hakAkses->division_name ?? 'unknown';


        /**
         * -------------------------------------------------------------
         * STEP 4
         * MENENTUKAN CACHE KEY
         * -------------------------------------------------------------
         *
         * Cache key digunakan untuk membedakan cache dashboard antara:
         *
         * - Super Admin
         * - Admin per divisi
         *
         * Contoh cache key:
         *
         * dashboard_superadmin
         * dashboard_admin_it
         * dashboard_admin_hr
         *
         * strtolower() digunakan agar format key konsisten.
         */
        $cacheKey = $isSuperAdmin
            ? 'dashboard_superadmin'
            : 'dashboard_admin_' . strtolower($division);


        /**
         * -------------------------------------------------------------
         * STEP 5
         * MENGAMBIL DATA DASHBOARD (MENGGUNAKAN CACHE)
         * -------------------------------------------------------------
         *
         * Cache::remember bekerja dengan cara:
         *
         * - Jika cache masih ada → gunakan cache
         * - Jika cache tidak ada → jalankan query dalam closure
         *   lalu simpan hasilnya ke cache
         *
         * TTL (Time To Live) cache = 1 menit
         */
        $dashboardData = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($isSuperAdmin, $division) {

            /**
             * =========================================================
             * STATISTIK 1
             * TOTAL LOWONGAN MAGANG AKTIF
             * =========================================================
             *
             * Menghitung jumlah lowongan yang statusnya masih "open".
             *
             * Jika admin bukan superadmin,
             * maka lowongan difilter berdasarkan divisi admin.
             */
            $lowonganQuery = VacancyMagang::where('status', 'open');

            if (!$isSuperAdmin) {
                $lowonganQuery->where('division_name', $division);
            }

            $totalLowongan = $lowonganQuery->count();


            /**
             * =========================================================
             * STATISTIK 2
             * PENDAFTARAN YANG PERLU DIVERIFIKASI
             * =========================================================
             *
             * Menghitung jumlah aplikasi dengan status "pending".
             */
            $pendingQuery = ApplicationMagang::where('status', 'pending');


            /**
             * =========================================================
             * STATISTIK 3
             * PESERTA YANG SEDANG MAGANG
             * =========================================================
             *
             * Status yang dianggap sedang magang:
             * - accepted
             * - active
             */
            $activeQuery = ApplicationMagang::whereIn('status', [
                'accepted',
                'active'
            ]);

            /**
             * Jika admin bukan superadmin,
             * maka data difilter berdasarkan divisi vacancy.
             */
            if (!$isSuperAdmin) {

                $pendingQuery->whereHas('vacancy', function ($q) use ($division) {
                    $q->where('division_name', $division);
                });

                $activeQuery->whereHas('vacancy', function ($q) use ($division) {
                    $q->where('division_name', $division);
                });
            }

            $perluVerifikasi = $pendingQuery->count();
            $sedangMagang    = $activeQuery->count();


            /**
             * =========================================================
             * STATISTIK 4
             * TOTAL PESERTA SISWA SMK
             * =========================================================
             */
            $totalSiswa = UserMagang::whereHas('profile', function ($q) {
                $q->where('education_level', 'siswa_smk');
            })->count();


            /**
             * =========================================================
             * STATISTIK 5
             * TOTAL PESERTA MAHASISWA
             * =========================================================
             */
            $totalMahasiswa = UserMagang::whereHas('profile', function ($q) {
                $q->where('education_level', 'mahasiswa');
            })->count();


            /**
             * =========================================================
             * DATA TAMBAHAN
             * 5 PENDAFTARAN TERBARU
             * =========================================================
             *
             * Data ini digunakan untuk tabel "Recent Applications"
             * yang ditampilkan pada dashboard admin.
             *
             * Eager loading digunakan untuk mencegah N+1 Query.
             */
            $pendaftaranQuery = ApplicationMagang::with([
                'leader:id,username,email',
                'leader.profile:id,user_id,institution_name',
                'vacancy:id,title,division_name'
            ])
                ->orderByDesc('submission_date');


            if (!$isSuperAdmin) {
                $pendaftaranQuery->whereHas('vacancy', function ($q) use ($division) {
                    $q->where('division_name', $division);
                });
            }

            $pendaftaranTerbaru = $pendaftaranQuery
                ->limit(5)
                ->get();


            /**
             * Data yang dikembalikan ke cache.
             */
            return [
                'totalLowongan'      => $totalLowongan,
                'perluVerifikasi'    => $perluVerifikasi,
                'sedangMagang'       => $sedangMagang,
                'totalSiswa'         => $totalSiswa,
                'totalMahasiswa'     => $totalMahasiswa,
                'pendaftaranTerbaru' => $pendaftaranTerbaru,
            ];
        });


        /**
         * -------------------------------------------------------------
         * STEP 6
         * MENGIRIM DATA KE VIEW
         * -------------------------------------------------------------
         *
         * View: resources/views/admin/dashboard/index.blade.php
         */
        return view('admin.dashboard.index', array_merge(
            $dashboardData,
            [
                'user'     => $user,
                'hakAkses' => $hakAkses,
            ]
        ));
    }
}
