<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'courier_id', 'status',
        'assigned_at', 'picked_up_at', 'delivered_at', 'notes',
    ];

    protected $casts = [
        'assigned_at'  => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'waiting'    => 'Menunggu Kurir',
            'assigned'   => 'Ditugaskan',
            'on_the_way' => 'Sedang Diantar',
            'delivered'  => 'Terkirim',
            'failed'     => 'Gagal',
            default      => $this->status,
        };
    }
}