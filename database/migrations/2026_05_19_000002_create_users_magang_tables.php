<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * MIGRATION: Users & Profiles Magang
 * ============================================================
 *
 * Tabel akun dan biodata peserta magang/penelitian.
 *
 * Tabel yang dibuat:
 *   - users_magang     (akun login peserta)
 *   - profiles_magang  (biodata dan dokumen peserta)
 *
 * Dependency: TIDAK ADA dependency ke tabel lain
 * ============================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        // --------------------------------------------------
        // 1. USERS MAGANG (Akun Login Peserta)
        // --------------------------------------------------
        Schema::create('users_magang', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password_hash');

            // Diperlukan untuk "Remember Me" di Laravel Auth
            $table->rememberToken();

            $table->timestamps();
        });

        // --------------------------------------------------
        // 2. PROFILES MAGANG (Biodata Peserta)
        // --------------------------------------------------
        Schema::create('profiles_magang', function (Blueprint $table) {
            $table->id();

            /**
             * Relasi 1-to-1 ke users_magang
             */
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users_magang')
                ->cascadeOnDelete();

            /**
             * Data utama
             */
            $table->string('full_name', 150);

            /**
             * Level pendidikan: siswa_smk | mahasiswa | peneliti
             */
            $table->string('education_level', 50)->nullable()->index();

            /**
             * Data institusi
             */
            $table->string('nim_nisn', 50)->nullable();
            $table->string('institution_name', 100)->nullable();
            $table->string('major', 100)->nullable();

            /**
             * Kontak
             */
            $table->string('phone_number', 20)->nullable();
            $table->text('address')->nullable();

            /**
             * File dokumen peserta
             */
            $table->string('cv_file_path', 255)->nullable();
            $table->string('surat_rekomendasi_file', 255)->nullable();
            $table->string('proposal_file_path', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles_magang');
        Schema::dropIfExists('users_magang');
    }
};
