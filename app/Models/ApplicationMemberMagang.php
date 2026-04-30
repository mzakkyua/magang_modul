<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationMemberMagang extends Model
{
    protected $table = 'application_members_magang';

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'application_id',
        'user_id',
        'individual_status'
    ];

    /**
     * =====================================================
     * STATUS CONSTANT
     * =====================================================
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DROPPED_OUT = 'dropped_out';
    public const STATUS_FINISHED = 'finished';

    /**
     * =====================================================
     * RELATION: APPLICATION
     * =====================================================
     */
    public function application()
    {
        return $this->belongsTo(ApplicationMagang::class, 'application_id');
    }

    /**
     * =====================================================
     * RELATION: USER
     * =====================================================
     */
    public function user()
    {
        return $this->belongsTo(UserMagang::class, 'user_id');
    }

    /**
     * =====================================================
     * RELATION: ASSESSMENT
     * =====================================================
     * Penilaian individu peserta
     */
    public function assessment()
    {
        return $this->hasOne(AssessmentMagang::class, 'member_id');
    }

    /**
     * =====================================================
     * RELATION: CERTIFICATE
     * =====================================================
     * Sertifikat setelah magang selesai
     */
    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'user_id', 'user_id');
    }

    /**
     * =====================================================
     * HELPER: STATUS CHECK
     * =====================================================
     */
    public function isFinished(): bool
    {
        return $this->individual_status === self::STATUS_FINISHED;
    }
}
