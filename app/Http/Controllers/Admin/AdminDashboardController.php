<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationMagang;
use App\Models\MagangAccessRight;
use App\Helpers\DashboardCache;
use App\Models\VacancyMagang;
use App\Models\UserMagang;
use App\Services\DivisionCapacityService;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * =====================================================================
 * CONTROLLER: AdminDashboardController
 * =====================================================================
 */
class AdminDashboardController extends Controller
{
    private const CACHE_TTL_MINUTES = 5;
    private const ACTIVE_STATUSES   = ['accepted', 'verified', 'interview'];
    private const MAHASISWA_LEVELS  = ['D3', 'S1', 'S2'];
    private const SISWA_LEVELS      = ['SMA', 'SMK'];

    // ======================================================================
    // INDEX
    // ======================================================================

    public function index()
    {
        $user     = Auth::user();
        $hakAkses = request()->attributes->get('magang_access');

        if (!$hakAkses) {
            abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai Admin Magang.');
        }

        $isSuperAdmin = $hakAkses->role === MagangAccessRight::ROLE_SUPERADMIN;
        $division     = $hakAkses->division_name ?? 'unknown';

        $cacheKey = $isSuperAdmin
            ? 'dashboard_superadmin'
            : DashboardCache::key($division);

        $dashboardData = Cache::remember(
            $cacheKey,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            function () use ($isSuperAdmin, $division) {
                return [
                    ...$this->getVacancyStats($isSuperAdmin, $division),
                    ...$this->getApplicationStats($isSuperAdmin, $division),
                    ...$this->getUserStats(),
                    'pendaftaranTerbaru' => $this->getRecentApplications($isSuperAdmin, $division),
                    'chartData'         => $this->getChartData($isSuperAdmin, $division),
                ];
            }
        );

        /**
         * -------------------------------------------------------
         * KAPASITAS DIVISI
         * -------------------------------------------------------
         *
         * Data kapasitas TIDAK dimasukkan ke dalam cache dashboard
         * utama, karena punya cache-nya sendiri (division_capacity_data)
         * dengan TTL dan invalidasi terpisah.
         *
         * Superadmin melihat SEMUA divisi.
         * Admin divisi hanya melihat divisinya sendiri.
         * -------------------------------------------------------
         */
        $divisionCapacity = DivisionCapacityService::getAllCached();

        if (!$isSuperAdmin) {
            $divisionCapacity = $divisionCapacity->filter(
                fn($item) => $item['division_name'] === $division
            )->values();
        }

        return view('admin.dashboard.index', array_merge(
            $dashboardData,
            [
                'user'             => $user,
                'hakAkses'         => $hakAkses,
                'divisionCapacity' => $divisionCapacity,
                'isSuperAdmin'     => $isSuperAdmin,
            ]
        ));
    }

    // ======================================================================
    // PRIVATE — STATS LOWONGAN
    // ======================================================================

    private function getVacancyStats(bool $isSuperAdmin, string $division): array
    {
        $query = VacancyMagang::where('status', 'open');

        if (!$isSuperAdmin) {
            $query->where('division_name', $division);
        }

        return [
            'totalLowongan' => $query->count(),
        ];
    }

    // ======================================================================
    // PRIVATE — STATS APLIKASI
    // ======================================================================

    private function getApplicationStats(bool $isSuperAdmin, string $division): array
    {
        $query = ApplicationMagang::query();

        if (!$isSuperAdmin) {
            $query->whereHas('vacancy', function ($q) use ($division) {
                $q->where('division_name', $division);
            });
        }

        $counts = (clone $query)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $perluVerifikasi = $counts->get('pending', 0);

        $sedangMagang = collect(self::ACTIVE_STATUSES)
            ->sum(fn($s) => $counts->get($s, 0));

        return [
            'perluVerifikasi' => $perluVerifikasi,
            'sedangMagang'    => $sedangMagang,
            'statusCounts'    => $counts,
        ];
    }

    // ======================================================================
    // PRIVATE — STATS USER
    // ======================================================================

    private function getUserStats(): array
    {
        $levelCounts = DB::table('profiles_magang')
            ->selectRaw('education_level, count(*) as total')
            ->whereIn('education_level', array_merge(self::SISWA_LEVELS, self::MAHASISWA_LEVELS))
            ->groupBy('education_level')
            ->pluck('total', 'education_level');

        $totalSiswa = collect(self::SISWA_LEVELS)
            ->sum(fn($l) => $levelCounts->get($l, 0));

        $totalMahasiswa = collect(self::MAHASISWA_LEVELS)
            ->sum(fn($l) => $levelCounts->get($l, 0));

        return [
            'totalSiswa'     => $totalSiswa,
            'totalMahasiswa' => $totalMahasiswa,
        ];
    }

    // ======================================================================
    // PRIVATE — RECENT APPLICATIONS
    // ======================================================================

    private function getRecentApplications(bool $isSuperAdmin, string $division)
    {
        $query = ApplicationMagang::with([
            'leader:id,username,email',
            'leader.profile:id,user_id,institution_name',
            'vacancy:id,title,division_name',
        ])->orderByDesc('created_at');

        if (!$isSuperAdmin) {
            $query->whereHas('vacancy', function ($q) use ($division) {
                $q->where('division_name', $division);
            });
        }

        return $query->limit(5)->get();
    }

    // ======================================================================
    // PRIVATE — CHART DATA
    // ======================================================================

    private function getChartData(bool $isSuperAdmin, string $division): array
    {
        $query = ApplicationMagang::selectRaw(
            'YEAR(created_at) as year,
                 MONTH(created_at) as month,
                 count(*) as total'
        )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)');

        if (!$isSuperAdmin) {
            $query->whereHas('vacancy', function ($q) use ($division) {
                $q->where('division_name', $division);
            });
        }

        $rawData = $query->get()->keyBy(function ($item) {
            return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
        });

        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $date     = now()->subMonths($i);
            $key      = $date->format('Y-m');
            $labels[] = $date->translatedFormat('M Y');
            $values[] = $rawData->get($key)?->total ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
