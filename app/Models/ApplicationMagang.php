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
        'vacancy_id', 'leader_user_id', 'research_title', 
        'submission_date', 'status', 'admin_feedback'
    ];
    
    // Agar submission_date otomatis terisi saat create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->submission_date = now();
        });
    }

    public function vacancy()
    {
        return $this->belongsTo(VacancyMagang::class, 'vacancy_id');
    }

    public function leader()
    {
        return $this->belongsTo(UserMagang::class, 'leader_user_id');
    }

    // Relasi ke Anggota
    public function members()
    {
        return $this->hasMany(ApplicationMemberMagang::class, 'application_id');
    }
}