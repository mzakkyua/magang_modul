<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates_magang';
    protected $fillable = ['user_id', 'title', 'file'];

    // Ubah tujuan relasinya ke model UserMagang
    public function user()
    {
        // Tolong sesuaikan nama 'UserMagang::class' jika model pemagangmu namanya berbeda (misal: PesertaMagang::class)
        return $this->belongsTo(UserMagang::class, 'user_id');
    }
}
