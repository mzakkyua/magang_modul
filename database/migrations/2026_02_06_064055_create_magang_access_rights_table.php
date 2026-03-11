<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * ======================================================
         * TABEL MAGANG ACCESS RIGHTS
         * ======================================================
         *
         * Menyimpan hak akses admin pada modul magang.
         *
         * Role yang tersedia:
         *
         * superadmin
         * admin_bidang
         */

        Schema::create('magang_access_rights', function (Blueprint $table) {

            $table->id();

            /**
             * Relasi ke tabel users (pegawai instansi)
             */

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();


            /**
             * Role admin
             */

            $table->enum('role', ['superadmin', 'admin_bidang'])
                ->index();


            /**
             * Nama divisi
             *
             * hanya digunakan oleh admin_bidang
             */

            $table->string('division_name', 100)
                ->nullable()
                ->index();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magang_access_rights');
    }
};
