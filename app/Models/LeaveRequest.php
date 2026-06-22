<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'start_date', 'end_date', 'total_days',
        'type', 'reason', 'status', 'approved_by', 'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'cuti'  => '🏖️ Cuti',
            'izin'  => '📋 Izin',
            'sakit' => '🏥 Sakit',
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'  => '⏳ Menunggu',
            'approved' => '✅ Disetujui',
            'rejected' => '❌ Ditolak',
            default    => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'  => '#E8B860',
            'approved' => '#6fcf97',
            'rejected' => '#e07070',
            default    => '#8A7A6A',
        };
    }
}