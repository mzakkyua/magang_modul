<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentMagang extends Model
{
    protected $table = 'assessments_magang';

    protected $fillable = [
        'member_id', 'assessor_name', 
        'score_behavior', 'score_discipline', 'score_performance',
        'final_score', 'evaluation_notes', 'additional_notes'
    ];

    public function member()
    {
        return $this->belongsTo(ApplicationMemberMagang::class, 'member_id');
    }
}