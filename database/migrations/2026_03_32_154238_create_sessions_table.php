<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {

            $table->string('id')->primary();

            // ID user yang login (opsional)
            $table->foreignId('user_id')
                ->nullable()
                ->index();

            // IP address user
            $table->string('ip_address', 45)->nullable();

            // Browser user
            $table->text('user_agent')->nullable();

            // Data session
            $table->longText('payload');

            // Timestamp aktivitas terakhir
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
