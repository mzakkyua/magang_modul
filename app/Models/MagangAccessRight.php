<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagangAccessRight extends Model
{
    protected $table = 'magang_access_rights';
    
    protected $fillable = ['user_id', 'role', 'division_name'];

    // Relasi balik ke tabel User Pegawai
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}