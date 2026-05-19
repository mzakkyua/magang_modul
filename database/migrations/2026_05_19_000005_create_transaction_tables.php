<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * MIGRATION: Transaction Tables
 * ============================================================
 *
 * Tabel-tabel inti pipeline pengajuan magang:
 *
 *   Vacancy → Application → Member → Assessment → Certificate
 *
 * Tabel yang dibuat (urutan wajib sesuai FK dependency):
 *   1. applications_magang
 *   2. application_members_magang
 *   3. assessments_magang
 *   4. certificates_magang
 *
 * Dependency:
 *   - vacancies_magang  (dari migration vacancies)
 *   - users_magang      (dari migration users_magang)
 *   - users             (dari migration infrastructure)
 * ============================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        // --------------------------------------------------
        // 1. APPLICATIONS MAGANG (Header Pengajuan)
        //    Satu baris = satu pengajuan oleh satu leader
        // --------------------------------------------------
        Schema::create('applications_magang', function (Blueprint $table) {
            $table->id();

            /**
             * Lowongan yang dilamar
             */
            $table->foreignId('vacancy_id')
                ->constrained('vacancies_magang');

            /**
             * Pemohon (leader kelompok atau individual)
             */
            $table->foreignId('leader_user_id')
                ->constrained('users_magang');

            /**
             * Judul dan abstrak penelitian (opsional, hanya untuk type=penelitian)
             */
            $table->string('research_title')->nullable();
            $table->text('research_abstract')->nullable();

            /**
             * Status alur verifikasi:
             *   pending   → baru masuk
             *   verified  → dokumen lengkap
             *   interview → dipanggil interview
             *   accepted  → diterima
             *   rejected  → ditolak (kuota di-restock)
             *   resigned  → mengundurkan diri
             *   completed → selesai magang
             */
            $table->enum('status', [
                'pending',
                'verified',
                'interview',
                'accepted',
                'rejected',
                'resigned',
                'completed',
            ])->default('pending');

            /**
             * Catatan dari admin (alasan tolak, arahan, dll)
             */
            $table->text('admin_feedback')->nullable();

            $table->timestamps();

            /**
             * Index untuk query yang sering digunakan:
             *
             * idx_applications_status
             *   → dipakai GROUP BY status di dashboard
             *
             * idx_applications_vacancy_status (composite)
             *   → dipakai checkQuota() dan updateStatusBasedOnQuota()
             *     WHERE vacancy_id = ? AND status IN (...)
             *
             * idx_applications_vacancy_leader (composite)
             *   → dipakai checkDuplicate()
             *     WHERE vacancy_id = ? AND leader_user_id = ?
             */
            $table->index('status', 'idx_applications_status');
            $table->index(['vacancy_id', 'status'], 'idx_applications_vacancy_status');
            $table->index(['vacancy_id', 'leader_user_id'], 'idx_applications_vacancy_leader');
        });

        // --------------------------------------------------
        // 2. APPLICATION MEMBERS MAGANG (Detail Anggota)
        //    Satu baris = satu anggota dalam satu pengajuan
        // --------------------------------------------------
        Schema::create('application_members_magang', function (Blueprint $table) {
            $table->id();

            /**
             * Pengajuan induk — jika pengajuan dihapus, member ikut terhapus
             */
            $table->foreignId('application_id')
                ->constrained('applications_magang')
                ->cascadeOnDelete();

            /**
             * Akun magang anggota
             */
            $table->foreignId('user_id')
                ->constrained('users_magang')
                ->cascadeOnDelete();

            /**
             * Status individu:
             *   active      → sedang aktif magang
             *   dropped_out → mengundurkan diri di tengah jalan
             *   finished    → selesai
             */
            $table->enum('individual_status', ['active', 'dropped_out', 'finished'])
                ->default('active');

            /**
             * Satu user tidak boleh menjadi anggota dua kali
             * dalam pengajuan yang sama
             */
            $table->unique(['application_id', 'user_id']);

            $table->timestamps();

            /**
             * idx_members_user_id
             *   → dipakai untuk query histori magang per user
             */
            $table->index('user_id', 'idx_members_user_id');
        });

        // --------------------------------------------------
        // 3. ASSESSMENTS MAGANG (Penilaian)
        //    Satu member = satu penilaian akhir
        // --------------------------------------------------
        Schema::create('assessments_magang', function (Blueprint $table) {
            $table->id();

            /**
             * Member yang dinilai — jika member dihapus, penilaian ikut terhapus
             */
            $table->foreignId('member_id')
                ->constrained('application_members_magang')
                ->cascadeOnDelete();

            /**
             * Nama penilai (tidak FK ke users, bisa eksternal)
             */
            $table->string('assessor_name', 150);

            /**
             * Komponen penilaian (skala 0–100, 2 desimal)
             */
            $table->decimal('score_behavior', 5, 2)->default(0);
            $table->decimal('score_discipline', 5, 2)->default(0);
            $table->decimal('score_performance', 5, 2)->default(0);

            /**
             * Final score — dihitung dan disimpan oleh controller/service,
             * bukan generated column, agar kompatibel semua DB driver.
             */
            $table->decimal('final_score', 5, 2)->default(0);

            /**
             * Catatan penilaian
             */
            $table->text('evaluation_notes')->nullable();
            $table->text('additional_notes')->nullable();

            $table->timestamps();
        });

        // --------------------------------------------------
        // 4. CERTIFICATES MAGANG (Sertifikat)
        //    Satu baris = satu sertifikat per periode magang
        // --------------------------------------------------
        Schema::create('certificates_magang', function (Blueprint $table) {
            $table->id();

            /**
             * Pemilik sertifikat.
             * Dipertahankan untuk kemudahan query dan filter per user,
             * BUKAN sebagai anchor utama relasi sertifikat.
             */
            $table->foreignId('user_id')
                ->constrained('users_magang')
                ->cascadeOnDelete();

            /**
             * PRIMARY ANCHOR sertifikat → periode magang spesifik.
             *
             * Business rule:
             *   Satu peserta bisa mengikuti magang beberapa kali di periode berbeda.
             *   Setiap periode menghasilkan satu sertifikat tersendiri.
             *   application_member_id memastikan sertifikat per periode tetap terpisah
             *   dan tidak bisa tertimpa oleh periode berikutnya.
             *
             *   user_id (di atas) → untuk filter/query saja
             *   application_member_id (ini) → source of truth relasi sertifikat
             *
             * Nullable untuk backward compatibility data lama yang belum
             * memiliki application_member_id.
             */
            $table->unsignedBigInteger('application_member_id')->nullable();

            $table->foreign('application_member_id')
                ->references('id')
                ->on('application_members_magang')
                ->nullOnDelete();

            /**
             * Satu member (= satu periode magang) hanya boleh punya satu sertifikat.
             * Constraint ini menjaga integritas "1 periode = 1 sertifikat".
             */
            $table->unique('application_member_id', 'uniq_cert_per_member');

            /**
             * Informasi sertifikat
             */
            $table->string('title');
            $table->string('file');

            /**
             * Audit upload
             */
            $table->unsignedBigInteger('uploaded_by_admin_id')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('replaced_at')->nullable();

            $table->foreign('uploaded_by_admin_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop dalam urutan terbalik (leaf → root)
        Schema::dropIfExists('certificates_magang');
        Schema::dropIfExists('assessments_magang');
        Schema::dropIfExists('application_members_magang');
        Schema::dropIfExists('applications_magang');
    }
};
