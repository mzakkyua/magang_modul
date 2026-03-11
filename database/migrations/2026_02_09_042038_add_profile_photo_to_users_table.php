<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        /**
         * ======================================================
         * TAMBAH FOTO PROFIL ADMIN
         * ======================================================
         *
         * Kolom ini menyimpan path file foto profil
         * admin/pegawai instansi.
         *
         * File disimpan di storage dan hanya path
         * yang disimpan di database.
         */

        Schema::table('users', function (Blueprint $table) {

            $table->string('profile_photo_path', 255)
                ->nullable()
                ->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('profile_photo_path');
        });
    }
};
