<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_type ENUM('dine_in', 'take_away', 'mixed') DEFAULT 'take_away'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_type ENUM('dine_in', 'take_away') DEFAULT 'take_away'");
    }
};
