<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pelanggan', 'karyawan', 'bos', 'it'])->default('pelanggan')->after('email');
            $table->string('phone')->unique()->nullable()->after('role');
            $table->string('photo')->nullable()->after('phone'); // foto profil/wajah
            $table->boolean('is_active')->default(true)->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'photo', 'is_active']);
        });
    }
};