<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationMagang extends Model
{
    protected $table = 'applications_magang';

    /**
     * =====================================================
     * TIMESTAMP
     * =====================================================
     * Tabel tidak menggunakan created_at / updated_at
     * karena hanya menggunakan submission_date.
     */
    public $timestamps = false;

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'vacancy_id',
        'leader_user_id',
        'research_title',
        'research_abstract',
        'status',
        'admin_feedback'
    ];

    /**
     * =====================================================
     * CASTING
     * =====================================================
     */
    protected $casts = [
        'submission_date' => 'datetime',
    ];

    /**
     * =====================================================
     * STATUS CONSTANT
     * =====================================================
     */
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_INTERVIEW = 'interview';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    /**
     * =====================================================
     * AUTO SET SUBMISSION DATE
     * =====================================================
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->submission_date = now();
        });
    }

    /**
     * =====================================================
     * RELATION: VACANCY
     * =====================================================
     */
    public function vacancy()
    {
        return $this->belongsTo(VacancyMagang::class);
    }

    /**
     * =====================================================
     * RELATION: LEADER
     * =====================================================
     */
    public function leader()
    {
        return $this->belongsTo(UserMagang::class, 'leader_user_id');
    }

    /**
     * =====================================================
     * RELATION: MEMBERS
     * =====================================================
     */
    public function members()
    {
        return $this->hasMany(ApplicationMemberMagang::class, 'application_id');
    }
}
