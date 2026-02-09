<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileMagang extends Model
{
    protected $table = 'profiles_magang';

    protected $fillable = [
        'user_id', 'full_name', 'nim_nisn', 'institution_name','education_level',
        'major', 'phone_number', 'address','surat_rekomendasi_file','cv_file_path', 'proposal_file_path'
    ];

    public function user()
    {
        return $this->belongsTo(UserMagang::class, 'user_id');
    }
}