<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagangAccessRight extends Model
{
    protected $table = 'magang_access_rights';

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'user_id',
        'role',
        'division_name'
    ];

    /**
     * =====================================================
     * ROLE CONSTANT
     * =====================================================
     */
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_DIVISION_ADMIN = 'admin divisi';

    /**
     * =====================================================
     * RELATION: USER
     * =====================================================
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * =====================================================
     * HELPER: ROLE CHECK
     * =====================================================
     */
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isDivisionAdmin()
    {
        return $this->role === self::ROLE_DIVISION_ADMIN;
    }
}
