<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price',
        'assigned_to', 'kitchen_status', 'kitchen_notes',
    ];

    public function getKitchenStatusLabelAttribute(): string
    {
        return match($this->kitchen_status) {
            'pending'    => '⏳ Menunggu',
            'processing' => '🔥 Sedang Dibuat',
            'ready'      => '✅ Siap',
            default      => $this->kitchen_status,
        };
    }

    public function getKitchenStatusColorAttribute(): string
    {
        return match($this->kitchen_status) {
            'pending'    => '#E8B860',
            'processing' => '#74b9ff',
            'ready'      => '#6fcf97',
            default      => '#8A7A6A',
        };
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }
}