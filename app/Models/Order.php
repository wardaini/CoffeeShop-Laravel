<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code', 'user_id', 'customer_name', 'customer_email',
        'customer_phone', 'notes', 'total_price', 'status',
        'order_type', 'take_away_method', 'table_number',
        'delivery_address', 'payment_method', 'payment_status', 'delivery_fee',
    ];

    protected $casts = [
        'total_price'  => 'decimal:2',
        'delivery_fee' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            $order->order_code = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8));
        });

        static::created(function ($order) {
            if ($order->take_away_method === 'delivery') {
                $order->delivery()->create(['status' => 'waiting']);
            }
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getFormattedDeliveryFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->delivery_fee, 0, ',', '.');
    }

    public function getGrandTotalAttribute(): float
    {
        return $this->total_price + $this->delivery_fee;
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return match($this->order_type) {
            'dine_in'  => 'Dine In' . ($this->table_number ? ' - Meja ' . $this->table_number : ''),
            'take_away'=> $this->take_away_method === 'delivery' ? 'Take Away - Delivery' : 'Take Away - Ambil Sendiri',
            'mixed'    => 'Dine In & Take Away' . ($this->table_number ? ' - Meja ' . $this->table_number : ''),
            default    => ucfirst($this->order_type ?? '-'),
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'qris'      => 'QRIS',
            'dana'      => 'Transfer DANA',
            'ovo'       => 'Transfer OVO',
            'bsi'       => 'Transfer Bank BSI',
            'bank_aceh' => 'Transfer Bank Aceh',
            'cash'      => 'Cash / Tunai',
            default     => $this->payment_method,
        };
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
}