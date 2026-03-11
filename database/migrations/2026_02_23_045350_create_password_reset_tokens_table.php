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
         * TABEL PASSWORD RESET TOKENS
         * ======================================================
         *
         * Digunakan untuk fitur reset password.
         */

        Schema::create('password_reset_tokens', function (Blueprint $table) {

            // email user yang melakukan reset
            $table->string('email')->primary();

            // token reset password (hashed)
            $table->string('token');

            // waktu request reset
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
