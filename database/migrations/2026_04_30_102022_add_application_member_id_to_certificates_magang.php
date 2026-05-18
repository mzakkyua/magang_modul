<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION: Ganti anchor sertifikat dari user_id → application_member_id
 *
 * KENAPA:
 *   Sebelumnya sertifikat hanya pakai user_id sebagai key di updateOrCreate().
 *   Jika user magang 2x → sertifikat periode 1 tertimpa oleh periode 2.
 *
 * SOLUSI:
 *   Tambah kolom application_member_id (FK ke application_members_magang).
 *   Satu baris = satu sertifikat per periode magang.
 *   Kolom nullable agar data lama (yang belum punya member_id) tidak rusak.
 *
 * BACKWARD COMPATIBLE:
 *   - Kolom user_id tetap ada — tidak di-drop, tidak di-ubah.
 *   - Data lama masih bisa diakses dan ditampilkan.
 *   - Unique constraint baru hanya berlaku untuk data baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates_magang', function (Blueprint $table) {

            // FK ke baris member spesifik (= periode magang spesifik)
            $table->unsignedBigInteger('application_member_id')
                  ->nullable()
                  ->after('user_id');

            $table->foreign('application_member_id')
                  ->references('id')
                  ->on('application_members_magang')
                  ->nullOnDelete();

            // Satu member hanya boleh punya satu sertifikat per periode.
            // Partial unique: hanya enforce jika application_member_id tidak null.
            // MySQL tidak support partial unique native → kita pakai unique biasa,
            // karena setiap member_id memang hanya boleh punya 1 sertifikat.
            $table->unique('application_member_id', 'uniq_cert_per_member');
        });
    }

    public function down(): void
    {
        Schema::table('certificates_magang', function (Blueprint $table) {
            $table->dropForeign(['application_member_id']);
            $table->dropUnique('uniq_cert_per_member');
            $table->dropColumn('application_member_id');
        });
    }
};