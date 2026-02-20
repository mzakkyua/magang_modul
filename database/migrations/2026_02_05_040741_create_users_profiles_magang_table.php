<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |=====================================================
        | 1. TABEL USERS_MAGANG (AKUN PESERTA)
        |=====================================================
        | Menyimpan data autentikasi peserta magang
        | Catatan:
        | - Password disimpan di kolom `password_hash`
        | - Timestamp dibuat manual agar konsisten & eksplisit
        |=====================================================
        */
        Schema::create('users_magang', function (Blueprint $table) {
            $table->id();

            // Kredensial akun
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password_hash');

            // PERBAIKAN 1: Gunakan timestamps() bawaan Laravel
            // Ini otomatis bikin kolom 'created_at' DAN 'updated_at' sekaligus.
            $table->timestamps();
        });

        /*
        |=====================================================
        | 2. TABEL PROFILES_MAGANG (BIODATA PESERTA)
        |=====================================================
        | Menyimpan detail profil peserta magang
        | Relasi:
        | - profiles_magang.user_id → users_magang.id
        |=====================================================
        */
        Schema::create('profiles_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_magang')->onDelete('cascade');

            // Data Wajib saat Register (Hanya Nama)
            $table->string('full_name', 150);

            // PERBAIKAN 2: Semua data detail SAYA BUAT NULLABLE
            // Supaya bisa Register dulu, baru lengkapi data nanti di dashboard.
            $table->enum('education_level', ['siswa_smk', 'mahasiswa'])->default('mahasiswa');
            $table->string('nim_nisn', 50)->nullable();      // Boleh kosong dulu
            $table->string('institution_name', 100)->nullable(); // Boleh kosong dulu
            $table->string('major', 100)->nullable();            // Boleh kosong dulu
            $table->string('phone_number', 20)->nullable();      // Boleh kosong dulu
            $table->text('address')->nullable();

            // Dokumen pendukung
            $table->string('cv_file_path')->nullable();
            $table->string('proposal_file_path')->nullable();

            // Timestamps standar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles_magang');
        Schema::dropIfExists('users_magang');
    }
};
