<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('courier_status', ['available', 'busy'])->default('available')->after('is_active');
            $table->timestamp('last_active_at')->nullable()->after('courier_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['courier_status', 'last_active_at']);
        });
    }
};