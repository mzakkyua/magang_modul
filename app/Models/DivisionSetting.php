<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DivisionSetting extends Model
{
    /**
     * =====================================================
     * TABLE
     * =====================================================
     */
    protected $table = 'division_settings_magang';

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'division_name',
        'max_open_vacancies',
        'updated_by',
    ];

    /**
     * =====================================================
     * CASTING
     * =====================================================
     */
    protected $casts = [
        'max_open_vacancies' => 'integer',
    ];

    /**
     * =====================================================
     * RELATION
     * =====================================================
     * Admin terakhir yang mengubah setting.
     * =====================================================
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * =====================================================
     * HELPER
     * =====================================================
     * Cek apakah divisi memiliki batas quota.
     * =====================================================
     */
    public function hasLimit(): bool
    {
        return $this->max_open_vacancies !== null;
    }
}