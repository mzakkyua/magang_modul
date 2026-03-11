<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * =========================================================
         * TABEL VACANCIES MAGANG
         * =========================================================
         *
         * Menyimpan lowongan magang dan penelitian.
         *
         * Tabel ini merupakan awal pipeline sistem:
         *
         * Vacancy → Application → Member → Assessment → Certificate
         */

        Schema::create('vacancies_magang', function (Blueprint $table) {

            $table->id();

            /**
             * Judul lowongan
             */
            $table->string('title', 200);

            /**
             * Nama divisi instansi
             * digunakan untuk filter admin divisi
             */
            $table->string('division_name', 100)->index();


            /**
             * Jenis kegiatan
             *
             * magang
             * penelitian
             */
            $table->enum('type', ['magang', 'penelitian'])->index();


            /**
             * Mode pendaftaran
             *
             * individu
             * kelompok
             * hybrid
             */
            $table->enum('registration_mode', ['individu', 'kelompok', 'hybrid'])
                ->default('individu');


            /**
             * Kuota peserta
             */
            $table->unsignedInteger('quota_slots')->default(0);


            /**
             * Batas anggota kelompok
             */

            $table->unsignedInteger('min_members')->default(1);

            $table->unsignedInteger('max_members')->default(1);


            /**
             * Timeline kegiatan
             */

            $table->date('start_date');

            $table->date('end_date');


            /**
             * Deskripsi kegiatan
             */

            $table->text('description')->nullable();


            /**
             * Status lowongan
             *
             * open
             * closed
             * archived
             */

            $table->enum('status', ['open', 'closed', 'archived'])
                ->default('open')
                ->index();


            /**
             * Timestamp Laravel
             */

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies_magang');
    }
};
