<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\VacancyMagang;

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

    /**
     * Kolom yang dibutuhkan untuk listing card
     * description sengaja tidak diambil
     */
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

    /**
     * Status aplikasi yang dianggap memakai slot
     */
    private const ACTIVE_STATUSES = [
        'pending',
        'verified',
        'interview',
        'accepted',
    ];

    /**
     * Status lowongan valid untuk snapshot
     */
    private const PUBLIC_STATUSES = [
        'open',
        'closed',
    ];

    // ======================================================================
    // INDEX
    // ======================================================================

    public function index(Request $request)
    {
        $search = trim((string) $request->search);

        /**
         * Jika tidak ada search:
         * pakai cache
         *
         * Jika ada search:
         * query fresh dari DB
         */
        if ($search === '') {
            [$vacanciesMagang, $vacanciesPenelitian] = $this->getFromCache();
        } else {
            [$vacanciesMagang, $vacanciesPenelitian] = $this->getFromDatabase($search);
        }

        return view('landing.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'search'
        ));
    }

    // ======================================================================
    // SHOW
    // ======================================================================

    /**
     * Detail lowongan publik
     * Hanya lowongan OPEN yang bisa diakses
     */
    public function show($id)
    {
        $vacancy = VacancyMagang::where('id', $id)
            ->where('status', 'open')
            ->firstOrFail();

        return view('landing.show', compact('vacancy'));
    }

    // ======================================================================
    // SNAPSHOT
    // ======================================================================

    /**
     * Endpoint ringan untuk polling frontend.
     *
     * Aman karena hanya mengembalikan:
     * - status
     * - updated_at
     *
     * Tidak mengembalikan data sensitif.
     */
    public function snapshot(VacancyMagang $vacancy)
    {
        if (!in_array($vacancy->status, self::PUBLIC_STATUSES, true)) {
            abort(404);
        }

        return response()
            ->json([
                'updated_at' => $vacancy->updated_at?->toDateTimeString(),
                'status'     => $vacancy->status,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    // ======================================================================
    // PRIVATE HELPERS
    // ======================================================================

    /**
     * Ambil listing dari cache
     */
    private function getFromCache(): array
    {
        $vacanciesMagang = Cache::remember(
            self::CACHE_KEY_MAGANG,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn() => $this->buildListingQuery('magang')
                ->limit(20)
                ->get()
        );

        $vacanciesPenelitian = Cache::remember(
            self::CACHE_KEY_PENELITIAN,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn() => $this->buildListingQuery('penelitian')
                ->get()
        );

        return [
            $vacanciesMagang,
            $vacanciesPenelitian,
        ];
    }

    /**
     * Search langsung ke database
     */
    private function getFromDatabase(string $search): array
    {
        $vacanciesMagang = $this->buildListingQuery('magang', $search)
            ->limit(20)
            ->get();

        $vacanciesPenelitian = $this->buildListingQuery('penelitian', $search)
            ->get();

        return [
            $vacanciesMagang,
            $vacanciesPenelitian,
        ];
    }

    /**
     * Query builder reusable
     */
    private function buildListingQuery(string $type, string $search = '')
    {
        return VacancyMagang::query()
            ->select(self::LISTING_COLUMNS)
            ->where('status', 'open')
            ->where('type', $type)
            ->withCount([
                'applications as active_applications_count' => function ($query) {
                    $query->whereIn('status', self::ACTIVE_STATUSES);
                },
                'applications as total_applications_count',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('division_name', 'like', "%{$search}%");
                });
            })
            ->latest();
    }
}
