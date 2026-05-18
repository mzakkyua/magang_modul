<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DashboardCache;
use App\Http\Controllers\Controller;
use App\Models\DivisionSetting;
use App\Services\DivisionCapacityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * ======================================================================
 * CONTROLLER: DivisionSettingController
 * ======================================================================
 *
 * Mengelola quota lowongan per divisi.
 *
 * Hanya dapat diakses oleh superadmin.
 *
 * Superadmin dapat:
 * - melihat kapasitas divisi
 * - mengubah quota divisi
 *
 * Source of truth divisi berada di:
 * DivisionController.
 * ======================================================================
 */
class DivisionSettingController extends Controller
{
    // ======================================================================
    // INDEX
    // ======================================================================

  /**
   * ==============================================================
   * HALAMAN KELOLA QUOTA DIVISI
   * ==============================================================
   */
  public function index()
  {
    $this->authorizeSuperAdmin();

    /**
     * ----------------------------------------------------------
     * SEMUA DIVISION SETTINGS
     * ----------------------------------------------------------
     */
    $settings = DivisionSetting::query()
      ->orderBy('division_name')
      ->get();

    /**
     * ----------------------------------------------------------
     * DATA KAPASITAS REALTIME
     * ----------------------------------------------------------
     */
    $capacityData = DivisionCapacityService::getAll()
      ->keyBy('division_name');

    return view(
      'admin.division-settings.index',
      compact(
        'settings',
        'capacityData'
      )
    );
  }

    // ======================================================================
    // UPDATE
    // ======================================================================

  /**
   * ==============================================================
   * UPDATE QUOTA DIVISI
   * ==============================================================
   */
  public function update(
    Request $request,
    DivisionSetting $divisionSetting
  ) {

    $this->authorizeSuperAdmin();

    /**
     * ----------------------------------------------------------
     * VALIDATION
     * ----------------------------------------------------------
     */
    $request->validate([
      'max_open_vacancies' => [
        'nullable',
        'integer',
        'min:1',
        'max:99',
      ],
    ], [

      'max_open_vacancies.min'
      => 'Batas minimum adalah 1 lowongan.',

      'max_open_vacancies.max'
      => 'Batas maksimum adalah 99 lowongan.',
    ]);

    /**
     * ----------------------------------------------------------
     * UPDATE SETTING
     * ----------------------------------------------------------
     */
    $divisionSetting->update([

      'max_open_vacancies' => $request->max_open_vacancies ?: null,

      'updated_by'
      => Auth::id(),
    ]);

    /**
     * ----------------------------------------------------------
     * CLEAR CACHE
     * ----------------------------------------------------------
     */
    DivisionCapacityService::clearCache();

    DashboardCache::clear();

    /**
     * ----------------------------------------------------------
     * LOGGING
     * ----------------------------------------------------------
     */
    Log::info('Quota divisi diperbarui', [

      'admin_id' => Auth::id(),

      'division_name'
      => $divisionSetting->division_name,

      'max_open_vacancies'
      => $divisionSetting->max_open_vacancies,
    ]);

    /**
     * ----------------------------------------------------------
     * RESPONSE
     * ----------------------------------------------------------
     */
    return back()->with(
      'success',
      "Kuota divisi \"{$divisionSetting->division_name}\" berhasil diperbarui."
    );
  }

    // ======================================================================
    // PRIVATE
    // ======================================================================

  /**
   * ==============================================================
   * SUPERADMIN GUARD
   * ==============================================================
   */
  private function authorizeSuperAdmin(): void
  {
    $hakAkses = request()
      ->attributes
      ->get('magang_access');

    if (
      !$hakAkses
      || !$hakAkses->isSuperAdmin()
    ) {
      abort(
        403,
        'Halaman ini hanya dapat diakses oleh Super Admin.'
      );
    }
  }
}
