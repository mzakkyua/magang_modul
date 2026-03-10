<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationMagang extends Model
{
    protected $table = 'applications_magang';

    // Matikan timestamp default (created_at/updated_at) 
    // karena di migration kita cuma buat 'submission_date' manual
    public $timestamps = false;

    protected $fillable = [
        'vacancy_id',
        'leader_user_id',
        'research_title',
        'research_abstract',
        'submission_date',
        'status',
        'admin_feedback'
    ];

    protected $casts = [
        'submission_date' => 'datetime',
    ];

    // Agar submission_date otomatis terisi saat create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->submission_date = now();
        });
    }

    /**
     * Relasi ke Lowongan
     */
    public function vacancy()
    {
        return $this->belongsTo(VacancyMagang::class, 'vacancy_id');
    }

    /**
     * Ketua kelompok
     */
    public function leader()
    {
        return $this->belongsTo(UserMagang::class, 'leader_user_id');
    }

    /**
     * Semua anggota
     */
    public function members()
    {
        return $this->hasMany(ApplicationMemberMagang::class, 'application_id');
    }
}
