<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ================================
        // 1. TABEL USERS MAGANG (LOGIN)
        // ================================
        Schema::create('users_magang', function (Blueprint $table) {
            $table->id();

            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();

            // Simpan hash password
            $table->string('password_hash');

            // created_at & updated_at
            $table->timestamps();
        });

        // ================================
        // 2. TABEL PROFILES MAGANG (BIODATA)
        // ================================
        Schema::create('profiles_magang', function (Blueprint $table) {
            $table->id();

            // Relasi ke users_magang
            $table->foreignId('user_id')
                ->constrained('users_magang')
                ->cascadeOnDelete();

            // Data wajib
            $table->string('full_name', 150);

            // UBAH ENUM → VARCHAR (lebih fleksibel)
            $table->string('education_level', 50)->nullable();

            // Data opsional (bisa dilengkapi setelah login)
            $table->string('nim_nisn', 50)->nullable();
            $table->string('institution_name', 100)->nullable();
            $table->string('major', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('cv_file_path')->nullable();
            $table->string('proposal_file_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles_magang');
        Schema::dropIfExists('users_magang');
    }
};
