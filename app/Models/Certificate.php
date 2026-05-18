<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ======================================================================
 * MODEL: Certificate
 * ======================================================================
 *
 * PERUBAHAN dari versi sebelumnya:
 *   - Tambah 'application_member_id' ke $fillable
 *   - Tambah relation applicationMember() → untuk akses data periode magang
 *   - Tambah scope forMember() → query per member
 *   - Accessor period_label → label periode yang mudah dibaca di view
 */
class Certificate extends Model
{
    protected $table = 'certificates_magang';

    /**
     * ==================================================================
     * FILLABLE
     * ==================================================================
     * 'file' SENGAJA tidak ada — set manual di controller untuk
     * mencegah path traversal via mass assignment.
     * ==================================================================
     */
    protected $fillable = [
        'user_id',
        'application_member_id',  // ← BARU: anchor ke periode magang spesifik
        'title',
        'file',
        'uploaded_by_admin_id',
        'uploaded_at',
        'replaced_at',
    ];

    /**
     * ==================================================================
     * HIDDEN — raw file path tidak boleh terekspos ke JSON / toArray
     * ==================================================================
     */
    protected $hidden = [
        'file',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'replaced_at' => 'datetime',
    ];


    // ======================================================================
    // RELATIONS
    // ======================================================================

    /** Peserta pemilik sertifikat */
    public function user()
    {
        return $this->belongsTo(UserMagang::class, 'user_id');
    }

    /** Admin yang mengupload */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_admin_id');
    }

    /**
     * Baris ApplicationMemberMagang yang terkait.
     * Dari sini bisa akses: ->applicationMember->application->vacancy
     */
    public function applicationMember()
    {
        return $this->belongsTo(ApplicationMemberMagang::class, 'application_member_id');
    }


    // ======================================================================
    // ACCESSORS
    // ======================================================================

    /** URL download aman — selalu lewat controller (ada ownership check) */
    public function getDownloadUrlAttribute(): string
    {
        return route('certificates.download', $this->id);
    }

    /** URL view inline */
    public function getViewUrlAttribute(): string
    {
        return route('certificates.view', $this->id);
    }

    /** Apakah pernah di-replace? */
    public function getIsReplacedAttribute(): bool
    {
        return !is_null($this->replaced_at);
    }

    /**
     * Label periode untuk ditampilkan di view peserta.
     * Contoh output: "IT • Apr 2026 – Mei 2026"
     * Fallback ke judul sertifikat jika relasi tidak di-eager-load.
     */
    public function getPeriodLabelAttribute(): string
    {
        $member = $this->relationLoaded('applicationMember')
            ? $this->applicationMember
            : null;

        if ($member && $member->relationLoaded('application')) {
            $vacancy = $member->application->relationLoaded('vacancy')
                ? $member->application->vacancy
                : null;

            if ($vacancy) {
                $start = \Carbon\Carbon::parse($vacancy->start_date)->translatedFormat('M Y');
                $end   = \Carbon\Carbon::parse($vacancy->end_date)->translatedFormat('M Y');
                return "{$vacancy->division_name} • {$start} – {$end}";
            }
        }

        return $this->title;
    }


    // ======================================================================
    // SCOPES
    // ======================================================================

    /** Sertifikat yang sudah diterbitkan (punya file) */
    public function scopeIssued(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNotNull('file');
    }

    /** Filter per member (periode magang) */
    public function scopeForMember(\Illuminate\Database\Eloquent\Builder $query, int $memberId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('application_member_id', $memberId);
    }

    /** Sertifikat tahun ini */
    public function scopeThisYear(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereYear('uploaded_at', now()->year);
    }
}
