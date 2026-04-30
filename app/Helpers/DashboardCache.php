<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use App\Models\MagangAccessRight;

class DashboardCache
{
  public static function key(string $division): string
  {
    $normalized = strtolower(str_replace(' ', '_', trim($division)));

    return 'dashboard_admin_' . $normalized;
  }

  public static function clear(): void
  {
    Cache::forget('dashboard_superadmin');

    $divisions = MagangAccessRight::query()
      ->whereNotNull('division_name')
      ->distinct()
      ->pluck('division_name');

    foreach ($divisions as $division) {
      Cache::forget(self::key($division));
    }
  }
}
