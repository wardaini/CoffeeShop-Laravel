<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone',
        'photo', 'is_active', 'courier_status', 'last_active_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_active_at'    => 'datetime',
        ];
    }

    public function isAvailable(): bool
    {
        return $this->courier_status === 'available';
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function cleaningSchedules()
    {
        return $this->hasMany(CleaningSchedule::class, 'assigned_to');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(UserNotification::class)->where('is_read', false);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

   

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Helper role check
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isPelanggan(): bool { return $this->role === 'pelanggan'; }
    public function isKaryawan(): bool { return $this->role === 'karyawan'; }
    public function isBos(): bool { return $this->role === 'bos'; }
    public function isIt(): bool { return $this->role === 'it'; }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-avatar.png');
    }
}