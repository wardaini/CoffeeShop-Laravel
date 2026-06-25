<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('assigned_to', ['barista', 'dapur'])->nullable()->after('price');
            $table->enum('kitchen_status', ['pending', 'processing', 'ready'])->default('pending')->after('assigned_to');
            $table->text('kitchen_notes')->nullable()->after('kitchen_status');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['assigned_to', 'kitchen_status', 'kitchen_notes']);
        });
    }
};