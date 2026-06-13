<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->enum('order_type', ['dine_in', 'take_away'])->default('take_away')->after('order_code');
            $table->enum('take_away_method', ['delivery', 'pickup'])->nullable()->after('order_type');
            $table->string('table_number')->nullable()->after('take_away_method'); // untuk dine-in
            $table->text('delivery_address')->nullable()->after('table_number');
            $table->enum('payment_method', ['qris', 'dana', 'ovo', 'bsi', 'bank_aceh', 'cash'])->default('cash')->after('total_price');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid')->after('payment_method');
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id', 'order_type', 'take_away_method', 'table_number',
                'delivery_address', 'payment_method', 'payment_status', 'delivery_fee',
            ]);
        });
    }
};