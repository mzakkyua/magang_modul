<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use App\Models\MagangAccessRight;

/**
 * ======================================================================
 * HELPER: DashboardCache
 * ======================================================================
 *
 * Mengelola invalidasi cache dashboard admin.
 *
 * Cache key dashboard dibangun dari MagangAccessRight.division_name,
 * bukan dari DivisionSetting.division_name — sehingga sumber
 * kebenaran untuk clear cache HARUS MagangAccessRight.
 * ======================================================================
 */
class DashboardCache
{
  /**
   * =========================================================
   * CACHE KEY DASHBOARD DIVISI
   * =========================================================
   *
   * Format: dashboard_admin_{division_normalized}
   * Contoh: dashboard_admin_teknologi_informasi
   * =========================================================
   */
  public static function key(string $division): string
  {
    $normalized = strtolower(
      str_replace(' ', '_', trim($division))
    );

    return 'dashboard_admin_' . $normalized;
  }

  /**
   * =========================================================
   * FORGET CACHE SATU DIVISI
   * =========================================================
   *
   * Digunakan oleh DivisionSettingController::destroy()
   * SEBELUM record dihapus dari DB, agar cache key-nya
   * masih bisa dihitung dari nama divisi yang tersedia.
   * =========================================================
   */
  public static function forgetDivision(string $division): void
  {
    Cache::forget(self::key($division));
  }

  /**
   * =========================================================
   * CLEAR ALL RELATED CACHE
   * =========================================================
   *
   * Dipanggil setiap kali ada perubahan data yang mempengaruhi
   * tampilan dashboard: create/update/archive/delete vacancy,
   * update status lamaran, update kuota divisi.
   * =========================================================
   */
  public static function clear(): void
  {
    /**
     * ---------------------------------------------------------
     * SUPERADMIN DASHBOARD
     * ---------------------------------------------------------
     */
    Cache::forget('dashboard_superadmin');

    /**
     * ---------------------------------------------------------
     * DASHBOARD PER DIVISI
     * ---------------------------------------------------------
     *
     * FIX: Gunakan MagangAccessRight, BUKAN DivisionSetting.
     *
     * SEBELUMNYA (BUG):
     *   $divisions = DivisionSetting::query()->pluck('division_name');
     *
     * Masalah: cache dashboard di-generate dari
     * MagangAccessRight.division_name (via $hakAkses->division_name).
     * Jika admin divisi memiliki divisi yang belum terdaftar
     * di division_settings, cache dashboard admin itu tidak
     * pernah di-clear → data stale di dashboard admin.
     *
     * SETELAH FIX:
     * Ambil semua division_name dari MagangAccessRight,
     * yang merupakan sumber kebenaran sesungguhnya untuk
     * menentukan cache key dashboard admin.
     * ---------------------------------------------------------
     */
    $divisions = MagangAccessRight::query()
      ->whereNotNull('division_name')
      ->distinct()
      ->pluck('division_name');

    foreach ($divisions as $division) {
      Cache::forget(self::key($division));
    }

    /**
     * ---------------------------------------------------------
     * LANDING PAGE CACHE
     * ---------------------------------------------------------
     *
     * Harus di-clear setiap kali status vacancy berubah
     * agar guest tidak melihat data stale.
     * ---------------------------------------------------------
     */
    Cache::forget('landing_vacancies_magang');
    Cache::forget('landing_vacancies_penelitian');

    /**
     * ---------------------------------------------------------
     * DIVISION CAPACITY CACHE
     * ---------------------------------------------------------
     *
     * Di-clear setiap kali status vacancy berubah (karena
     * filled_slots dihitung dari vacancy open/closed) atau
     * ketika kuota divisi diubah oleh superadmin.
     * ---------------------------------------------------------
     */
    Cache::forget('division_capacity_data');
  }
}
