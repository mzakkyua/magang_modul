<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\VacancyMagang;
use App\Services\DivisionCapacityService;

/**
 * ======================================================================
 * CONTROLLER: LandingController
 * ======================================================================
 */
class LandingController extends Controller
{
    // ======================================================================
    // KONFIGURASI
    // ======================================================================

    private const CACHE_TTL_MINUTES = 10;
    private const CACHE_KEY_MAGANG = 'landing_vacancies_magang';
    private const CACHE_KEY_PENELITIAN = 'landing_vacancies_penelitian';
    private const LISTING_LIMIT = 20;

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
                $vacanciesPenelitian
            ] = $this->getFromCache();
        } else {
            [
                $vacanciesMagang,
                $vacanciesPenelitian
            ] = $this->getFromDatabase($search);
        }

        /**
         * -------------------------------------------------------
         * KAPASITAS DIVISI
         * -------------------------------------------------------
         *
         * Data kapasitas per divisi untuk ditampilkan di landing.
         * Hanya divisi yang sudah dikonfigurasi superadmin yang
         * muncul. Menggunakan cache tersendiri (10 menit).
         *
         * Jika tidak ada divisi yang dikonfigurasi, $divisionCapacity
         * akan berupa Collection kosong — section tidak ditampilkan.
         * -------------------------------------------------------
         */
        $divisionCapacity = DivisionCapacityService::getAllCached();

        return view('landing.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'search',
            'divisionCapacity'
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
            !in_array(
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
    // PRIVATE HELPERS
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
}
