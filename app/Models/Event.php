<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * =====================================================
     * MASS ASSIGNMENT
     * =====================================================
     */
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'color'
    ];

    /**
     * =====================================================
     * CASTING
     * =====================================================
     * Agar tanggal menjadi object Carbon
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    /**
     * =====================================================
     * HELPER: CHECK EVENT STATUS
     * =====================================================
     */

    public function isPast()
    {
        return $this->end_date < now();
    }

    public function isOngoing()
    {
        return now()->between($this->start_date, $this->end_date);
    }

    public function isUpcoming()
    {
        return $this->start_date > now();
    }
}
