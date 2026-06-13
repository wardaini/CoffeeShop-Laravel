<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'image', 'icon', 'is_available', 'is_featured',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_featured'  => 'boolean',
        'price'        => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Icon/emoji unik per produk. Jika tidak diset manual,
     * gunakan default berdasarkan nama produk.
     */
    public function getDisplayIconAttribute(): string
    {
        if ($this->icon) {
            return $this->icon;
        }

        $name = strtolower($this->name);

        $map = [
            'espresso'     => '☕',
            'americano'    => '☕',
            'cappuccino'   => '🥛',
            'latte'        => '🥛',
            'macchiato'    => '🍮',
            'mocha'        => '🍫',
            'cold brew'    => '🧊',
            'es kopi'      => '🧊',
            'matcha'       => '🍵',
            'taro'         => '🟣',
            'choco'        => '🍫',
            'milk tea'     => '🧋',
            'croissant'    => '🥐',
            'cheesecake'   => '🍰',
            'donat'        => '🍩',
            'roti'         => '🍞',
            'sandwich'     => '🥪',
            'pasta'        => '🍝',
            'nasi'         => '🍚',
            'kentang'      => '🍟',
            'french fries' => '🍟',
            'nugget'       => '🍗',
            'pisang'       => '🍌',
            'waffle'       => '🧇',
            'pancake'      => '🥞',
            'smoothie'     => '🥤',
            'jus'          => '🥤',
            'juice'        => '🥤',
            'soda'         => '🥤',
            'lemon'        => '🍋',
            'cookies'      => '🍪',
            'brownies'     => '🍫',
            'pudding'      => '🍮',
        ];

        foreach ($map as $keyword => $emoji) {
            if (str_contains($name, $keyword)) {
                return $emoji;
            }
        }

        // fallback berdasarkan kategori
        return match($this->category->name ?? '') {
            'Kopi'     => '☕',
            'Non-Kopi' => '🥤',
            'Makanan'  => '🍽️',
            'Snack'    => '🍪',
            default    => '☕',
        };
    }
}