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

            // Timestamp manual
            $table->timestamp('created_at')
                ->useCurrent()
                ->comment('Waktu akun dibuat');

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate()
                ->comment('Waktu terakhir akun diperbarui');
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

            // Relasi ke akun peserta
            $table->foreignId('user_id')
                ->constrained('users_magang')
                ->onDelete('cascade');

            // Data pendidikan & identitas
            $table->enum('education_level', ['siswa_smk', 'mahasiswa'])
                ->default('mahasiswa');

            $table->string('full_name', 150)->nullable();
            $table->string('nim_nisn', 50)->nullable();
            $table->string('institution_name', 100)->nullable();
            $table->string('major', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->text('address')->nullable();

            // Dokumen pendukung
            $table->string('cv_file_path')->nullable();
            $table->string('proposal_file_path')->nullable();

            // Timestamp manual
            $table->timestamp('created_at')
                ->useCurrent()
                ->comment('Waktu profil dibuat');

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate()
                ->comment('Waktu terakhir profil diperbarui');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles_magang');
        Schema::dropIfExists('users_magang');
    }
};
