<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'clock_in', 'clock_out',
        'clock_in_photo', 'clock_out_photo', 'status', 'notes',
    ];

    protected $casts = [
        'date'      => 'date',
        'clock_in'  => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getClockInPhotoUrlAttribute(): ?string
    {
        return $this->clock_in_photo ? asset('storage/' . $this->clock_in_photo) : null;
    }

    public function getClockOutPhotoUrlAttribute(): ?string
    {
        return $this->clock_out_photo ? asset('storage/' . $this->clock_out_photo) : null;
    }

    public function getWorkDurationAttribute(): ?string
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }

        $diff = $this->clock_in->diff($this->clock_out);
        return $diff->format('%H jam %I menit');
    }
}