<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationMemberMagang extends Model
{
    protected $table = 'application_members_magang';
    public $timestamps = false;

    protected $fillable = [
        'application_id', 'user_id', 'individual_status'
    ];

    public function application()
    {
        return $this->belongsTo(ApplicationMagang::class, 'application_id');
    }

    public function user()
    {
        return $this->belongsTo(UserMagang::class, 'user_id');
    }

    // Relasi ke Penilaian
    public function assessment()
    {
        return $this->hasOne(AssessmentMagang::class, 'member_id');
    }
    
    // Relasi ke Sertifikat
    public function certificate()
    {
        return $this->hasOne(CertificateMagang::class, 'member_id');
    }
}