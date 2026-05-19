<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApplicationMemberMagang;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Models\VacancyMagang;
use Illuminate\Http\Request;

/**
 * ======================================================================
 * CONTROLLER: PesertaController
 * ======================================================================
 *
 * Halaman rekap terpusat untuk admin — menampilkan SEMUA peserta magang
 * beserta informasi lengkap: status magang, nilai, dan sertifikat.
 *
 * Admin tidak perlu loncat-loncat antar halaman. Satu halaman,
 * semua data, langsung bisa dicari dan difilter.
 *
 * FITUR:
 *   - Search by nama / email peserta
 *   - Filter by status magang (all / pending / verified / interview /
 *                              accepted / completed / resigned)
 *   - Filter by divisi (hanya superadmin)
 *   - Filter by status sertifikat (semua / sudah / belum)
 *   - Pagination
 *   - Aksi langsung: nilai → assessment, belum sertifikat → upload
 * ======================================================================
 */
class PesertaController extends Controller
{
    // ======================================================================
    // KONFIGURASI
    // ======================================================================

    private const PER_PAGE = 15;

    /** Semua status yang dikenali sistem */
    private const ALL_STATUSES = [
        'pending'   => 'Menunggu',
        'verified'  => 'Terverifikasi',
        'interview' => 'Interview',
        'accepted'  => 'Diterima',
        'completed' => 'Selesai',
        'rejected'  => 'Ditolak',
        'resigned'  => 'Mengundurkan Diri',
    ];

    // ======================================================================
    // INDEX — REKAP PESERTA TERPUSAT
    // ======================================================================

    public function index(Request $request)
    {
        $hakAkses = request()->attributes->get('magang_access');

        // ── Base query ────────────────────────────────────────────────────
        $query = ApplicationMemberMagang::with([
            'user.profile',
            'application.vacancy',
            'assessment',
            'certificate',
        ]);

        // ── Filter divisi (admin biasa hanya lihat divisinya) ─────────────
        if ($hakAkses && !$hakAkses->isSuperAdmin()) {
            $query->whereHas('application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        // ── Filter: Search nama / email ───────────────────────────────────
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user.profile', function ($p) use ($search) {
                    $p->where('full_name', 'like', "%{$search}%");
                })->orWhereHas('user', function ($u) use ($search) {
                    $u->where('email', 'like', "%{$search}%");
                });
            });
        }

        // ── Filter: Status magang ─────────────────────────────────────────
        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereHas('application', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // ── Filter: Divisi (superadmin bisa pilih) ────────────────────────
        if ($hakAkses && $hakAkses->isSuperAdmin() && $request->filled('divisi') && $request->divisi !== 'all') {
            $query->whereHas('application.vacancy', function ($q) use ($request) {
                $q->where('division_name', $request->divisi);
            });
        }

        // ── Filter: Status sertifikat ─────────────────────────────────────
        if ($request->filled('sertifikat') && $request->sertifikat !== 'all') {
            if ($request->sertifikat === 'sudah') {
                $query->whereHas('certificate');
            } elseif ($request->sertifikat === 'belum') {
                $query->whereDoesntHave('certificate');
            }
        }

        // ── Ambil daftar divisi untuk filter dropdown (superadmin) ────────
        $divisiList = collect(); // selalu collection, bukan array

        if ($hakAkses && $hakAkses->isSuperAdmin()) {
            $divisiList = VacancyMagang::select('division_name')
                ->distinct()
                ->orderBy('division_name')
                ->pluck('division_name');
        }

        // ── Eksekusi query dengan pagination ──────────────────────────────
        $members = $query
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // ── Statistik ringkas untuk summary bar ───────────────────────────
        $statsQuery = ApplicationMemberMagang::query();
        if ($hakAkses && !$hakAkses->isSuperAdmin()) {
            $statsQuery->whereHas('application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        /**
         * --------------------------------------------------------------
         * TOTAL PESERTA
         * --------------------------------------------------------------
         */
        $totalMembers = (clone $statsQuery)->count();

        /**
         * --------------------------------------------------------------
         * STATUS COUNTS (1 QUERY GROUP BY)
         * --------------------------------------------------------------
         */
        $statusCounts = (clone $statsQuery)
            ->join(
                'applications_magang',
                'application_members_magang.application_id',
                '=',
                'applications_magang.id'
            )
            ->selectRaw('applications_magang.status, COUNT(*) as total')
            ->groupBy('applications_magang.status')
            ->pluck('total', 'status');

        /**
         * --------------------------------------------------------------
         * PESERTA BERSERTIFIKAT
         * --------------------------------------------------------------
         */
        $bersertifCount = (clone $statsQuery)
            ->whereHas('certificate')
            ->count();

        /**
         * --------------------------------------------------------------
         * PESERTA BELUM DINILAI
         * --------------------------------------------------------------
         *
         * Hanya peserta accepted
         * yang belum punya assessment.
         */
        $belumNilaiCount = (clone $statsQuery)
            ->whereHas('application', function ($q) {
                $q->where('status', 'accepted');
            })
            ->whereDoesntHave('assessment')
            ->count();

        /**
         * --------------------------------------------------------------
         * FINAL ARRAY
         * --------------------------------------------------------------
         */
        $stats = [
            'total' => $totalMembers,

            'aktif' => (int) (
                $statusCounts['accepted'] ?? 0
            ),

            'selesai' => (int) (
                $statusCounts['completed'] ?? 0
            ),

            'bersertif' => $bersertifCount,

            'belum_nilai' => $belumNilaiCount,
        ];

        return view('admin.peserta.index', compact(
            'members',
            'divisiList',
            'stats',
        ))->with('allStatuses', self::ALL_STATUSES);
    }
}
