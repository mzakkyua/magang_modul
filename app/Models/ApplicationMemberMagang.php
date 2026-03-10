<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationMemberMagang extends Model
{
    protected $table = 'application_members_magang';

    /**
     * =====================================================
     * TIMESTAMP
     * =====================================================
     * Tabel tidak menggunakan created_at / updated_at
     */
    public $timestamps = false;

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
    const STATUS_PENDING   = 'pending';
    const STATUS_ACTIVE    = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED  = 'rejected';

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
        return $this->hasOne(CertificateMagang::class, 'member_id');
    }

    /**
     * =====================================================
     * HELPER: STATUS CHECK
     * =====================================================
     */
    public function isCompleted()
    {
        return $this->individual_status === self::STATUS_COMPLETED;
    }
}
