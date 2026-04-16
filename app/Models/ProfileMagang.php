<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileMagang extends Model
{
    protected $table = 'profiles_magang';

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'nim_nisn',
        'institution_name',
        'education_level',
        'major',
        'phone_number',
        'address',
        'surat_rekomendasi_file',
        'cv_file_path',
        'proposal_file_path'
    ];

    /**
     * =====================================================
     * CASTING
     * =====================================================
     */
    protected $casts = [
        'phone_number' => 'string',
    ];

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
     * HELPER: PROFILE COMPLETENESS
     * =====================================================
     * Fungsi ini sekarang mengecek SEMUA kolom wajib
     */
    public function isComplete()
    {
        return $this->full_name &&
            $this->nim_nisn &&
            $this->institution_name &&
            $this->education_level &&
            $this->major &&
            $this->phone_number &&
            $this->address &&
            $this->cv_file_path; // CV Wajib, Proposal opsional
    }
}
