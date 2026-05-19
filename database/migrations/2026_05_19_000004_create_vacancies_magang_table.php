<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * MIGRATION: Vacancies Magang
 * ============================================================
 *
 * Tabel lowongan magang dan penelitian.
 * Merupakan titik awal pipeline sistem:
 *
 *   Vacancy → Application → Member → Assessment → Certificate
 *
 * Tabel yang dibuat:
 *   - vacancies_magang
 *
 * Dependency:
 *   - Tidak ada FK ke tabel lain (division_name sebagai string)
 * ============================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies_magang', function (Blueprint $table) {
            $table->id();

            /**
             * Informasi dasar lowongan
             */
            $table->string('title', 200);

            /**
             * Nama divisi pemilik lowongan.
             * String (bukan FK) agar fleksibel; validasi dilakukan di app layer.
             * Dropdown mengambil data dari divisions_magang.
             */
            $table->string('division_name', 100)->index();

            /**
             * Jenis kegiatan: magang | penelitian
             */
            $table->enum('type', ['magang', 'penelitian'])->index();

            /**
             * Mode pendaftaran:
             *   individu = 1 orang
             *   kelompok = min..max orang
             *   hybrid   = bisa keduanya
             */
            $table->enum('registration_mode', ['individu', 'kelompok', 'hybrid'])
                ->default('individu');

            /**
             * Kuota slot total (0 = tidak dibatasi per vacancy)
             */
            $table->unsignedInteger('quota_slots')->default(0);

            /**
             * Batas anggota kelompok
             */
            $table->unsignedInteger('min_members')->default(1);
            $table->unsignedInteger('max_members')->default(1);

            /**
             * Timeline pelaksanaan
             */
            $table->date('start_date');
            $table->date('end_date');

            /**
             * Deskripsi kegiatan (opsional)
             */
            $table->text('description')->nullable();

            /**
             * Status lowongan: open | closed | archived
             */
            $table->enum('status', ['open', 'closed', 'archived'])
                ->default('open')
                ->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies_magang');
    }
};
