<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationMemberMagang;
use App\Models\AssessmentMagang;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

class AssessmentController extends Controller
{
    /**
     * =====================================================
     * METHOD: index()
     * =====================================================
     * Menampilkan daftar peserta yang sudah accepted
     * dan dapat dinilai oleh admin sesuai hak akses.
     */
    public function index(Request $request)
    {
        $hakAkses = $this->getHakAkses();

        $query = ApplicationMemberMagang::with([
            'user.profile',
            'application.vacancy',
            'assessment'
        ])
            ->whereHas('application', function ($q) {
                $q->where('status', 'accepted');
            });

        /**
         * Filter divisi:
         * Superadmin bebas lihat semua.
         * Admin bidang hanya divisinya.
         */
        if (!$hakAkses->isSuperAdmin()) {
            $query->whereHas('application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        /**
         * Search nama peserta
         */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('user.profile', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            });
        }

        $members = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.assessments.index', compact('members'));
    }

    /**
     * =====================================================
     * METHOD: create()
     * =====================================================
     * Menampilkan form penilaian / edit nilai peserta.
     */
    public function create($member_id)
    {
        $member = $this->checkAccess($member_id);

        $existingAssessment = AssessmentMagang::where(
            'member_id',
            $member_id
        )->first();

        return view(
            'admin.assessments.create',
            compact('member', 'existingAssessment')
        );
    }

    /**
     * =====================================================
     * METHOD: store()
     * =====================================================
     * Simpan / update nilai peserta.
     */
    public function store(Request $request, $member_id)
    {
        $member = $this->checkAccess($member_id);

        $request->validate([
            'score_behavior'    => 'required|numeric|min:0|max:100',
            'score_discipline'  => 'required|numeric|min:0|max:100',
            'score_performance' => 'required|numeric|min:0|max:100',
            'evaluation_notes'  => 'nullable|string|max:2000',
            'additional_notes'  => 'nullable|string|max:2000',
        ]);

        /**
         * Hitung nilai akhir
         */
        $finalScore = round(
            (
                $request->score_behavior +
                $request->score_discipline +
                $request->score_performance
            ) / 3,
            2
        );

        /** @var \App\Models\User $admin */
        $admin = Auth::user();

        $namaPenilai = $admin->name ?? 'Admin Magang';

        /**
         * Upsert penilaian
         */
        AssessmentMagang::updateOrCreate(
            [
                'member_id' => $member->id
            ],
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

        /**
         * Refresh dashboard stats
         */
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
     * HELPER: getHakAkses()
     * =====================================================
     * Ambil hak akses dari middleware cache.
     */
    private function getHakAkses(): MagangAccessRight
    {
        $hakAkses = request()->attributes->get('magang_access');

        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki hak akses penilaian.');
        }

        return $hakAkses;
    }

    /**
     * =====================================================
     * HELPER: checkAccess()
     * =====================================================
     * Pastikan admin hanya menilai peserta divisinya.
     */
    private function checkAccess($memberId): ApplicationMemberMagang
    {
        $hakAkses = $this->getHakAkses();

        $targetMember = ApplicationMemberMagang::with([
            'user.profile',
            'application.vacancy'
        ])->findOrFail($memberId);

        /**
         * Superadmin bebas akses
         */
        if ($hakAkses->isSuperAdmin()) {
            return $targetMember;
        }

        /**
         * Admin bidang hanya divisinya
         */
        if (
            $hakAkses->division_name !==
            $targetMember->application->vacancy->division_name
        ) {
            abort(
                403,
                'AKSES DITOLAK: Anda tidak berhak menilai peserta dari divisi lain.'
            );
        }

        return $targetMember;
    }
}
