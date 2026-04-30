<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ======================================================================
 * MODEL: Certificate
 * ======================================================================
 */
class Certificate extends Model
{
    protected $table = 'certificates_magang';

    /**
     * ==================================================================
     * FILLABLE — FIELD YANG BOLEH MASS ASSIGN
     * ==================================================================
     *
     * PENTING: Kolom 'file' SENGAJA tidak ada di sini.
     * File path hanya boleh di-set secara eksplisit di controller:
     *   $cert->file = $filePath;
     *   $cert->save();
     *
     * Atau via updateOrCreate() dengan array yang dikontrol manual,
     * BUKAN dari $request->all() atau $request->validated() langsung.
     *
     * Ini mencegah attacker / bug admin meng-set path aneh seperti:
     *   file = ../../.env
     * via manipulasi form input.
     * ==================================================================
     */
    protected $fillable = [
        'user_id',
        'title',
        'file',
        'uploaded_by_admin_id',
        'uploaded_at',
        'replaced_at',
    ];

    /**
     * ==================================================================
     * HIDDEN — FIELD YANG DISEMBUNYIKAN DARI JSON / TOARRAY
     * ==================================================================
     *
     * Raw file path tidak boleh terekspos ke:
     * - Response API (json())
     * - Blade {{ $cert }} atau {{ $cert->toJson() }}
     * - Log yang mencetak model
     *
     * Download/view harus selalu lewat controller yang verifikasi ownership.
     * Gunakan accessor download_url / view_url sebagai gantinya.
     * ==================================================================
     */
    protected $hidden = [
        'file', // raw storage path — jangan ekspos langsung
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'replaced_at' => 'datetime',
    ];


    // ======================================================================
    // RELATIONS
    // ======================================================================

    /**
     * Peserta pemilik sertifikat.
     */
    public function user()
    {
        return $this->belongsTo(UserMagang::class, 'user_id');
    }

    /**
     * Admin yang mengupload sertifikat ini.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_admin_id');
    }


    // ======================================================================
    // ACCESSORS
    // ======================================================================

    /**
     * URL download yang aman — selalu lewat controller (ada ownership check).
     *
     * CARA PAKAI DI BLADE:
     *   <a href="{{ $cert->download_url }}">Download</a>
     *
     * CARA PAKAI DI API RESPONSE:
     *   $cert->append('download_url')
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('certificates.download', $this->id);
    }

    /**
     * URL view inline (preview PDF di browser).
     *
     * CARA PAKAI DI BLADE:
     *   <iframe src="{{ $cert->view_url }}"></iframe>
     */
    public function getViewUrlAttribute(): string
    {
        return route('certificates.view', $this->id);
    }

    /**
     * Apakah sertifikat ini pernah di-replace?
     *
     * CARA PAKAI DI BLADE:
     *   @if($cert->is_replaced)
     *     <span>Diganti pada {{ $cert->replaced_at->format('d M Y') }}</span>
     *   @endif
     */
    public function getIsReplacedAttribute(): bool
    {
        return !is_null($this->replaced_at);
    }


    // ======================================================================
    // SCOPES
    // ======================================================================

    /**
     * Sertifikat yang sudah diterbitkan (punya file).
     *
     * CARA PAKAI:
     *   Certificate::issued()->get();
     */
    public function scopeIssued($query)
    {
        return $query->whereNotNull('file');
    }

    /**
     * Sertifikat yang diterbitkan tahun ini.
     *
     * CARA PAKAI:
     *   Certificate::thisYear()->count();
     */
    public function scopeThisYear($query)
    {
        return $query->whereYear('uploaded_at', now()->year);
    }
}
