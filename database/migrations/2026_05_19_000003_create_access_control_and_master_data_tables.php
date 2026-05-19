<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * MIGRATION: Access Control & Master Data
 * ============================================================
 *
 * Tabel hak akses admin dan data master modul magang.
 *
 * Tabel yang dibuat:
 *   - magang_access_rights     (hak akses admin per divisi)
 *   - divisions_magang         (master list divisi)
 *   - division_settings_magang (konfigurasi kapasitas divisi)
 *   - events_magang            (kalender kegiatan)
 *
 * Dependency:
 *   - users (dari migration infrastructure)
 * ============================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        // --------------------------------------------------
        // 1. MAGANG ACCESS RIGHTS
        //    Role admin: superadmin | admin_bidang
        // --------------------------------------------------
        Schema::create('magang_access_rights', function (Blueprint $table) {
            $table->id();

            /**
             * Relasi ke tabel users (pegawai instansi)
             * Satu user hanya boleh punya satu role
             */
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            /**
             * Role: superadmin = akses penuh
             *       admin_bidang = akses per divisi saja
             */
            $table->enum('role', ['superadmin', 'admin_bidang'])->index();

            /**
             * Hanya diisi jika role = admin_bidang
             */
            $table->string('division_name', 100)->nullable()->index();

            $table->timestamps();
        });

        // --------------------------------------------------
        // 2. DIVISIONS MAGANG
        //    Master source of truth seluruh divisi magang.
        //    Menggantikan free-string division_name yang rawan typo.
        // --------------------------------------------------
        Schema::create('divisions_magang', function (Blueprint $table) {
            $table->id();

            /**
             * Nama divisi — unik, menjadi referensi ke tabel lain
             */
            $table->string('name', 100)->unique()->index();

            /**
             * Jika inactive:
             * - tidak tampil di dropdown
             * - tidak bisa dipakai vacancy baru
             * - data lama tetap aman (soft disable)
             */
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
        });

        // --------------------------------------------------
        // 3. DIVISION SETTINGS MAGANG
        //    Konfigurasi kapasitas lowongan aktif per divisi.
        //    NULL = unlimited, angka = batas maksimal.
        // --------------------------------------------------
        Schema::create('division_settings_magang', function (Blueprint $table) {
            $table->id();

            /**
             * Referensi ke nama divisi (string, bukan FK ke divisions_magang)
             * karena divisions_magang bisa lebih baru dari settings ini.
             */
            $table->string('division_name', 100)->unique();

            /**
             * Kuota slot lowongan aktif per divisi.
             * Dihitung dari vacancy berstatus: open + closed (bukan archived).
             *
             * Business rule:
             *   6    → default starter quota bawaan sistem saat setting pertama dibuat
             *   angka lain → custom quota yang diubah superadmin (8, 10, 20, dst)
             *   NULL → unlimited (superadmin melepas batas kuota)
             *
             * NULL merepresentasikan "absence of limit" — disengaja sebagai
             * desain sistem, bukan missing data.
             */
            $table->unsignedTinyInteger('max_open_vacancies')
                ->nullable()
                ->default(6)
                ->comment('default 6 = starter quota; NULL = unlimited; angka lain = custom quota');

            /**
             * Audit: siapa yang terakhir mengubah setting ini
             */
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });

        // --------------------------------------------------
        // 4. EVENTS MAGANG
        //    Kalender kegiatan untuk ditampilkan ke peserta
        // --------------------------------------------------
        Schema::create('events_magang', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('color', 20)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events_magang');
        Schema::dropIfExists('division_settings_magang');
        Schema::dropIfExists('divisions_magang');
        Schema::dropIfExists('magang_access_rights');
    }
};
