<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * ======================================================================
 * MODEL: UserMagang
 * ======================================================================
 */
class UserMagang extends Authenticatable implements CanResetPasswordContract
{
    use Notifiable, CanResetPassword;


    // ======================================================================
    // KONFIGURASI TABEL
    // ======================================================================

    protected $table = 'users_magang';


    // ======================================================================
    // MASS ASSIGNMENT
    // ======================================================================

    protected $fillable = [
        'username',
        'email',
        'password_hash',
    ];


    // ======================================================================
    // HIDDEN
    // ======================================================================

    protected $hidden = [
        'password_hash',
        'remember_token', // ← ditambahkan
    ];


    // ======================================================================
    // CASTS
    // ======================================================================

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // ======================================================================
    // AUTH OVERRIDES
    // ======================================================================

    /**
     * Override kolom password default Laravel ('password' → 'password_hash').
     * Wajib ada agar Auth::attempt() dan Hash::check() membaca kolom yang benar.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }


    // ======================================================================
    // RELATIONS
    // ======================================================================

    /**
     * Profil lengkap peserta (NIM, institusi, CV, dll).
     */
    public function profile()
    {
        return $this->hasOne(ProfileMagang::class, 'user_id');
    }

    /**
     * Lamaran yang dipimpin peserta ini sebagai ketua.
     */
    public function applications()
    {
        return $this->hasMany(ApplicationMagang::class, 'leader_user_id');
    }

    /**
     * Keikutsertaan peserta dalam lamaran (sebagai anggota maupun ketua).
     */
    public function applicationMembers()
    {
        return $this->hasMany(ApplicationMemberMagang::class, 'user_id');
    }

    /**
     * Sertifikat yang dimiliki peserta.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'user_id');
    }


    // ======================================================================
    // ACCESSORS
    // ======================================================================

    /**
     * Nama tampilan — ambil dari profile jika sudah diisi, fallback ke username.
     *
     * CARA PAKAI DI BLADE:
     *   {{ $user->display_name }}
     *
     * Berguna di notifikasi, greeting, dan tabel admin
     * tanpa perlu eager load manual tiap kali.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->profile?->full_name ?? $this->username;
    }


    // ======================================================================
    // HELPERS
    // ======================================================================

    /**
     * Cek apakah profil sudah lengkap (termasuk CV).
     * Dipakai di ApplicationMagangController sebelum izinkan daftar.
     */
    public function hasCompleteProfile(): bool
    {
        return $this->profile && $this->profile->isComplete();
    }


    // ======================================================================
    // SCOPES
    // ======================================================================

    /**
     * Filter user yang profilenya berstatus aktif.
     *
     * CARA PAKAI:
     *   UserMagang::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->whereHas('profile', function ($q) {
            $q->whereNotNull('cv_file_path'); // profile dianggap aktif jika sudah upload CV
        });
    }
}
