<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_code')->unique(); // untuk barcode
            $table->string('position')->nullable(); // Kasir, Barista, Kurir, dll
            $table->string('ktp_number')->nullable();
            $table->string('ktp_photo')->nullable();
            $table->string('face_photo')->nullable(); // foto wajah untuk verifikasi
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->date('joined_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};