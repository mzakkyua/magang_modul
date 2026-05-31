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
     *
     * Semua status application yang dianggap:
     * - masih aktif
     * - masih menggunakan slot kuota
     *
     * Tujuan:
     * Menghindari duplicate magic string di banyak tempat.
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
        /**
         * -----------------------------------------------------
         * HITUNG KUOTA TERPAKAI
         * -----------------------------------------------------
         *
         * Hanya status aktif yang mengambil slot kuota.
         */
        $terpakai = $this->applications()
            ->whereIn(
                'status',
                self::ACTIVE_APPLICATION_STATUSES
            )
            ->count();

        $sisaKuota = $this->quota_slots - $terpakai;

        /**
         * -----------------------------------------------------
         * AUTO CLOSE / OPEN
         * -----------------------------------------------------
         */
        if (
            $sisaKuota <= 0 &&
            $this->status === self::STATUS_OPEN
        ) {

            $this->update([
                'status' => self::STATUS_CLOSED
            ]);
        } elseif (
            $sisaKuota > 0 &&
            $this->status === self::STATUS_CLOSED
        ) {

            $this->update([
                'status' => self::STATUS_OPEN
            ]);
        }
    }

    /**
     * =====================================================
     * MENGHITUNG SISA KUOTA
     * =====================================================
     *
     * Optimisasi:
     * - Jika active_applications_count tersedia
     *   → gunakan hasil withCount()
     *
     * Fallback:
     * - Jika model tidak di-load dengan withCount()
     *   → query langsung ke database
     *
     * Tujuan:
     * Menghindari silent bug yang menyebabkan
     * sisa kuota selalu dianggap penuh.
     * =====================================================
     */
    public function getSisaKuota(): int
    {
        /**
         * -------------------------------------------------
         * FAST PATH
         * -------------------------------------------------
         *
         * Gunakan hasil eager load withCount()
         * jika tersedia.
         */
        if (isset($this->active_applications_count)) {

            return max(
                $this->quota_slots - $this->active_applications_count,
                0
            );
        }

        /**
         * -------------------------------------------------
         * FALLBACK QUERY
         * -------------------------------------------------
         *
         * Lebih lambat, tapi menjamin data akurat
         * jika withCount() tidak digunakan.
         */
        $terpakai = $this->applications()
            ->whereIn(
                'status',
                self::ACTIVE_APPLICATION_STATUSES
            )
            ->count();

        return max(
            $this->quota_slots - $terpakai,
            0
        );
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
