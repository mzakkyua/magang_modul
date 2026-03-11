<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * ========================================================
         * TABEL USERS MAGANG
         * ========================================================
         *
         * Menyimpan akun login peserta magang.
         */

        Schema::create('users_magang', function (Blueprint $table) {

            $table->id();

            // Username unik untuk login
            $table->string('username', 50)->unique();

            // Email unik
            $table->string('email', 100)->unique();

            // Password yang sudah di-hash
            $table->string('password_hash');

            $table->timestamps();
        });


        /**
         * ========================================================
         * TABEL PROFILES MAGANG
         * ========================================================
         *
         * Menyimpan biodata peserta magang.
         */

        Schema::create('profiles_magang', function (Blueprint $table) {

            $table->id();

            /**
             * Relasi ke users_magang
             *
             * 1 user hanya memiliki 1 profile
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
             * Level pendidikan
             *
             * Contoh:
             * - siswa_smk
             * - mahasiswa
             * - peneliti
             */

            $table->string('education_level', 50)
                ->nullable()
                ->index();


            /**
             * Data tambahan
             */

            $table->string('nim_nisn', 50)->nullable();

            $table->string('institution_name', 100)->nullable();

            $table->string('major', 100)->nullable();

            $table->string('phone_number', 20)->nullable();

            $table->text('address')->nullable();


            /**
             * File dokumen
             */

            $table->string('cv_file_path', 255)->nullable();

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
