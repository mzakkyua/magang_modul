<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class UserMagang extends Authenticatable implements CanResetPasswordContract
{
    use Notifiable, CanResetPassword;

    /**
     * =====================================================
     * TABLE NAME
     * =====================================================
     */
    protected $table = 'users_magang';

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'username',
        'email',
        'password_hash',
    ];

    /**
     * =====================================================
     * HIDDEN ATTRIBUTE
     * =====================================================
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * =====================================================
     * ATTRIBUTE CASTING
     * =====================================================
     */
    protected $casts = [
        'password_hash' => 'hashed'
    ];

    /**
     * =====================================================
     * CUSTOM PASSWORD COLUMN
     * =====================================================
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * =====================================================
     * RELATION: PROFILE
     * =====================================================
     */
    public function profile()
    {
        return $this->hasOne(ProfileMagang::class, 'user_id');
    }

    /**
     * =====================================================
     * RELATION: APPLICATION AS LEADER
     * =====================================================
     */
    public function applications()
    {
        return $this->hasMany(ApplicationMagang::class, 'leader_user_id');
    }

    /**
     * =====================================================
     * RELATION: APPLICATION MEMBER
     * =====================================================
     */
    public function memberOf()
    {
        return $this->hasMany(ApplicationMemberMagang::class, 'user_id');
    }

    /**
     * =====================================================
     * HELPER: PROFILE COMPLETENESS
     * =====================================================
     */
    public function hasCompleteProfile()
    {
        return $this->profile && $this->profile->isComplete();
    }

    /**
     * Relasi ke ApplicationMemberMagang
     * Menghubungkan peserta dengan lamaran (baik individu maupun kelompok)
     */
    public function applicationMembers()
    {
        return $this->hasMany(\App\Models\ApplicationMemberMagang::class, 'user_id');
    }
}
