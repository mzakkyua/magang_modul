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
        'individual_status',
    ];

    /**
     * =====================================================
     * STATUS CONSTANT
     * =====================================================
     */
    public const STATUS_ACTIVE      = 'active';
    public const STATUS_DROPPED_OUT = 'dropped_out';
    public const STATUS_FINISHED    = 'finished';


    // =====================================================
    // RELATIONS
    // =====================================================

    public function application()
    {
        return $this->belongsTo(ApplicationMagang::class, 'application_id');
    }

    public function user()
    {
        return $this->belongsTo(UserMagang::class, 'user_id');
    }

    public function assessment()
    {
        return $this->hasOne(AssessmentMagang::class, 'member_id');
    }

    /**
     * Sertifikat untuk periode magang ini.
     *
     * PERUBAHAN dari versi sebelumnya:
     *   LAMA: hasOne(Certificate, 'user_id', 'user_id')
     *         → join pakai user_id saja, tidak membedakan periode.
     *         → Jika user magang 2x, relation selalu return cert yang sama.
     *
     *   BARU: hasOne(Certificate, 'application_member_id')
     *         → satu baris member = satu sertifikat untuk periode itu saja.
     *         → Magang 2x = 2 member record = 2 sertifikat terpisah. ✅
     */
    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'application_member_id');
    }


    // =====================================================
    // HELPERS
    // =====================================================

    public function isFinished(): bool
    {
        return $this->individual_status === self::STATUS_FINISHED;
    }
}
