<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'month', 'year', 'base_salary', 'total_present',
        'bonus', 'deduction', 'total_salary', 'status', 'paid_at', 'notes',
    ];

    protected $casts = [
        'base_salary'  => 'decimal:2',
        'bonus'        => 'decimal:2',
        'deduction'    => 'decimal:2',
        'total_salary' => 'decimal:2',
        'paid_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPeriodLabelAttribute(): string
    {
        $bulan = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni',
            7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];

        return ($bulan[$this->month] ?? $this->month) . ' ' . $this->year;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_salary, 0, ',', '.');
    }

    public function getFormattedBaseAttribute(): string
    {
        return 'Rp ' . number_format($this->base_salary, 0, ',', '.');
    }

    public function getFormattedBonusAttribute(): string
    {
        return 'Rp ' . number_format($this->bonus, 0, ',', '.');
    }

    public function getFormattedDeductionAttribute(): string
    {
        return 'Rp ' . number_format($this->deduction, 0, ',', '.');
    }
}