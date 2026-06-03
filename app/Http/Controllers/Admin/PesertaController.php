<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApplicationMemberMagang;
use App\Http\Controllers\Controller;
use App\Models\VacancyMagang;
use Illuminate\Http\Request;

/**
 * ======================================================================
 * CONTROLLER: PesertaController
 * ======================================================================
 */
class PesertaController extends Controller
{
    private const PER_PAGE = 20;

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
    // INDEX — REKAP PESERTA TERPUSAT (compact list)
    // ======================================================================

    public function index(Request $request)
    {
        $hakAkses = request()->attributes->get('magang_access');

        $query = ApplicationMemberMagang::with([
            'user.profile',
            'application.vacancy',
            'assessment',
            'certificate',
        ]);

        if ($hakAkses && !$hakAkses->isSuperAdmin()) {
            $query->whereHas('application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

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

        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereHas('application', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($hakAkses && $hakAkses->isSuperAdmin() && $request->filled('divisi') && $request->divisi !== 'all') {
            $query->whereHas('application.vacancy', function ($q) use ($request) {
                $q->where('division_name', $request->divisi);
            });
        }

        if ($request->filled('sertifikat') && $request->sertifikat !== 'all') {
            if ($request->sertifikat === 'sudah') {
                $query->whereHas('certificate');
            } elseif ($request->sertifikat === 'belum') {
                $query->whereDoesntHave('certificate');
            }
        }

        $divisiList = collect();
        if ($hakAkses && $hakAkses->isSuperAdmin()) {
            $divisiList = VacancyMagang::select('division_name')
                ->distinct()
                ->orderBy('division_name')
                ->pluck('division_name');
        }

        $members = $query
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // ── Statistik ──────────────────────────────────────────────────
        $statsQuery = ApplicationMemberMagang::query();
        if ($hakAkses && !$hakAkses->isSuperAdmin()) {
            $statsQuery->whereHas('application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        $totalMembers = (clone $statsQuery)->count();

        $statusCounts = (clone $statsQuery)
            ->join('applications_magang', 'application_members_magang.application_id', '=', 'applications_magang.id')
            ->selectRaw('applications_magang.status, COUNT(*) as total')
            ->groupBy('applications_magang.status')
            ->pluck('total', 'status');

        $bersertifCount = (clone $statsQuery)->whereHas('certificate')->count();

        $belumNilaiCount = (clone $statsQuery)
            ->whereHas('application', fn($q) => $q->where('status', 'accepted'))
            ->whereDoesntHave('assessment')
            ->count();

        $stats = [
            'total'       => $totalMembers,
            'aktif'       => (int) ($statusCounts['accepted'] ?? 0),
            'selesai'     => (int) ($statusCounts['completed'] ?? 0),
            'bersertif'   => $bersertifCount,
            'belum_nilai' => $belumNilaiCount,
        ];

        return view('admin.peserta.index', compact('members', 'divisiList', 'stats'))
            ->with('allStatuses', self::ALL_STATUSES);
    }

    // ======================================================================
    // SHOW — DETAIL LENGKAP SATU PESERTA
    // ======================================================================

    public function show(ApplicationMemberMagang $member)
    {
        $hakAkses = request()->attributes->get('magang_access');

        // Guard: admin non-super hanya boleh lihat divisinya sendiri
        if ($hakAkses && !$hakAkses->isSuperAdmin()) {
            $divisi = optional(optional($member->application)->vacancy)->division_name;
            if ($divisi !== $hakAkses->division_name) {
                abort(403, 'Akses ditolak.');
            }
        }

        $member->load([
            'user.profile',
            'application.vacancy',
            'application.leader.profile',
            'assessment',
            'certificate',
        ]);

        return view('admin.peserta.show', compact('member'));
    }
}
