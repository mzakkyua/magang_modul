<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // <-- Perhatikan ini
use Illuminate\Notifications\Notifiable;

class UserMagang extends Authenticatable
{
    use Notifiable;

    // Kasih tahu Laravel nama tabel aslinya
    protected $table = 'users_magang';

    protected $fillable = [
        'username', 'email', 'password_hash',
    ];

    // Laravel minta 'password', tapi kita punya 'password_hash'
    // Kita harus mapping biar Auth jalan
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Relasi ke Profil
    public function profile()
    {
        return $this->hasOne(ProfileMagang::class, 'user_id');
    }
        public function getNamaPesertaAttribute()
{
    return $this->profile->full_name ?? 'Peserta';
}

    
}