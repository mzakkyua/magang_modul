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
 */
class DivisionCapacityService
{
  /**
   * ==============================================================
   * CACHE CONFIG
   * ==============================================================
   */
  private const CACHE_KEY = 'division_capacity_data';

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
   * 2. Aggregate vacancy dalam 1 query
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

    $divisionNames = $settings
      ->keys()
      ->toArray();

    /**
     * ----------------------------------------------------------
     * QUERY 2:
     * Aggregate vacancy per division
     * ----------------------------------------------------------
     */
    $vacancyStats = DB::table('vacancies_magang')
      ->select([
        'division_name',
        DB::raw('COUNT(*) as filled_slots'),
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
       * ------------------------------------------------------
       * BASIC VALUES
       * ------------------------------------------------------
       */
      $maxSlots    = $setting->max_open_vacancies;
      $filledSlots = (int) ($stats->filled_slots ?? 0);

      /**
       * ------------------------------------------------------
       * AVAILABLE SLOTS
       * ------------------------------------------------------
       *
       * NULL jika unlimited.
       */
      $availableSlots = $maxSlots !== null
        ? max(0, $maxSlots - $filledSlots)
        : null;

      /**
       * ------------------------------------------------------
       * IS FULL
       * ------------------------------------------------------
       */
      $isFull = $maxSlots !== null
        && $availableSlots === 0;

      /**
       * ------------------------------------------------------
       * FILL PERCENTAGE
       * ------------------------------------------------------
       *
       * NULL jika unlimited.
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
       * ------------------------------------------------------
       * LAST BATCH END (Faktualitas)
       * ------------------------------------------------------
       */
      $lastBatchEnd = null;

      if ($stats && $stats->latest_end_date) {
        $lastBatchEnd = Carbon::parse($stats->latest_end_date)->translatedFormat('d F Y');
      }

      return [
        'division_name'     => $setting->division_name,
        'max_slots'         => $maxSlots,
        'filled_slots'      => $filledSlots,
        'available_slots'   => $availableSlots,
        'is_full'           => $isFull,
        'last_batch_end'    => $lastBatchEnd, // <-- Ini data faktualnya
        'fill_percentage'   => $fillPercentage,
      ];
    })->values();
  }
}
