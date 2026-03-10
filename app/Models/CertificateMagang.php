<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateMagang extends Model
{
    protected $table = 'certificates_magang';

    /**
     * =====================================================
     * TIMESTAMP
     * =====================================================
     * Tabel tidak menggunakan created_at / updated_at
     * karena menggunakan uploaded_at manual.
     */
    public $timestamps = false;

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'member_id',
        'certificate_number',
        'file_path'
    ];

    /**
     * =====================================================
     * CASTING
     * =====================================================
     */
    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * =====================================================
     * AUTO SET UPLOADED DATE
     * =====================================================
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uploaded_at = now();
        });
    }

    /**
     * =====================================================
     * RELATION: MEMBER
     * =====================================================
     */
    public function member()
    {
        return $this->belongsTo(ApplicationMemberMagang::class, 'member_id');
    }

    /**
     * =====================================================
     * HELPER: CHECK FILE EXISTENCE
     * =====================================================
     */
    public function hasFile()
    {
        return !empty($this->file_path);
    }
}
