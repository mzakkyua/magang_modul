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
        Schema::create('magang_access_rights', function (Blueprint $table) {
            $table->id();
            
            // KUNCI UTAMA: Kita hanya menyimpan ID dari tabel users pegawai
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // JABATAN DI MODUL MAGANG
            $table->enum('role', ['superadmin', 'admin_bidang']);
            
            // DIVISI (Khusus Admin Bidang)
            $table->string('division_name', 100)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magang_access_rights');
    }
};
