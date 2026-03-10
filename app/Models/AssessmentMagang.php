<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentMagang extends Model
{
    protected $table = 'assessments_magang';

    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'member_id',
        'assessor_name',
        'score_behavior',
        'score_discipline',
        'score_performance',
        'evaluation_notes',
        'additional_notes'
    ];

    /**
     * =====================================================
     * CASTING
     * =====================================================
     */
    protected $casts = [
        'score_behavior'    => 'integer',
        'score_discipline'  => 'integer',
        'score_performance' => 'integer',
        'final_score'       => 'float'
    ];

    /**
     * =====================================================
     * AUTO CALCULATE FINAL SCORE
     * =====================================================
     */
    protected static function booted()
    {
        static::saving(function ($model) {

            $scores = [
                $model->score_behavior,
                $model->score_discipline,
                $model->score_performance
            ];

            $model->final_score = round(array_sum($scores) / 3, 2);
        });
    }

    /**
     * =====================================================
     * RELATION: MEMBER
     * =====================================================
     */
    public function member()
    {
        return $this->belongsTo(ApplicationMemberMagang::class, 'member_id');
    }

    /**
     * =====================================================
     * HELPER: CHECK PASS
     * =====================================================
     */
    public function isPassed()
    {
        return $this->final_score >= 70;
    }
}
