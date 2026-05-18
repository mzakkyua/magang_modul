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
         * TABEL DIVISION SETTINGS MAGANG
         * =========================================================
         *
         * Menyimpan konfigurasi kapasitas lowongan
         * per divisi pada modul magang.
         *
         * Tabel ini juga menjadi pseudo master divisi
         * karena division_name pada vacancies_magang
         * masih berupa string.
         *
         * Logika:
         * - NULL  = unlimited (tidak ada batas)
         * - angka = batas maksimal lowongan aktif
         *
         * Lowongan aktif yang dihitung:
         * - open
         * - closed
         *
         * Lowongan archived tidak dihitung.
         * =========================================================
         */
        Schema::create('division_settings_magang', function (Blueprint $table) {

            $table->id();

            /**
             * Nama divisi unik.
             * Menjadi referensi ke vacancies_magang.division_name.
             */
            $table->string('division_name', 100)->unique();

            /**
             * Maksimal lowongan aktif.
             *
             * FIX: hanya ->nullable(), TANPA ->default().
             *
             * SEBELUMNYA (BUG):
             *   ->nullable()->default(6)
             *
             * Masalah: nullable() + default(6) saling bertentangan.
             * Ketika record dibuat tanpa meng-set kolom ini,
             * MySQL menggunakan nilai default (6), BUKAN NULL.
             * Akibatnya desain "NULL = unlimited" tidak pernah
             * terjadi secara otomatis — divisi baru selalu
             * mendapat batas 6, bahkan yang harusnya unlimited.
             *
             * SETELAH FIX:
             * - Tidak ada default → nilai otomatis NULL
             * - NULL di DB = unlimited (sesuai desain)
             * - Jika admin isi angka → ada batas
             * - Jika admin kosongkan → controller kirim NULL → unlimited
             */
            $table->unsignedTinyInteger('max_open_vacancies')
                ->nullable()
                ->comment('NULL = unlimited, angka = batas maks lowongan aktif');

            /**
             * Admin terakhir yang mengubah setting.
             * Nullable karena superadmin bisa saja dihapus dari sistem.
             */
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('division_settings_magang');
    }
};
