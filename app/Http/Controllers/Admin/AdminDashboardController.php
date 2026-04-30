<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationMagang;
use App\Models\MagangAccessRight;
use App\Helpers\DashboardCache;
use App\Models\VacancyMagang;
use App\Models\UserMagang;

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

    // ======================================================================
    // KONFIGURASI
    // ======================================================================

    private const CACHE_TTL_MINUTES = 5;

    // Status yang dianggap "sedang magang aktif"
    private const ACTIVE_STATUSES = ['accepted', 'verified', 'interview'];

    // Jenjang yang dikategorikan mahasiswa
    private const MAHASISWA_LEVELS = ['D3', 'S1', 'S2'];

    // Jenjang yang dikategorikan siswa
    private const SISWA_LEVELS = ['SMA', 'SMK'];


    // ======================================================================
    // INDEX
    // ======================================================================

    public function index()
    {
        /*
        ==============================================================
        1. VALIDASI HAK AKSES
        ==============================================================
        */
        $user     = Auth::user();
        $hakAkses = request()->attributes->get('magang_access');

        if (!$hakAkses) {
            abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai Admin Magang.');
        }

        $isSuperAdmin = $hakAkses->role === MagangAccessRight::ROLE_SUPERADMIN;
        $division     = $hakAkses->division_name ?? 'unknown';

        /*
        ==============================================================
        2. CACHE KEY UNIK PER ROLE / DIVISI
        ==============================================================
        Superadmin dan setiap admin divisi punya cache terpisah
        agar data tidak bercampur antar role.
        */
        $cacheKey = $isSuperAdmin
            ? 'dashboard_superadmin'
            : DashboardCache::key($division);

        /*
        ==============================================================
        3. AMBIL DATA DASHBOARD (DENGAN CACHE)
        ==============================================================
        TTL 5 menit — dashboard tidak perlu real-time.
        Cache di-clear oleh DashboardCache::clear() setiap kali ada
        perubahan status aplikasi atau mutasi lowongan.
        */
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

        return view('admin.dashboard.index', array_merge(
            $dashboardData,
            ['user' => $user, 'hakAkses' => $hakAkses]
        ));
    }


    // ======================================================================
    // PRIVATE — STATS LOWONGAN
    // ======================================================================

    /**
     * Hitung total lowongan aktif (status = open).
     * Scope ke divisi jika bukan superadmin.
     */
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

    /**
     * -----------------------------------------------------------------------
     * getApplicationStats()
     * -----------------------------------------------------------------------
     *
     * IMPROVEMENT BESAR — dari N query menjadi 1 query:
     *
     * SEBELUMNYA:
     *   $perluVerifikasi = Application::where('status','pending')->count();   // 1 query
     *   $sedangMagang    = Application::whereIn('status',[...])->count();      // 1 query
     *   (+ jika ada status lain, tambah query lagi)
     *
     * SEKARANG:
     *   1 query GROUP BY status → hasilnya di-map ke variabel
     *
     * Ini mengurangi round-trip DB dari N menjadi 1,
     * sangat terasa saat data besar atau koneksi DB lambat.
     * -----------------------------------------------------------------------
     */
    private function getApplicationStats(bool $isSuperAdmin, string $division): array
    {
        $query = ApplicationMagang::query();

        // Scope divisi — WAJIB konsisten untuk semua widget
        if (!$isSuperAdmin) {
            $query->whereHas('vacancy', function ($q) use ($division) {
                $q->where('division_name', $division);
            });
        }

        // 1 query GROUP BY → dapat semua status sekaligus
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
            'statusCounts'    => $counts, // tersedia untuk widget lain di blade jika diperlukan
        ];
    }


    // ======================================================================
    // PRIVATE — STATS USER
    // ======================================================================

    /**
     * -----------------------------------------------------------------------
     * getUserStats()
     * -----------------------------------------------------------------------
     *
     * IMPROVEMENT — dari 2 query menjadi 1 query:
     *
     * SEBELUMNYA:
     *   UserMagang::whereHas('profile', fn($q) => $q->where('education_level','SMA/SMK'))->count();
     *   UserMagang::whereHas('profile', fn($q) => $q->whereIn('education_level',['D3','S1']))->count();
     *
     * SEKARANG:
     *   1 query GROUP BY education_level → map ke kategori
     *
     * Stats user tidak perlu di-scope divisi karena user terdaftar
     * secara global, bukan per divisi.
     * -----------------------------------------------------------------------
     */
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

    /**
     * Ambil 5 pendaftaran terbaru dengan eager loading.
     * Scope ke divisi jika bukan superadmin — KONSISTEN dengan stats.
     */
    private function getRecentApplications(bool $isSuperAdmin, string $division)
    {
        $query = ApplicationMagang::with([
            'leader:id,username,email',
            'leader.profile:id,user_id,institution_name',
            'vacancy:id,title,division_name',
        ])->orderByDesc('created_at');

        // Scope divisi — sama seperti stats agar data konsisten
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

    /**
     * -----------------------------------------------------------------------
     * getChartData()
     * -----------------------------------------------------------------------
     *
     * IMPROVEMENT — hitung data chart langsung di SQL, bukan loop PHP:
     *
     * SEBELUMNYA (pola umum yang salah):
     *   foreach (last 6 months as $month) {
     *       $data[] = Application::whereMonth('created_at', $month)->count(); // 6 query
     *   }
     *
     * SEKARANG:
     *   1 query GROUP BY YEAR, MONTH → hasil di-map ke array bulan
     *
     * Mengurangi dari 6 query menjadi 1 query,
     * dan hasilnya lebih akurat karena dihitung di DB.
     * -----------------------------------------------------------------------
     */
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

        // Bangun array 6 bulan lengkap — isi 0 jika tidak ada data di bulan itu
        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $date   = now()->subMonths($i);
            $key    = $date->format('Y-m');
            $labels[] = $date->translatedFormat('M Y'); // e.g. "Jan 2026"
            $values[] = $rawData->get($key)?->total ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
