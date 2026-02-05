<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Applications (Header Pengajuan)
        Schema::create('applications_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained('vacancies_magang');
            $table->foreignId('leader_user_id')->constrained('users_magang');
            $table->string('research_title')->nullable();
            $table->timestamp('submission_date')->useCurrent();
            
            // Logic Kuota: Rejected = Restock
            $table->enum('status', ['pending', 'verified', 'interview', 'accepted', 'rejected', 'completed'])
                ->default('pending');
            
            $table->text('admin_feedback')->nullable();
        });

        // 2. Members (Detail Anggota)
        Schema::create('application_members_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications_magang')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users_magang');
            
            // Dashboard Statistik
            $table->enum('individual_status', ['active', 'dropped_out', 'finished'])->default('active');
        });

        // 3. Assessments (Penilaian)
        Schema::create('assessments_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('application_members_magang')->onDelete('cascade');
            $table->string('assessor_name', 150);
            
            $table->decimal('score_behavior', 5, 2)->default(0);
            $table->decimal('score_discipline', 5, 2)->default(0);
            $table->decimal('score_performance', 5, 2)->default(0);
            
            // Final score dihitung di query/model accessor, 
            // tapi kita simpan kolomnya jika ingin generated column (MySQL 5.7+)
            // Note: Laravel migration support generated column via raw query, 
            // tapi agar aman di semua DB driver, kita pakai decimal biasa dulu, hitung di Controller.
            $table->decimal('final_score', 5, 2)->default(0); 
            
            $table->text('evaluation_notes')->nullable(); // Catatan Resmi
            $table->text('additional_notes')->nullable(); // Catatan NB
            
            $table->timestamp('created_at')->useCurrent();
        });

        // 4. Certificates (Manual Upload)
        Schema::create('certificates_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('application_members_magang')->onDelete('cascade');
            $table->string('certificate_number', 100); // Input Manual
            $table->string('file_path'); // Upload Manual
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates_magang');
        Schema::dropIfExists('assessments_magang');
        Schema::dropIfExists('application_members_magang');
        Schema::dropIfExists('applications_magang');
    }
};