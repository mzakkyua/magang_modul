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

    protected static function booted(): void
    {
        $clearCache = function () {
            \Illuminate\Support\Facades\Cache::forget('landing_division_stats');
            \Illuminate\Support\Facades\Cache::forget('landing_vacancies_magang');
            \Illuminate\Support\Facades\Cache::forget('landing_vacancies_penelitian');
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }

    /**
     * =====================================================
     * CONSTANT DOMAIN VALUE
     * =====================================================
     */
    const TYPE_PENELITIAN = 'penelitian';
    const TYPE_RESEARCH = 'penelitian';
    const TYPE_MAGANG = 'magang';

    const MODE_INDIVIDUAL = 'individu';
    const MODE_KELOMPOK = 'kelompok';
    const MODE_INDIVIDU = 'individu';
    const MODE_GROUP = 'kelompok';
    const MODE_HYBRID = 'hybrid';

    const STATUS_ARCHIVED = 'archived';
    const STATUS_CLOSED = 'closed';
    const STATUS_OPEN = 'open';

    /**
     * =====================================================
     * STATUS APLIKASI YANG MENGAMBIL KUOTA
     * =====================================================
     */
    const ACTIVE_APPLICATION_STATUSES = [
        'pending',
        'verified',
        'interview',
        'accepted',
    ];

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
     * =========================================================
     * FUNGSI PINTAR: CEK & UPDATE STATUS KUOTA OTOMATIS
     * =========================================================
     */
    public function updateStatusBasedOnQuota()
    {
        $terpakai = $this->applications()
            ->whereIn('status', self::ACTIVE_APPLICATION_STATUSES)
            ->count();

        $sisaKuota = $this->quota_slots - $terpakai;

        if ($sisaKuota <= 0 && $this->status === self::STATUS_OPEN) {
            $this->update(['status' => self::STATUS_CLOSED]);
        } elseif ($sisaKuota > 0 && $this->status === self::STATUS_CLOSED) {
            $this->update(['status' => self::STATUS_OPEN]);
        }
    }

    /**
     * =====================================================
     * MENGHITUNG SISA KUOTA
     * =====================================================
     */
    public function getSisaKuota(): int
    {
        if (isset($this->active_applications_count)) {
            return max($this->quota_slots - $this->active_applications_count, 0);
        }

        $terpakai = $this->applications()
            ->whereIn('status', self::ACTIVE_APPLICATION_STATUSES)
            ->count();

        return max($this->quota_slots - $terpakai, 0);
    }

    /**
     * =====================================================
     * HELPER METHODS
     * =====================================================
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function hasStarted(): bool
    {
        return now()->gte($this->start_date);
    }

    public function hasEnded(): bool
    {
        return now()->gt($this->end_date);
    }
}
