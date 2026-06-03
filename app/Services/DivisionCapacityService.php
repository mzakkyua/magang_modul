<?php

namespace App\Services;

use App\Models\DivisionSetting;
use App\Models\VacancyMagang;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * ======================================================================
 * SERVICE: DivisionCapacityService
 * ======================================================================
 *
 * Menghitung kapasitas lowongan per divisi.
 *
 * Slot terisi:
 * - open
 * - closed
 *
 * Vacancy archived tidak dihitung.
 *
 * Estimasi slot buka:
 * - menggunakan MAX(end_date)
 * - dari vacancy open/closed
 *
 * ======================================================================
 * CHANGELOG:
 * - FIX: Tambah open_count di SQL agar has_open & open_vacancies tersedia
 * - FIX: Tambah key yang dibutuhkan blade (has_open, open_vacancies,
 *         has_unlimited, total_quota, total_available)
 * - FIX: Tetap pertahankan key lama (max_slots, available_slots, dll.)
 *         agar AdminDashboard & DivisionSettingController tidak rusak
 * ======================================================================
 */
class DivisionCapacityService
{
  /**
   * ==============================================================
   * CACHE CONFIG
   * ==============================================================
   */
  private const CACHE_KEY         = 'division_capacity_data';
  private const CACHE_TTL_MINUTES = 10;

  /**
   * ==============================================================
   * STATUS YANG MENGISI SLOT
   * ==============================================================
   */
  private const OCCUPYING_STATUSES = [
    VacancyMagang::STATUS_OPEN,
    VacancyMagang::STATUS_CLOSED,
  ];

    // ======================================================================
    // PUBLIC API
    // ======================================================================

  /**
   * ==============================================================
   * GET ALL DIVISION CAPACITY (WITH CACHE)
   * ==============================================================
   */
  public static function getAllCached(): Collection
  {
    return Cache::remember(
      self::CACHE_KEY,
      self::CACHE_TTL_MINUTES * 60,
      fn() => self::computeAll()
    );
  }

  /**
   * ==============================================================
   * GET ALL WITHOUT CACHE
   * ==============================================================
   */
  public static function getAll(): Collection
  {
    return self::computeAll();
  }

  /**
   * ==============================================================
   * CLEAR CACHE
   * ==============================================================
   */
  public static function clearCache(): void
  {
    Cache::forget(self::CACHE_KEY);
  }

    // ======================================================================
    // PRIVATE METHODS
    // ======================================================================

  /**
   * ==============================================================
   * COMPUTE ALL DIVISION CAPACITY
   * ==============================================================
   *
   * STRATEGI:
   * 1. Ambil semua division settings
   * 2. Aggregate vacancy dalam 1 query (total + open count)
   * 3. Build collection di PHP
   *
   * Total query: 2 query saja
   * ==============================================================
   */
  private static function computeAll(): Collection
  {
    /**
     * ----------------------------------------------------------
     * QUERY 1:
     * Semua division settings
     * ----------------------------------------------------------
     */
    $settings = DivisionSetting::query()
      ->orderBy('division_name')
      ->get()
      ->keyBy('division_name');

    $divisionNames = $settings->keys()->toArray();

    /**
     * ----------------------------------------------------------
     * QUERY 2:
     * Aggregate vacancy per division.
     *
     * FIX: Tambah open_count via CASE WHEN agar blade bisa
     * menampilkan jumlah lowongan yang benar-benar OPEN,
     * terpisah dari yang sudah CLOSED (penuh).
     *
     * Sebelum: hanya ada filled_slots (gabungan open + closed)
     * Sesudah: ada filled_slots + open_count (hanya yg open)
     * ----------------------------------------------------------
     */
    $vacancyStats = DB::table('vacancies_magang')
      ->select([
        'division_name',

        // Total slot terpakai (open + closed)
        DB::raw('COUNT(*) as filled_slots'),

        // FIX: Hanya lowongan berstatus OPEN
        DB::raw(
          'SUM(CASE WHEN status = \'' . VacancyMagang::STATUS_OPEN . '\' THEN 1 ELSE 0 END) as open_count'
        ),

        // Tanggal akhir lowongan terakhir (faktual)
        DB::raw('MAX(end_date) as latest_end_date'),
      ])
      ->whereIn('status', self::OCCUPYING_STATUSES)
      ->whereIn('division_name', $divisionNames)
      ->groupBy('division_name')
      ->get()
      ->keyBy('division_name');

    /**
     * ----------------------------------------------------------
     * BUILD FINAL COLLECTION
     * ----------------------------------------------------------
     */
    return $settings->map(function (DivisionSetting $setting) use ($vacancyStats) {

      $stats = $vacancyStats->get($setting->division_name);

      /**
       * --------------------------------------------------
       * BASIC VALUES
       * --------------------------------------------------
       */
      $maxSlots    = $setting->max_open_vacancies;   // null = unlimited
      $filledSlots = (int) ($stats->filled_slots ?? 0);
      $openCount   = (int) ($stats->open_count   ?? 0); // FIX: hitung open saja

      /**
       * --------------------------------------------------
       * AVAILABLE SLOTS
       * NULL jika unlimited.
       * --------------------------------------------------
       */
      $availableSlots = $maxSlots !== null
        ? max(0, $maxSlots - $filledSlots)
        : null;

      /**
       * --------------------------------------------------
       * IS FULL
       * --------------------------------------------------
       */
      $isFull = $maxSlots !== null && $availableSlots === 0;

      /**
       * --------------------------------------------------
       * FILL PERCENTAGE
       * NULL jika unlimited.
       * --------------------------------------------------
       */
      $fillPercentage = match (true) {
        $maxSlots === null => null,
        $maxSlots === 0   => 100,
        default           => min(
          100,
          (int) round(($filledSlots / $maxSlots) * 100)
        ),
      };

      /**
       * --------------------------------------------------
       * LAST BATCH END (Faktualitas)
       * --------------------------------------------------
       */
      $lastBatchEnd = null;
      if ($stats && $stats->latest_end_date) {
        $lastBatchEnd = Carbon::parse($stats->latest_end_date)
          ->translatedFormat('d F Y');
      }

      /**
       * --------------------------------------------------
       * FIX: KEY YANG DIBUTUHKAN BLADE
       * --------------------------------------------------
       *
       * Blade division-capacity-landing.blade.php membutuhkan:
       *   - has_open        → apakah ada lowongan open saat ini
       *   - open_vacancies  → jumlah lowongan berstatus open
       *   - has_unlimited   → apakah kuota tidak dibatasi
       *   - total_quota     → alias max_slots (untuk tampilan blade)
       *   - total_available → alias available_slots
       *
       * Key lama (max_slots, available_slots, dll.) TETAP ADA
       * agar DivisionSettingController & AdminDashboard tidak rusak.
       * --------------------------------------------------
       */
      return [
        // ── KEY UNTUK BLADE (landing page) ──────────────────
        'division_name'   => $setting->division_name,
        'has_open'        => $openCount > 0,                // FIX: dulu tidak ada
        'open_vacancies'  => $openCount,                    // FIX: dulu tidak ada
        'has_unlimited'   => $maxSlots === null,            // FIX: dulu tidak ada
        'total_quota'     => $maxSlots,                     // FIX: dulu 'max_slots'
        'total_available' => $availableSlots ?? 0,          // FIX: dulu 'available_slots'
        'last_batch_end'  => $lastBatchEnd,                 // sudah ada, selalu included

        // ── KEY LAMA (backward compat admin panel) ───────────
        'max_slots'       => $maxSlots,
        'filled_slots'    => $filledSlots,
        'available_slots' => $availableSlots,
        'is_full'         => $isFull,
        'fill_percentage' => $fillPercentage,
      ];
    })->values();
  }
}
