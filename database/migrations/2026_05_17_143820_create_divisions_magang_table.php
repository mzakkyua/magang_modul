<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * =========================================================
     * TABLE: divisions_magang
     * =========================================================
     *
     * Master source of truth untuk seluruh divisi magang.
     *
     * Sebelumnya:
     * - division_name bersifat free string
     * - rawan typo
     * - rawan occupancy split
     * - rawan cache mismatch
     *
     * Setelah migration ini:
     * - seluruh divisi dikelola terpusat
     * - superadmin menjadi authority utama
     * - dropdown vacancy mengambil data dari sini
     */

    public function up(): void
    {
        Schema::create('divisions_magang', function (Blueprint $table) {

            $table->id();

            /**
             * Nama divisi
             */
            $table->string('name', 100)
                ->unique()
                ->index();

            /**
             * Status aktif
             *
             * inactive:
             * - tidak tampil di dropdown
             * - tidak bisa dipakai vacancy baru
             * - data lama tetap aman
             */
            $table->boolean('is_active')
                ->default(true)
                ->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions_magang');
    }
};
