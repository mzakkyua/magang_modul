<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * MIGRATION: Infrastructure Tables
 * ============================================================
 *
 * Tabel-tabel inti Laravel dan auth admin/pegawai instansi.
 *
 * Tabel yang dibuat:
 *   - users               (akun admin / pegawai instansi)
 *   - password_reset_tokens
 *   - sessions
 *   - cache
 *   - cache_locks
 *
 * Dependency: TIDAK ADA (harus dijalankan pertama)
 * ============================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        // --------------------------------------------------
        // 1. USERS (Admin / Pegawai Instansi)
        // --------------------------------------------------
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('profile_photo_path', 255)->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // --------------------------------------------------
        // 2. PASSWORD RESET TOKENS
        // --------------------------------------------------
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('password_reset_tokens_magang', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // --------------------------------------------------
        // 3. SESSIONS
        // --------------------------------------------------
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();

            // Membedakan sesi admin vs magang
            $table->string('auth_guard', 20)->nullable()->index();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // --------------------------------------------------
        // 4. CACHE
        // --------------------------------------------------
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('password_reset_tokens_magang');
        Schema::dropIfExists('users');
    }
};
