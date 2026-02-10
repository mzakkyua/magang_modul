<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VacancyMagang;
use App\Models\ApplicationMagang;
use App\Models\UserMagang;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        /* =====================================================
         * 1. USER LOGIN & HAK AKSES
         * ===================================================== */
        $user = Auth::user();

        $hakAkses = MagangAccessRight::where('user_id', $user->id)->first();

        if (!$hakAkses) {
            abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai Admin Magang.');
        }

        $isSuperAdmin = $hakAkses->role === 'superadmin';
        $division     = $hakAkses->division_name;

        /* =====================================================
         * 2. CACHE KEY (AMAN & SPESIFIK)
         * ===================================================== */
        $cacheKey = $isSuperAdmin
            ? 'dashboard_superadmin'
            : 'dashboard_admin_' . strtolower($division);

        /* =====================================================
         * 3. AMBIL DATA DARI CACHE (ATAU HITUNG JIKA BELUM ADA)
         * ===================================================== */
        $dashboardData = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($isSuperAdmin, $division) {

            /* ================= QUERY DASAR ================= */

            $lowonganQuery = VacancyMagang::where('status', 'open');
            $pendingQuery  = ApplicationMagang::where('status', 'pending');
            $activeQuery   = ApplicationMagang::where('status', 'accepted'); // atau 'active'

            if (!$isSuperAdmin) {

                $lowonganQuery->where('division_name', $division);

                $pendingQuery->whereHas('vacancy', function ($q) use ($division) {
                    $q->where('division_name', $division);
                });

                $activeQuery->whereHas('vacancy', function ($q) use ($division) {
                    $q->where('division_name', $division);
                });
            }

            /* ================= COUNT ================= */

            $totalLowongan   = (clone $lowonganQuery)->count();
            $perluVerifikasi = (clone $pendingQuery)->count();
            $sedangMagang    = (clone $activeQuery)->count();

            /* ================= STATISTIK PESERTA ================= */

            $totalSiswa = UserMagang::whereHas('profile', function ($q) {
                $q->where('education_level', 'siswa_smk');
            })->count();

            $totalMahasiswa = UserMagang::whereHas('profile', function ($q) {
                $q->where('education_level', 'mahasiswa');
            })->count();

            /* ================= PENDAFTARAN TERBARU ================= */

            $pendaftaranQuery = ApplicationMagang::select(
                'id',
                'name',
                'institution',
                'status',
                'submission_date',
                'vacancy_id'
            )->orderBy('submission_date', 'desc');

            if (!$isSuperAdmin) {
                $pendaftaranQuery->whereHas('vacancy', function ($q) use ($division) {
                    $q->where('division_name', $division);
                });
            }

            $pendaftaranTerbaru = $pendaftaranQuery
                ->limit(5)
                ->get();

            /* ================= RETURN KE CACHE ================= */

            return [
                'totalLowongan'       => $totalLowongan,
                'perluVerifikasi'     => $perluVerifikasi,
                'sedangMagang'        => $sedangMagang,
                'totalSiswa'          => $totalSiswa,
                'totalMahasiswa'      => $totalMahasiswa,
                'pendaftaranTerbaru'  => $pendaftaranTerbaru,
            ];
        });

        /* =====================================================
         * 4. KIRIM KE VIEW
         * ===================================================== */
        return view('admin.dashboard.index', $dashboardData);
    }
}
