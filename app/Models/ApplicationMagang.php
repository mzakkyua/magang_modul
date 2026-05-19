<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationMagang extends Model
{
    protected $table = 'applications_magang';

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
     * STATUS CONSTANT
     * =====================================================
     */
    const STATUS_COMPLETED = 'completed';
    const STATUS_INTERVIEW = 'interview';
    const STATUS_RESIGNED  = 'resigned';
    const STATUS_VERIFIED = 'verified';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PENDING = 'pending';

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
