<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use App\Models\MagangAccessRight;
use Illuminate\Support\Str;

class DashboardCache
{

  /**
   * ===============================================================
   * CLEAR DASHBOARD CACHE
   * ===============================================================
   *
   * Digunakan untuk menghapus cache dashboard ketika terjadi
   * perubahan data penting seperti:
   *
   * - create vacancy
   * - update vacancy
   * - delete vacancy
   * - update application status
   * - save assessment
   *
   */

  public static function clear()
  {

    /**
     * ===========================================================
     * HAPUS CACHE SUPERADMIN
     * ===========================================================
     */

    Cache::forget('dashboard_superadmin');



    /**
     * ===========================================================
     * AMBIL SEMUA NAMA DIVISI
     * ===========================================================
     *
     * distinct() agar tidak ada duplikat
     * whereNotNull() untuk mencegah cache key kosong
     *
     */

    $divisions = MagangAccessRight::query()
      ->whereNotNull('division_name')
      ->distinct()
      ->pluck('division_name');



    /**
     * ===========================================================
     * HAPUS CACHE SETIAP DIVISI
     * ===========================================================
     */

    foreach ($divisions as $division) {

      /**
       * Normalisasi key
       *
       * Contoh:
       * "Pengawasan Tenaga Kerja"
       *
       * menjadi:
       * "pengawasan-tenaga-kerja"
       */

      $normalizedDivision = Str::slug($division);



      Cache::forget('dashboard_admin_' . $normalizedDivision);
    }
  }
}
