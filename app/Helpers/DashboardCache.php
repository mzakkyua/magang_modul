<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use App\Models\MagangAccessRight;

class DashboardCache
{
  public static function clear()
  {
    // Hapus cache Super Admin
    Cache::forget('dashboard_superadmin');

    // Hapus cache semua Admin Bidang
    $divisions = MagangAccessRight::distinct()
      ->pluck('division_name');

    foreach ($divisions as $division) {
      Cache::forget('dashboard_admin_' . strtolower($division));
    }
  }
}
