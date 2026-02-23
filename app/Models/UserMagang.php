<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Trait & Contract untuk fitur reset password
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class UserMagang extends Authenticatable implements CanResetPasswordContract
{
    use Notifiable, CanResetPassword;

    /**
     * Nama tabel khusus untuk peserta magang
     */
    protected $table = 'users_magang';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'username',
        'email',
        'password_hash',
    ];

    /**
     * Mapping field password custom.
     * Laravel default cari kolom 'password',
     * tapi kita pakai 'password_hash'.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Relasi ke tabel profile_magang
     */
    public function profile()
    {
        return $this->hasOne(ProfileMagang::class, 'user_id');
    }
}
