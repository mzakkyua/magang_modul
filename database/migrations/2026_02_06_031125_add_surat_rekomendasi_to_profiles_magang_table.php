<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * =====================================================
         * TAMBAH KOLOM SURAT REKOMENDASI
         * =====================================================
         *
         * Kolom ini digunakan untuk menyimpan path file
         * surat rekomendasi dari institusi peserta.
         *
         * File disimpan di storage dan hanya path yang
         * disimpan di database.
         */

        Schema::table('profiles_magang', function (Blueprint $table) {

            $table->string('surat_rekomendasi_file', 255)
                ->nullable()
                ->after('cv_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('profiles_magang', function (Blueprint $table) {

            $table->dropColumn('surat_rekomendasi_file');
        });
    }
};
