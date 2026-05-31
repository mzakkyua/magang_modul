<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\VacancyMagang;

/**
 * ======================================================================
 * CONTROLLER: LandingController
 * ======================================================================
 *
 * CHANGELOG (refactor divisionStats):
 *   - $divisionCapacity (admin quota slot) DIGANTI dengan $divisionStats
 *   - $divisionStats = data lowongan aktif per divisi dari vacancies_magang
 *   - DivisionCapacityService tidak lagi dipakai di sini
 *     → import-nya dihapus dari file ini
 *     → JANGAN hapus file DivisionCapacityService.php dulu sebelum
 *       memastikan tidak ada controller lain (misal AdminDashboard) yang
 *       masih menggunakannya
 */
class LandingController extends Controller
{
    // ======================================================================
    // KONFIGURASI
    // ======================================================================

    private const CACHE_TTL_MINUTES       = 10;
    private const CACHE_KEY_MAGANG        = 'landing_vacancies_magang';
    private const CACHE_KEY_PENELITIAN    = 'landing_vacancies_penelitian';
    private const CACHE_KEY_DIVISION_STATS = 'landing_division_stats';  // ← BARU
    private const LISTING_LIMIT           = 20;

    private const LISTING_COLUMNS = [
        'id',
        'title',
        'division_name',
        'type',
        'registration_mode',
        'quota_slots',
        'min_members',
        'max_members',
        'status',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    private const ACTIVE_STATUSES = [
        'pending',
        'verified',
        'interview',
        'accepted',
    ];

    private const PUBLIC_STATUSES = [
        VacancyMagang::STATUS_OPEN,
        VacancyMagang::STATUS_CLOSED,
    ];

    // ======================================================================
    // INDEX
    // ======================================================================

    public function index(Request $request)
    {
        $search = trim((string) $request->search);

        if ($search === '') {
            [
                $vacanciesMagang,
                $vacanciesPenelitian,
            ] = $this->getFromCache();
        } else {
            [
                $vacanciesMagang,
                $vacanciesPenelitian,
            ] = $this->getFromDatabase($search);
        }

        /**
         * -------------------------------------------------------
         * DIVISION STATS (BARU)
         * -------------------------------------------------------
         *
         * Menampilkan lowongan aktif per divisi di landing page —
         * dari sudut pandang calon peserta:
         *   "Divisi mana yang sedang buka lowongan?"
         *   "Berapa tempat tersedia?"
         *   "Kapan kira-kira buka lagi kalau penuh?"
         *
         * Data bersumber dari vacancies_magang (bukan quota admin).
         * Cache terpisah 10 menit — tidak ikut reset saat search.
         *
         * Selalu fresh dari cache; tidak bergantung pada $search
         * karena section ini bukan bagian dari fitur search.
         * -------------------------------------------------------
         */
        $divisionStats = $this->getDivisionStatsFromCache();
        return view('landing.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'search',
            'divisionStats',
        ));
    }

    // ======================================================================
    // SHOW
    // ======================================================================

    public function show($id)
    {
        $vacancy = VacancyMagang::where('id', $id)
            ->where('status', VacancyMagang::STATUS_OPEN)
            ->firstOrFail();

        return view('landing.show', compact('vacancy'));
    }

    // ======================================================================
    // SNAPSHOT
    // ======================================================================

    public function snapshot(VacancyMagang $vacancy)
    {
        if (
            ! in_array(
                $vacancy->status,
                self::PUBLIC_STATUSES,
                true
            )
        ) {
            abort(404);
        }

        return response()
            ->json([
                'updated_at' => $vacancy->updated_at?->toDateTimeString(),
                'status'     => $vacancy->status,
            ])
            ->header(
                'Cache-Control',
                'no-store, no-cache, must-revalidate'
            );
    }

    // ======================================================================
    // PRIVATE HELPERS — LISTING VACANCIES
    // ======================================================================

    private function getFromCache(): array
    {
        $vacanciesMagang = Cache::remember(
            self::CACHE_KEY_MAGANG,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn() => $this->buildListingQuery('magang')
                ->limit(self::LISTING_LIMIT)
                ->get()
        );

        $vacanciesPenelitian = Cache::remember(
            self::CACHE_KEY_PENELITIAN,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn() => $this->buildListingQuery('penelitian')
                ->limit(self::LISTING_LIMIT)
                ->get()
        );

        return [
            $vacanciesMagang,
            $vacanciesPenelitian,
        ];
    }

    private function getFromDatabase(string $search): array
    {
        $vacanciesMagang = $this->buildListingQuery('magang', $search)
            ->limit(self::LISTING_LIMIT)
            ->get();

        $vacanciesPenelitian = $this->buildListingQuery('penelitian', $search)
            ->limit(self::LISTING_LIMIT)
            ->get();

        return [
            $vacanciesMagang,
            $vacanciesPenelitian,
        ];
    }

    private function buildListingQuery(string $type, string $search = '')
    {
        return VacancyMagang::query()
            ->select(self::LISTING_COLUMNS)
            ->where('status', VacancyMagang::STATUS_OPEN)
            ->where('type', $type)
            ->withCount([
                'applications as active_applications_count' => function ($query) {
                    $query->whereIn('status', self::ACTIVE_STATUSES);
                },
                'applications as total_applications_count',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('division_name', 'like', "%{$search}%");
                    });
                }
            )
            ->latest();
    }

    // ======================================================================
    // PRIVATE HELPERS — DIVISION STATS (BARU)
    // ======================================================================

    /**
     * Ambil division stats dari cache.
     * TTL 10 menit, cache key tersendiri.
     */
    private function getDivisionStatsFromCache(): Collection
    {
        return Cache::remember(
            self::CACHE_KEY_DIVISION_STATS,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn() => $this->computeDivisionStats()
        );
    }

    /**
     * Hitung division stats dari database.
     *
     * Menggunakan 2 query:
     *   Q1 — semua open vacancies + jumlah active applications per vacancy
     *   Q2 — MAX(end_date) lowongan closed per divisi (untuk estimasi buka)
     *
     * Semua aggregasi dilakukan di PHP supaya logika
     * quota_slots=null (unlimited) mudah ditangani.
     *
     * Output per item (sesuai blade division-stats.blade.php):
     *   division_name    string
     *   open_vacancies   int      jumlah vacancy status 'open'
     *   total_available  int      total slot tersedia (0 jika has_unlimited)
     *   has_unlimited    bool     true jika ada vacancy tanpa batas slot
     *   has_open         bool     true jika ada lowongan open
     *   estimated_open   ?string  "Agustus 2026" — hanya jika !has_open
     */
    private function computeDivisionStats(): Collection
    {
        /**
         * ----------------------------------------------------------
         * QUERY 1:
         * Semua open vacancies beserta jumlah active applications.
         *
         * active_count dipakai untuk menghitung sisa slot per vacancy:
         *   sisa = quota_slots - active_count
         * ----------------------------------------------------------
         */
        $openVacancies = VacancyMagang::query()
            ->select(['division_name', 'quota_slots'])
            ->where('status', VacancyMagang::STATUS_OPEN)
            ->withCount([
                'applications as active_count' => fn($q) =>
                $q->whereIn('status', self::ACTIVE_STATUSES),
            ])
            ->get();

        /**
         * ----------------------------------------------------------
         * QUERY 2:
         * MAX(end_date) dari vacancies berstatus closed, per divisi.
         * Dipakai sebagai estimasi kapan divisi bisa buka lagi.
         *
         * Hanya relevan untuk divisi yang tidak punya lowongan open.
         * ----------------------------------------------------------
         */
        $closedEndDates = DB::table('vacancies_magang')
            ->select([
                'division_name',
                DB::raw('MAX(end_date) as latest_end_date'),
            ])
            ->where('status', VacancyMagang::STATUS_CLOSED)
            ->groupBy('division_name')
            ->get()
            ->keyBy('division_name');

        /**
         * ----------------------------------------------------------
         * GROUP open vacancies by division_name
         * ----------------------------------------------------------
         */
        $grouped = $openVacancies->groupBy('division_name');

        /**
         * ----------------------------------------------------------
         * UNIVERSE DIVISI:
         * Union antara divisi yang punya open vacancy
         * dan divisi yang punya closed vacancy (untuk estimated_open).
         *
         * Sort alphabetical agar urutan konsisten.
         * ----------------------------------------------------------
         */
        $allDivisionNames = $grouped->keys()
            ->merge($closedEndDates->keys())
            ->unique()
            ->sort()
            ->values();

        /**
         * ----------------------------------------------------------
         * BUILD FINAL COLLECTION
         * ----------------------------------------------------------
         */
        return $allDivisionNames->map(function (string $divisionName) use (
            $grouped,
            $closedEndDates,
        ) {
            /** @var \Illuminate\Support\Collection $vacancies */
            $vacancies     = $grouped->get($divisionName, collect());
            $hasOpen       = $vacancies->isNotEmpty();
            $openCount     = $vacancies->count();

            /**
             * has_unlimited:
             * true jika ada minimal 1 vacancy dengan quota_slots = null
             */
            $hasUnlimited = $vacancies->contains(
                fn($v) => $v->quota_slots === 0
            );

            /**
             * total_available:
             * Jumlah slot yang masih bisa diisi di semua open vacancies.
             * 0 (dan diabaikan) jika has_unlimited = true.
             */
            $totalAvailable = 0;
            $totalQuota = 0;

            if (! $hasUnlimited) {
                $totalAvailable = $vacancies->sum(function ($v) {
                    $filled = (int) ($v->active_count ?? 0);
                    $quota  = (int) ($v->quota_slots ?? 0);
                    return max(0, $quota - $filled);
                });
                // Hitung total kuota awal ← TAMBAHKAN INI
                $totalQuota = $vacancies->sum(function ($v) {
                    return (int) ($v->quota_slots ?? 0);
                });
            }

            /**
             * estimated_open:
             * Hanya relevan jika divisi tidak punya open vacancies.
             * Diambil dari MAX(end_date) closed vacancies divisi tsb.
             */
            $estimatedOpen = null;

            if (! $hasOpen && isset($closedEndDates[$divisionName])) {
                $latestEnd = $closedEndDates[$divisionName]->latest_end_date;

                if ($latestEnd) {
                    $estimatedOpen = Carbon::parse($latestEnd)
                        ->translatedFormat('F Y');
                }
            }

            return [
                'division_name'   => $divisionName,
                'open_vacancies'  => $openCount,
                'total_available' => $totalAvailable,
                'total_quota'     => $totalQuota,
                'has_unlimited'   => $hasUnlimited,
                'has_open'        => $hasOpen,
                'estimated_open'  => $estimatedOpen,
            ];
        })->values();
    }
}
