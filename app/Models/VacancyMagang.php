<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacancyMagang extends Model
{
    protected $table = 'vacancies_magang';

    protected $fillable = [
        'title', 
        'division_name', 
        'type', 
        'registration_mode',
        'quota_slots',
        'min_members', 
        'max_members', 
        'start_date', 
        'end_date',
        'description', 
        'status'
    ];

    // Relasi: Satu lowongan punya banyak lamaran
    public function applications()
    {
        return $this->hasMany(ApplicationMagang::class, 'vacancy_id');
    }
}