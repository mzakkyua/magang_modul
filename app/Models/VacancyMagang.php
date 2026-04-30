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
        // Hitung berapa kuota yang sedang terpakai (status aktif)
        $terpakai = $this->applications()
            ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
            ->count();

        $sisaKuota = $this->quota_slots - $terpakai;

        // Jika kuota habis, tutup. Jika masih ada, buka.
        if ($sisaKuota <= 0 && $this->status === 'open') {
            $this->update(['status' => 'closed']);
        } elseif ($sisaKuota > 0 && $this->status === 'closed') {
            $this->update(['status' => 'open']);
        }
    }

    /**
     * =====================================================
     * MENGHITUNG SISA KUOTA (UNTUK TAMPILAN)
     * =====================================================
     */
    public function getSisaKuota()
    {
        $terpakai = $this->active_applications_count ?? 0;

        $sisa = $this->quota_slots - $terpakai;

        return max($sisa, 0);
    }

    /**
     * =====================================================
     * HELPER METHODS
     * =====================================================
     */

    public function isOpen()
    {
        return $this->status === VacancyMagang::STATUS_OPEN;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
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
