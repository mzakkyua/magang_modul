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
 * ---------------------------------------------------------------------
 * OPTIMISASI PERFORMANCE
 * ---------------------------------------------------------------------
 * Controller ini menggunakan Cache::remember() agar data dashboard
 * tidak selalu di-query ke database setiap halaman dibuka.
 * Cache disimpan selama 1 menit.
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
     * Menampilkan halaman Dashboard Admin beserta seluruh data statistik.
     *
     * ALUR:
     * 1. Ambil user yang sedang login
     * 2. Validasi hak akses admin magang
     * 3. Tentukan role (superadmin / admin divisi)
     * 4. Buat cache key berdasarkan role
     * 5. Ambil data statistik (dengan cache)
     * 6. Kirim data ke view
     *
     * =================================================================
     */
    public function index()
    {
        // STEP 1: Ambil user yang sedang login (guard default = admin)
        $user = Auth::user();

        // STEP 2: Validasi hak akses admin magang
        $hakAkses = MagangAccessRight::where('user_id', $user->id)->first();

        if (!$hakAkses) {
            abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai Admin Magang.');
        }

        // STEP 3: Tentukan role admin
        $isSuperAdmin = $hakAkses->role === 'superadmin';
        $division     = $hakAkses->division_name ?? 'unknown';

        // STEP 4: Buat cache key unik per role / divisi
        $cacheKey = $isSuperAdmin
            ? 'dashboard_superadmin'
            : 'dashboard_admin_' . strtolower($division);

        // STEP 5: Ambil data dashboard (gunakan cache 1 menit)
        $dashboardData = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($isSuperAdmin, $division) {

            // =========================================================
            // STATISTIK 1: TOTAL LOWONGAN AKTIF
            // =========================================================
            $lowonganQuery = VacancyMagang::where('status', 'open');

            if (!$isSuperAdmin) {
                $lowonganQuery->where('division_name', $division);
            }

            $totalLowongan = $lowonganQuery->count();

            // =========================================================
            // STATISTIK 2: PENDAFTARAN YANG PERLU DIVERIFIKASI
            // =========================================================
            $pendingQuery = ApplicationMagang::where('status', 'pending');

            // =========================================================
            // STATISTIK 3: PESERTA YANG SEDANG MAGANG
            // Status yang dianggap sedang magang: accepted, active
            // =========================================================
            $activeQuery = ApplicationMagang::whereIn('status', ['accepted', 'active']);

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

            // =========================================================
            // STATISTIK 4: TOTAL SISWA SMK
            // =========================================================
            // BUGFIX: Sebelumnya mencari 'siswa_smk' yang tidak pernah
            // match. Nilai yang disimpan register form adalah 'SMA/SMK'.
            $totalSiswa = UserMagang::whereHas('profile', function ($q) {
                $q->where('education_level', 'SMA/SMK');
            })->count();

            // =========================================================
            // STATISTIK 5: TOTAL MAHASISWA
            // =========================================================
            // BUGFIX: Sebelumnya mencari 'mahasiswa' yang tidak pernah
            // match. Nilai yang disimpan register form adalah:
            // 'D3', 'S1', 'S2' — ketiganya termasuk kategori mahasiswa.
            $totalMahasiswa = UserMagang::whereHas('profile', function ($q) {
                $q->whereIn('education_level', ['D3', 'S1', 'S2']);
            })->count();

            // =========================================================
            // DATA TAMBAHAN: 5 PENDAFTARAN TERBARU
            // Eager loading digunakan untuk mencegah N+1 Query.
            // =========================================================
            $pendaftaranQuery = ApplicationMagang::with([
                'leader:id,username,email',
                'leader.profile:id,user_id,institution_name',
                'vacancy:id,title,division_name'
            ])->orderByDesc('submission_date');

            if (!$isSuperAdmin) {
                $pendaftaranQuery->whereHas('vacancy', function ($q) use ($division) {
                    $q->where('division_name', $division);
                });
            }

            $pendaftaranTerbaru = $pendaftaranQuery->limit(5)->get();

            return [
                'totalLowongan'      => $totalLowongan,
                'perluVerifikasi'    => $perluVerifikasi,
                'sedangMagang'       => $sedangMagang,
                'totalSiswa'         => $totalSiswa,
                'totalMahasiswa'     => $totalMahasiswa,
                'pendaftaranTerbaru' => $pendaftaranTerbaru,
            ];
        });

        // STEP 6: Kirim data ke view
        return view('admin.dashboard.index', array_merge(
            $dashboardData,
            [
                'user'     => $user,
                'hakAkses' => $hakAkses,
            ]
        ));
    }
}
