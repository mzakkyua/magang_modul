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
 * =========================================================
 * CONTROLLER: AdminDashboardController
 * =========================================================
 * TANGGUNG JAWAB:
 * - Menyediakan seluruh data statistik untuk Dashboard Admin
 * - Menyesuaikan data berdasarkan role:
 *   - Super Admin  → Semua divisi
 *   - Admin Divisi → Hanya divisi terkait
 *
 * CATATAN DESAIN:
 * - Menggunakan Cache untuk mengurangi beban query
 * - Seluruh filtering berbasis relasi Eloquent
 *
 * POTENSI MIGRASI:
 * - Logic hak akses bisa dipindahkan ke Middleware
 * - Cache TTL dapat disesuaikan jika traffic tinggi
 * =========================================================
 */
class AdminDashboardController extends Controller
{
    /**
     * =====================================================
     * METHOD: index()
     * =====================================================
     * TUJUAN:
     * - Menampilkan halaman dashboard admin
     * - Menghitung statistik utama (lowongan, pendaftaran, peserta)
     *
     * FLOW BESAR:
     * 1. Validasi user login & hak akses
     * 2. Tentukan cache key berdasarkan role
     * 3. Ambil data dashboard (menggunakan cache)
     * 4. Kirim data ke view
     * =====================================================
     */
    public function index()
    {
        /* =====================================================
         * 1. VALIDASI USER LOGIN & HAK AKSES
         * =====================================================
         * - Mengambil user yang sedang login
         * - Memastikan user terdaftar sebagai Admin Magang
         * - Menentukan role & divisi user
         *
         * NOTE MIGRASI:
         * - Logic ini idealnya dipindahkan ke Middleware
         *   (misal: AdminMagangMiddleware)
         * ===================================================== */
        $user = Auth::user();

        $hakAkses = MagangAccessRight::where('user_id', $user->id)->first();

        // Jika user tidak memiliki hak akses admin magang
        if (!$hakAkses) {
            abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai Admin Magang.');
        }

        // Penentuan role
        $isSuperAdmin = $hakAkses->role === 'superadmin';
        $division     = $hakAkses->division_name;

        /* =====================================================
         * 2. PENENTUAN CACHE KEY
         * =====================================================
         * TUJUAN:
         * - Memisahkan cache antara Super Admin dan Admin Divisi
         *
         * CONTOH CACHE KEY:
         * - dashboard_superadmin
         * - dashboard_admin_it
         *
         * NOTE:
         * - strtolower() digunakan untuk konsistensi penamaan
         * ===================================================== */
        $cacheKey = $isSuperAdmin
            ? 'dashboard_superadmin'
            : 'dashboard_admin_' . strtolower($division);

        /* =====================================================
         * 3. PENGAMBILAN DATA DASHBOARD (CACHE)
         * =====================================================
         * STRATEGI:
         * - Cache disimpan selama 1 menit
         * - Jika cache ada → langsung dipakai
         * - Jika tidak → jalankan query di dalam closure
         *
         * ALASAN TTL PENDEK:
         * - Dashboard perlu data cukup realtime
         * ===================================================== */
        $dashboardData = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($isSuperAdmin, $division) {

            /* ================= LOWONGAN AKTIF =================
             * Menghitung jumlah lowongan dengan status "open"
             * - Super Admin: semua divisi
             * - Admin Divisi: hanya divisinya sendiri
             * =================================================== */
            $lowonganQuery = VacancyMagang::where('status', 'open');

            if (!$isSuperAdmin) {
                $lowonganQuery->where('division_name', $division);
            }

            $totalLowongan = $lowonganQuery->count();

            /* ================= PENDAFTARAN ====================
             * - Pending  → Perlu diverifikasi admin
             * - Accepted / Active → Peserta sedang magang
             *
             * Filter berdasarkan relasi vacancy
             * =================================================== */
            $pendingQuery = ApplicationMagang::where('status', 'pending');
            $activeQuery  = ApplicationMagang::whereIn('status', ['accepted', 'active']);

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

            /* ================= STATISTIK PESERTA ===============
             * Menghitung total peserta berdasarkan jenjang:
             * - Siswa SMK
             * - Mahasiswa
             *
             * Data diambil dari relasi profile user
             * =================================================== */
            $totalSiswa = UserMagang::whereHas('profile', function ($q) {
                $q->where('education_level', 'siswa_smk');
            })->count();

            $totalMahasiswa = UserMagang::whereHas('profile', function ($q) {
                $q->where('education_level', 'mahasiswa');
            })->count();

            /* ================= PENDAFTARAN TERBARU =============
             * - Menampilkan 5 pendaftaran terakhir
             * - Menggunakan eager loading untuk efisiensi query
             *
             * DATA YANG DIMUAT:
             * - Leader + user
             * - Profil institusi
             * - Lowongan magang
             * =================================================== */
            $pendaftaranQuery = ApplicationMagang::with([
                'leader.user:id,name',
                'leader.user.profile:id,user_id,institution_name',
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

            // Data yang akan dikirim ke view
            return [
                'totalLowongan'      => $totalLowongan,
                'perluVerifikasi'    => $perluVerifikasi,
                'sedangMagang'       => $sedangMagang,
                'totalSiswa'         => $totalSiswa,
                'totalMahasiswa'     => $totalMahasiswa,
                'pendaftaranTerbaru' => $pendaftaranTerbaru,
            ];
        });

        /* =====================================================
         * 4. KIRIM DATA KE VIEW
         * =====================================================
         * - Menggabungkan data dashboard dengan data user & hak akses
         * - View: admin.dashboard.index
         * ===================================================== */
        return view('admin.dashboard.index', array_merge(
            $dashboardData,
            [
                'user'     => $user,
                'hakAkses' => $hakAkses,
            ]
        ));
    }
}
