<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel User Magang (Akun)
        Schema::create('users_magang', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password_hash'); // Nanti di Model harus di-map ke 'password'
            $table->timestamp('created_at')->useCurrent();
        });

        // 2. Tabel Profil Magang (Biodata)
        Schema::create('profiles_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_magang')->onDelete('cascade');
            $table->string('full_name', 150);
            $table->string('nim_nisn', 50);
            $table->string('institution_name', 100);
            $table->string('major', 100);
            $table->string('phone_number', 20);
            $table->text('address')->nullable();
            $table->string('cv_file_path')->nullable();
            $table->string('proposal_file_path')->nullable();
            
            // updated_at otomatis update current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles_magang');
        Schema::dropIfExists('users_magang');
    }
};