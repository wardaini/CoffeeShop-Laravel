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
        $snack   = Category::where('name', 'Snack')->first();

        $products = [
            // ===== KOPI =====
            ['category_id' => $kopi->id, 'name' => 'Espresso', 'price' => 18000, 'icon' => '☕', 'is_featured' => true, 'description' => 'Kopi hitam pekat dengan rasa kuat dan aroma intens.'],
            ['category_id' => $kopi->id, 'name' => 'Americano', 'price' => 20000, 'icon' => '☕', 'is_featured' => false, 'description' => 'Espresso dengan tambahan air panas, lebih ringan.'],
            ['category_id' => $kopi->id, 'name' => 'Cappuccino', 'price' => 25000, 'icon' => '🥛', 'is_featured' => true, 'description' => 'Espresso, susu panas, dan foam susu yang lembut.'],
            ['category_id' => $kopi->id, 'name' => 'Caramel Latte', 'price' => 28000, 'icon' => '🍯', 'is_featured' => true, 'description' => 'Latte dengan sirup karamel manis legit.'],
            ['category_id' => $kopi->id, 'name' => 'Vanilla Latte', 'price' => 28000, 'icon' => '🥛', 'is_featured' => false, 'description' => 'Latte dengan aroma vanilla yang menenangkan.'],
            ['category_id' => $kopi->id, 'name' => 'Hazelnut Latte', 'price' => 29000, 'icon' => '🌰', 'is_featured' => false, 'description' => 'Latte dengan sirup hazelnut yang harum.'],
            ['category_id' => $kopi->id, 'name' => 'Mocha', 'price' => 27000, 'icon' => '🍫', 'is_featured' => false, 'description' => 'Kombinasi espresso, cokelat, dan susu.'],
            ['category_id' => $kopi->id, 'name' => 'Cold Brew', 'price' => 30000, 'icon' => '🧊', 'is_featured' => true, 'description' => 'Kopi diseduh dingin selama 12 jam, rasa smooth.'],
            ['category_id' => $kopi->id, 'name' => 'Es Kopi Susu Gula Aren', 'price' => 22000, 'icon' => '🧊', 'is_featured' => true, 'description' => 'Kopi susu khas Indonesia dengan gula aren.'],
            ['category_id' => $kopi->id, 'name' => 'Affogato', 'price' => 32000, 'icon' => '🍨', 'is_featured' => false, 'description' => 'Es krim vanilla disiram espresso panas.'],

            // ===== NON-KOPI =====
            ['category_id' => $nonKopi->id, 'name' => 'Matcha Latte', 'price' => 27000, 'icon' => '🍵', 'is_featured' => true, 'description' => 'Bubuk matcha premium dengan susu creamy.'],
            ['category_id' => $nonKopi->id, 'name' => 'Taro Milk Tea', 'price' => 25000, 'icon' => '🟣', 'is_featured' => false, 'description' => 'Milk tea dengan rasa taro yang creamy.'],
            ['category_id' => $nonKopi->id, 'name' => 'Choco Milkshake', 'price' => 26000, 'icon' => '🍫', 'is_featured' => false, 'description' => 'Milkshake cokelat kental dan creamy.'],
            ['category_id' => $nonKopi->id, 'name' => 'Red Velvet Latte', 'price' => 28000, 'icon' => '❤️', 'is_featured' => false, 'description' => 'Perpaduan red velvet dan susu lembut.'],
            ['category_id' => $nonKopi->id, 'name' => 'Lemon Tea', 'price' => 18000, 'icon' => '🍋', 'is_featured' => false, 'description' => 'Teh segar dengan perasan lemon asli.'],
            ['category_id' => $nonKopi->id, 'name' => 'Strawberry Smoothie', 'price' => 27000, 'icon' => '🍓', 'is_featured' => true, 'description' => 'Smoothie strawberry segar dan creamy.'],
            ['category_id' => $nonKopi->id, 'name' => 'Mango Juice', 'price' => 22000, 'icon' => '🥭', 'is_featured' => false, 'description' => 'Jus mangga segar tanpa pengawet.'],
            ['category_id' => $nonKopi->id, 'name' => 'Thai Tea', 'price' => 23000, 'icon' => '🧋', 'is_featured' => false, 'description' => 'Teh Thailand dengan susu kental manis.'],

            // ===== MAKANAN =====
            ['category_id' => $makanan->id, 'name' => 'Croissant', 'price' => 20000, 'icon' => '🥐', 'is_featured' => false, 'description' => 'Pastry berlapis dengan tekstur renyah.'],
            ['category_id' => $makanan->id, 'name' => 'Cheesecake', 'price' => 35000, 'icon' => '🍰', 'is_featured' => true, 'description' => 'Cheesecake lembut dengan topping fresh.'],
            ['category_id' => $makanan->id, 'name' => 'Pasta Carbonara', 'price' => 38000, 'icon' => '🍝', 'is_featured' => true, 'description' => 'Pasta creamy dengan smoked beef dan parmesan.'],
            ['category_id' => $makanan->id, 'name' => 'Nasi Goreng Spesial', 'price' => 30000, 'icon' => '🍚', 'is_featured' => true, 'description' => 'Nasi goreng dengan telur, ayam, dan acar.'],
            ['category_id' => $makanan->id, 'name' => 'Chicken Sandwich', 'price' => 32000, 'icon' => '🥪', 'is_featured' => false, 'description' => 'Sandwich ayam crispy dengan saus mayo.'],
            ['category_id' => $makanan->id, 'name' => 'Beef Burger', 'price' => 35000, 'icon' => '🍔', 'is_featured' => false, 'description' => 'Burger daging sapi dengan keju leleh.'],
            ['category_id' => $makanan->id, 'name' => 'Waffle Honey', 'price' => 28000, 'icon' => '🧇', 'is_featured' => false, 'description' => 'Waffle renyah dengan madu dan butter.'],

            // ===== SNACK =====
            ['category_id' => $snack->id, 'name' => 'French Fries', 'price' => 18000, 'icon' => '🍟', 'is_featured' => false, 'description' => 'Kentang goreng renyah dengan saus pilihan.'],
            ['category_id' => $snack->id, 'name' => 'Chicken Nugget', 'price' => 20000, 'icon' => '🍗', 'is_featured' => false, 'description' => 'Nugget ayam crispy 6 pcs dengan saus.'],
            ['category_id' => $snack->id, 'name' => 'Banana Fritter', 'price' => 15000, 'icon' => '🍌', 'is_featured' => false, 'description' => 'Pisang goreng crispy dengan topping coklat.'],
            ['category_id' => $snack->id, 'name' => 'Cookies Choco Chip', 'price' => 16000, 'icon' => '🍪', 'is_featured' => true, 'description' => 'Cookies renyah dengan choco chip melimpah.'],
            ['category_id' => $snack->id, 'name' => 'Brownies', 'price' => 18000, 'icon' => '🍫', 'is_featured' => false, 'description' => 'Brownies coklat lembut dan moist.'],
            ['category_id' => $snack->id, 'name' => 'Donat Glaze', 'price' => 12000, 'icon' => '🍩', 'is_featured' => false, 'description' => 'Donat lembut dengan glaze manis.'],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}