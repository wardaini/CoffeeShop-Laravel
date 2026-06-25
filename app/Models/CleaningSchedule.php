<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleaningSchedule extends Model
{
    protected $fillable = [
        'area', 'assigned_to', 'frequency', 'status',
        'scheduled_date', 'completed_at', 'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_at'   => 'datetime',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match($this->frequency) {
            'daily'     => 'Harian',
            'per_shift' => 'Per Shift',
            'weekly'    => 'Mingguan',
            default     => $this->frequency,
        };
    }
}