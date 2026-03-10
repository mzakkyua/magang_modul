<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacancyMagang extends Model
{
    protected $table = 'vacancies_magang';

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'title',
        'division_name',
        'type',
        'registration_mode',
        'quota_slots',
        'min_members',
        'max_members',
        'start_date',
        'end_date',
        'description',
        'status'
    ];

    /**
     * =====================================================
     * CASTING
     * =====================================================
     */
    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'quota_slots' => 'integer',
        'min_members' => 'integer',
        'max_members' => 'integer',
    ];

    /**
     * =====================================================
     * CONSTANT DOMAIN VALUE
     * =====================================================
     */
    const TYPE_MAGANG = 'magang';
    const TYPE_RESEARCH = 'penelitian';

    const MODE_INDIVIDUAL = 'individu';
    const MODE_GROUP = 'kelompok';
    const MODE_HYBRID = 'hybrid';

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    /**
     * =====================================================
     * RELATION: APPLICATIONS
     * =====================================================
     */
    public function applications()
    {
        return $this->hasMany(ApplicationMagang::class, 'vacancy_id');
    }

    /**
     * =====================================================
     * HELPER METHODS
     * =====================================================
     */

    public function isOpen()
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function hasStarted()
    {
        return now()->gte($this->start_date);
    }

    public function hasEnded()
    {
        return now()->gt($this->end_date);
    }
}
