<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\MagangAccessRight;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
    ];

    /**
     * =====================================================
     * HIDDEN ATTRIBUTE
     * =====================================================
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * =====================================================
     * ATTRIBUTE CASTING
     * =====================================================
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * =====================================================
     * RELATION: MAGANG ACCESS RIGHT
     * =====================================================
     */
    public function magangAccessRight()
    {
        return $this->hasOne(MagangAccessRight::class, 'user_id');
    }

    /**
     * =====================================================
     * HELPER: ROLE CHECK
     * =====================================================
     */
    public function isSuperAdmin()
    {
        return $this->magangAccessRight?->role === 'superadmin';
    }

    public function isDivisionAdmin()
    {
        return $this->magangAccessRight?->role === MagangAccessRight::ROLE_DIVISION_ADMIN;
    }
}
