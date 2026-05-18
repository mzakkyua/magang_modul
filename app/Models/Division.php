<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
  /**
   * =========================================================
   * TABLE
   * =========================================================
   */

  protected $table = 'divisions_magang';

  /**
   * =========================================================
   * MASS ASSIGNMENT
   * =========================================================
   */

  protected $fillable = [
    'name',
    'is_active',
  ];

  /**
   * =========================================================
   * CASTS
   * =========================================================
   */

  protected $casts = [
    'is_active' => 'boolean',
  ];

  /**
   * =========================================================
   * SCOPES
   * =========================================================
   */

  public function scopeActive(Builder $query): Builder
  {
    return $query->where('is_active', true);
  }

  /**
   * =========================================================
   * RELATIONS
   * =========================================================
   */

  /**
   * Relasi sementara masih string bridge.
   *
   * Belum memakai division_id agar:
   * - backward compatible
   * - tidak rewrite subsystem besar
   * - aman untuk occupancy system existing
   */
  public function vacancies()
  {
    return $this->hasMany(
      VacancyMagang::class,
      'division_name',
      'name'
    );
  }

  public function setting()
  {
    return $this->hasOne(
      DivisionSetting::class,
      'division_name',
      'name'
    );
  }

  /**
   * =========================================================
   * HELPERS
   * =========================================================
   */

  /**
   * Apakah masih memiliki lowongan
   * yang masih memakai slot occupancy.
   */
  public function hasActiveVacancies(): bool
  {
    return $this->vacancies()
      ->whereIn('status', [
        VacancyMagang::STATUS_OPEN,
        VacancyMagang::STATUS_CLOSED,
      ])
      ->exists();
  }
}
