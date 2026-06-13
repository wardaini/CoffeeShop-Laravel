<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class EmployeeProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'employee_code', 'position', 'ktp_number',
        'ktp_photo', 'face_photo', 'base_salary',
        'verification_status', 'joined_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'joined_at' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($profile) {
            if (empty($profile->employee_code)) {
                $profile->employee_code = 'EMP-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getKtpPhotoUrlAttribute(): ?string
    {
        return $this->ktp_photo ? asset('storage/' . $this->ktp_photo) : null;
    }

    public function getFacePhotoUrlAttribute(): ?string
    {
        return $this->face_photo ? asset('storage/' . $this->face_photo) : null;
    }
}