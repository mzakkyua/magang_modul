<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications_magang', function (Blueprint $table) {

            // INDEX 1: status saja
            // Dipakai oleh GROUP BY status di dashboard dan filter tabel verifikasi
            $table->index('status', 'idx_applications_status');

            // INDEX 2: vacancy_id + status (COMPOSITE)
            // Dipakai oleh checkQuota() dan updateStatusBasedOnQuota()
            // Query: WHERE vacancy_id = ? AND status IN (...)
            $table->index(
                ['vacancy_id', 'status'],
                'idx_applications_vacancy_status'
            );

            // INDEX 3: vacancy_id + leader_user_id (COMPOSITE)
            // Dipakai oleh checkDuplicate()
            // Query: WHERE vacancy_id = ? AND leader_user_id = ?
            $table->index(
                ['vacancy_id', 'leader_user_id'],
                'idx_applications_vacancy_leader'
            );
        });
    }

    public function down(): void
    {
        Schema::table('applications_magang', function (Blueprint $table) {

            $table->dropIndex('idx_applications_status');
            $table->dropIndex('idx_applications_vacancy_status');
            $table->dropIndex('idx_applications_vacancy_leader');
        });
    }
};
