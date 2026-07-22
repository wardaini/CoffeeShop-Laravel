<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Delivery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::where('is_available', true)->get();
        $pelanggan = [
            ['name' => 'Andi Pratama', 'phone' => '08123456701', 'email' => 'andi@gmail.com'],
            ['name' => 'Siti Rahma', 'phone' => '08123456702', 'email' => 'siti@gmail.com'],
            ['name' => 'Budi Santoso', 'phone' => '08123456703', 'email' => 'budi@gmail.com'],
            ['name' => 'Rina Marlina', 'phone' => '08123456704', 'email' => 'rina@gmail.com'],
            ['name' => 'Fajar Nugroho', 'phone' => '08123456705', 'email' => 'fajar@gmail.com'],
            ['name' => 'Dewi Lestari', 'phone' => '08123456706', 'email' => 'dewi@gmail.com'],
            ['name' => 'Reza Mahendra', 'phone' => '08123456707', 'email' => 'reza@gmail.com'],
            ['name' => 'Putri Amalia', 'phone' => '08123456708', 'email' => 'putri@gmail.com'],
            ['name' => 'Hendra Wijaya', 'phone' => '08123456709', 'email' => 'hendra@gmail.com'],
            ['name' => 'Nadia Fitri', 'phone' => '08123456710', 'email' => 'nadia@gmail.com'],
        ];

        $orderTypes = ['dine_in', 'take_away', 'mixed'];
        $paymentMethods = ['cash', 'qris', 'dana', 'ovo', 'bsi', 'bank_aceh'];
        $tableNumbers = ['A1', 'A2', 'A3', 'B1', 'B2', 'B3', 'C1', 'C2'];

        // Bulan 4 dan 5
        $months = [4, 5];

        foreach ($months as $month) {
            // 30 transaksi per bulan
            for ($i = 0; $i < 30; $i++) {
                $day       = rand(1, 28);
                $hour      = rand(8, 21);
                $minute    = rand(0, 59);
                $date      = Carbon::create(2026, $month, $day, $hour, $minute, 0);

                $customer    = $pelanggan[array_rand($pelanggan)];
                $orderType   = $orderTypes[array_rand($orderTypes)];
                $payMethod   = $paymentMethods[array_rand($paymentMethods)];
                $isDelivery  = $orderType === 'take_away' && rand(0, 1);
                $deliveryFee = $isDelivery ? 8000 : 0;

                // Pilih 1-4 produk random
                $selectedProducts = $products->random(rand(1, 4));
                $totalPrice = 0;

                foreach ($selectedProducts as $product) {
                    $qty = rand(1, 3);
                    $totalPrice += $product->price * $qty;
                }

                $order = Order::create([
                    'order_code'       => 'ORD-' . strtoupper(Str::random(8)),
                    'customer_name'    => $customer['name'],
                    'customer_email'   => $customer['email'],
                    'customer_phone'   => $customer['phone'],
                    'order_type'       => $orderType,
                    'take_away_method' => $orderType === 'take_away' ? ($isDelivery ? 'delivery' : 'pickup') : null,
                    'table_number'     => in_array($orderType, ['dine_in', 'mixed']) ? $tableNumbers[array_rand($tableNumbers)] : null,
                    'delivery_address' => $isDelivery ? 'Jl. Contoh No.' . rand(1, 100) . ', Lhokseumawe' : null,
                    'payment_method'   => $payMethod,
                    'payment_status'   => 'paid',
                    'total_price'      => $totalPrice,
                    'delivery_fee'     => $deliveryFee,
                    'status'           => 'completed',
                    'notes'            => null,
                    'created_at'       => $date,
                    'updated_at'       => $date,
                ]);

                // Paksa update created_at karena Laravel auto-set
                \DB::table('orders')->where('id', $order->id)->update([
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // Buat order items
                foreach ($selectedProducts as $product) {
                    $qty = rand(1, 3);
                    $itemType = $orderType === 'mixed'
                        ? (rand(0, 1) ? 'dine_in' : 'take_away')
                        : ($orderType === 'dine_in' ? 'dine_in' : 'take_away');

                    OrderItem::create([
                        'order_id'        => $order->id,
                        'product_id'      => $product->id,
                        'quantity'        => $qty,
                        'price'           => $product->price,
                        'kitchen_status'  => 'ready',
                        'item_order_type' => $itemType,
                    ]);
                }

                // Buat delivery record kalau delivery
                if ($isDelivery) {
                    $couriers = User::where('role', 'karyawan')
                        ->whereHas('employeeProfile', fn($q) => $q->where('position', 'Kurir'))
                        ->get();

                    Delivery::create([
                        'order_id'     => $order->id,
                        'courier_id'   => $couriers->isNotEmpty() ? $couriers->random()->id : null,
                        'status'       => 'delivered',
                        'assigned_at'  => $date,
                        'picked_up_at' => $date->copy()->addMinutes(10),
                        'delivered_at' => $date->copy()->addMinutes(30),
                        'created_at'   => $date,
                        'updated_at'   => $date,
                    ]);
                }
            }
        }

        $this->command->info('✅ 60 transaksi berhasil dibuat untuk bulan April & Mei 2026!');
    }
}