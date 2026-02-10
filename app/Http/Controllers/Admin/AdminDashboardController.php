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
         * 2. CACHE KEY
         * ===================================================== */
        $cacheKey = $isSuperAdmin
            ? 'dashboard_superadmin'
            : 'dashboard_admin_' . strtolower($division);

        /* =====================================================
         * 3. AMBIL DATA DASHBOARD (CACHE)
         * ===================================================== */
        $dashboardData = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($isSuperAdmin, $division) {

            /* ================= LOWONGAN AKTIF ================= */
            $lowonganQuery = VacancyMagang::where('status', 'open');

            if (!$isSuperAdmin) {
                $lowonganQuery->where('division_name', $division);
            }

            $totalLowongan = $lowonganQuery->count();

            /* ================= PENDAFTARAN ================= */
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

            /* ================= STATISTIK PESERTA ================= */
            $totalSiswa = UserMagang::whereHas('profile', function ($q) {
                $q->where('education_level', 'siswa_smk');
            })->count();

            $totalMahasiswa = UserMagang::whereHas('profile', function ($q) {
                $q->where('education_level', 'mahasiswa');
            })->count();

            /* ================= PENDAFTARAN TERBARU ================= */
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
         * 4. KIRIM KE VIEW
         * ===================================================== */
        return view('admin.dashboard.index', array_merge(
            $dashboardData,
            [
                'user'      => $user,
                'hakAkses'  => $hakAkses,
            ]
        ));
    }
}
