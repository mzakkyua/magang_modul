<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateMagang extends Model
{
    protected $table = 'certificates_magang';
    public $timestamps = false; // Kita pakai uploaded_at manual

    protected $fillable = [
        'member_id', 'certificate_number', 'file_path', 'uploaded_at'
    ];
    
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uploaded_at = now();
        });
    }

    public function member()
    {
        return $this->belongsTo(ApplicationMemberMagang::class, 'member_id');
    }
}