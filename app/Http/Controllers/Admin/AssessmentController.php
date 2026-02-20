<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationMemberMagang; // Peserta magang (unit penilaian)
use App\Models\AssessmentMagang;        // Nilai resmi peserta
use App\Models\MagangAccessRight;       // SK penunjukan / hak akses admin
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

/**
 * =========================================================
 * CONTROLLER: AssessmentController
 * =========================================================
 * TANGGUNG JAWAB:
 * - Menampilkan daftar peserta magang yang dapat dinilai
 * - Menampilkan form penilaian per peserta
 * - Menyimpan & memperbarui nilai peserta
 *
 * PRINSIP UTAMA:
 * - Penilaian bersifat PERORANGAN (bukan per kelompok)
 * - Admin hanya boleh menilai peserta dari divisinya
 *
 * KEAMANAN & PRIVASI:
 * - Akses divisi diverifikasi di setiap endpoint
 * - Mencegah manipulasi URL & request API
 *
 * DAMPAK SISTEM:
 * - Data nilai bersifat resmi & sensitif
 * - Perubahan nilai memengaruhi dashboard statistik
 *
 * POTENSI MIGRASI:
 * - Logic penilaian → Service Layer
 * - Hak akses → Policy / Middleware
 * =========================================================
 */
class AssessmentController extends Controller
{
    /**
     * =====================================================
     * METHOD: index()
     * =====================================================
     * TUJUAN:
     * - Menampilkan daftar peserta yang:
     *   - Sudah diterima magang (status: accepted)
     *   - Bisa dinilai oleh admin
     *
     * FITUR:
     * - Filter otomatis berdasarkan divisi admin
     * - Pencarian nama peserta (optional)
     * =====================================================
     */
    public function index(Request $request)
    {
        /* =====================================================
         * 1. AMBIL DATA ADMIN LOGIN & HAK AKSES
         * ===================================================== */
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        // Safety check: admin harus punya SK penunjukan
        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki hak akses penilaian.');
        }

        /* =====================================================
         * 2. QUERY DASAR: PESERTA YANG SUDAH DITERIMA
         * =====================================================
         * - Target penilaian adalah ApplicationMemberMagang
         * - Hanya peserta dengan status lamaran "accepted"
         * - Eager loading untuk efisiensi query
         * ===================================================== */
        $query = ApplicationMemberMagang::with([
            'user.profile',
            'application.vacancy',
            'assessment'
        ])
            ->whereHas('application', function ($q) {
                $q->where('status', 'accepted');
            });

        /* =====================================================
         * 3. FILTER DIVISI (BERDASARKAN SK PENUNJUKAN)
         * =====================================================
         * - Superadmin → bebas akses
         * - Admin bidang → hanya peserta divisinya
         * ===================================================== */
        if ($hakAkses->role !== 'superadmin') {
            $query->whereHas('application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        /* =====================================================
         * 4. FITUR PENCARIAN NAMA PESERTA (OPTIONAL)
         * ===================================================== */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user.profile', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(10);

        return view('admin.assessments.index', compact('members'));
    }

    /**
     * =====================================================
     * METHOD: create()
     * =====================================================
     * TUJUAN:
     * - Menampilkan form penilaian untuk satu peserta
     * - Mendukung mode edit jika nilai sudah ada
     *
     * SECURITY:
     * - Akses divisi diverifikasi sebelum data ditampilkan
     * =====================================================
     */
    public function create($member_id)
    {
        // Validasi hak akses admin terhadap peserta ini
        $this->checkAccess($member_id);

        /* =====================================================
         * Ambil data peserta + lowongan
         * ===================================================== */
        $member = ApplicationMemberMagang::with([
            'user.profile',
            'application.vacancy'
        ])->findOrFail($member_id);

        /* =====================================================
         * Cek apakah peserta sudah pernah dinilai
         * ===================================================== */
        $existingAssessment = AssessmentMagang::where('member_id', $member_id)->first();

        return view(
            'admin.assessments.create',
            compact('member', 'existingAssessment')
        );
    }

    /**
     * =====================================================
     * METHOD: store()
     * =====================================================
     * TUJUAN:
     * - Menyimpan atau memperbarui nilai peserta
     *
     * ATURAN BISNIS:
     * - Skor dalam rentang 0–100
     * - Skor akhir = rata-rata 3 komponen
     *
     * KEAMANAN:
     * - Hak akses dicek ulang (anti API abuse)
     * =====================================================
     */
    public function store(Request $request, $member_id)
    {
        // Security check ulang (POST / API safe)
        $this->checkAccess($member_id);

        /* =====================================================
         * 1. VALIDASI INPUT NILAI
         * ===================================================== */
        $request->validate([
            'score_behavior'    => 'required|numeric|min:0|max:100',
            'score_discipline'  => 'required|numeric|min:0|max:100',
            'score_performance' => 'required|numeric|min:0|max:100',
            'evaluation_notes'  => 'nullable|string',
            'additional_notes'  => 'nullable|string',
        ]);

        /* =====================================================
         * 2. HITUNG SKOR AKHIR
         * =====================================================
         * Rumus:
         * (Perilaku + Disiplin + Kinerja) / 3
         * ===================================================== */
        $finalScore = (
            $request->score_behavior +
            $request->score_discipline +
            $request->score_performance
        ) / 3;

        /* =====================================================
         * 3. AMBIL NAMA PENILAI OTOMATIS
         * ===================================================== */
        $namaPenilai = Auth::user()->name ?? 'Admin Magang';

        /* =====================================================
         * 4. SIMPAN NILAI (UPSERT)
         * =====================================================
         * - Jika sudah ada → UPDATE
         * - Jika belum → CREATE
         * ===================================================== */
        AssessmentMagang::updateOrCreate(
            ['member_id' => $member_id],
            [
                'assessor_name'     => $namaPenilai,
                'score_behavior'    => $request->score_behavior,
                'score_discipline'  => $request->score_discipline,
                'score_performance' => $request->score_performance,
                'final_score'       => $finalScore,
                'evaluation_notes'  => $request->evaluation_notes,
                'additional_notes'  => $request->additional_notes,
            ]
        );

        // Bersihkan cache dashboard agar statistik ter-update
        DashboardCache::clear();

        return redirect()
            ->route('admin.assessments.index')
            ->with(
                'success',
                'Nilai berhasil disimpan. Skor Akhir: ' . number_format($finalScore, 2)
            );
    }

    /**
     * =====================================================
     * HELPER: checkAccess()
     * =====================================================
     * TUJUAN:
     * - Memastikan admin hanya menilai peserta divisinya
     *
     * DIPAKAI OLEH:
     * - create()
     * - store()
     *
     * KEAMANAN:
     * - Mencegah penilaian lintas divisi
     * =====================================================
     */
    private function checkAccess($memberId)
    {
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        // Ambil data peserta target
        $targetMember = ApplicationMemberMagang::with(
            'application.vacancy'
        )->findOrFail($memberId);

        // Superadmin → bebas akses
        if ($hakAkses->role === 'superadmin') {
            return;
        }

        // Admin bidang → hanya divisi sendiri
        if ($hakAkses->division_name !== $targetMember->application->vacancy->division_name) {
            abort(
                403,
                'AKSES DITOLAK: Anda tidak berhak menilai peserta dari divisi lain.'
            );
        }
    }
}
