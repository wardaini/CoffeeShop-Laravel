<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $kopi    = Category::where('name', 'Kopi')->first();
        $nonKopi = Category::where('name', 'Non-Kopi')->first();
        $makanan = Category::where('name', 'Makanan')->first();

        $products = [
            ['category_id' => $kopi->id,    'name' => 'Espresso',        'price' => 18000, 'is_featured' => true],
            ['category_id' => $kopi->id,    'name' => 'Cappuccino',      'price' => 25000, 'is_featured' => true],
            ['category_id' => $kopi->id,    'name' => 'Caramel Latte',   'price' => 28000, 'is_featured' => true],
            ['category_id' => $kopi->id,    'name' => 'Cold Brew',       'price' => 30000, 'is_featured' => false],
            ['category_id' => $nonKopi->id, 'name' => 'Matcha Latte',    'price' => 27000, 'is_featured' => true],
            ['category_id' => $nonKopi->id, 'name' => 'Taro Milk Tea',   'price' => 25000, 'is_featured' => false],
            ['category_id' => $makanan->id, 'name' => 'Croissant',       'price' => 20000, 'is_featured' => false],
            ['category_id' => $makanan->id, 'name' => 'Cheesecake',      'price' => 35000, 'is_featured' => true],
        ];

        foreach ($products as $p) {
            Product::create(array_merge($p, ['description' => 'Deskripsi produk ' . $p['name']]));
        }
    }
}