<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies_magang', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            
            // Filter Admin Bidang
            $table->string('division_name', 100);
            
            $table->enum('type', ['magang', 'penelitian']);
            $table->enum('registration_mode', ['individu', 'kelompok', 'hybrid'])->default('individu');
            
            $table->integer('quota_slots')->default(0);
            $table->integer('max_members')->default(1);
            
            // Timeline Visual
            $table->date('start_date');
            $table->date('end_date');
            
            $table->text('description')->nullable();
            
            // Kontrol Admin
            $table->enum('status', ['open', 'closed', 'archived'])->default('open');
            
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies_magang');
    }
};