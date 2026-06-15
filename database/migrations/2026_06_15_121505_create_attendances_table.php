<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();
            $table->string('clock_in_photo')->nullable();  // foto wajah saat masuk
            $table->string('clock_out_photo')->nullable(); // foto wajah saat keluar
            $table->enum('status', ['hadir', 'telat', 'izin', 'alpha'])->default('hadir');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']); // 1 baris per karyawan per hari
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};