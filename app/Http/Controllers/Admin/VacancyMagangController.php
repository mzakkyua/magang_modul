<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

/**
 * =========================================================
 * CONTROLLER: VacancyMagangController
 * =========================================================
 * TANGGUNG JAWAB:
 * - CRUD Lowongan Magang
 * - Pengaturan kuota & mode pendaftaran
 *
 * KEAMANAN (LEVEL SEKARANG):
 * - Middleware  : auth + admin.magang (pintu gedung)
 * - Policy     : VacancyMagangPolicy (aturan edit data)
 *
 * CATATAN ARSITEKTUR:
 * - Controller fokus ke alur bisnis
 * - Aturan "boleh edit lowongan ini atau tidak"
 *   DIPINDAHKAN ke Policy
 * =========================================================
 */


class VacancyMagangController extends Controller
{
    use AuthorizesRequests;
    /**
     * =====================================================
     * HELPER: getHakAkses()
     * =====================================================
     * TUJUAN:
     * - Mengambil SK penunjukan admin magang
     *
     * CATATAN:
     * - Masih dipakai untuk kebutuhan NON-policy
     *   (misal: menentukan divisi default)
     * =====================================================
     */
    private function getHakAkses()
    {
        $hakAkses = MagangAccessRight::where('user_id', Auth::id())->first();

        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki akses ke Modul Magang.');
        }

        return $hakAkses;
    }

    /**
     * =====================================================
     * INDEX
     * =====================================================
     */
    public function index()
    {
        $hakAkses = $this->getHakAkses();

        $query = VacancyMagang::withCount('applications');

        if ($hakAkses->role !== 'superadmin') {
            $query->where('division_name', $hakAkses->division_name);
        }

        $vacancies = $query
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.vacancies.index', compact('vacancies'));
    }

    /**
     * =====================================================
     * CREATE
     * =====================================================
     */
    public function create()
    {
        return view('admin.vacancies.create');
    }

    /**
     * =====================================================
     * STORE
     * =====================================================
     */
    public function store(Request $request)
    {
        $hakAkses = $this->getHakAkses();

        // Validasi dasar
        $baseRules = [
            'title'             => 'required|string|max:200',
            'type'              => 'required|in:magang,penelitian',
            'registration_mode' => 'required|in:individu,kelompok,hybrid',
            'quota_slots'       => 'required|integer|min:1',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'description'       => 'nullable|string',
        ];

        // Jika bukan individu, wajibkan min/max members
        if ($request->registration_mode !== 'individu') {
            $baseRules['min_members'] = 'required|integer|min:1';
            $baseRules['max_members'] = 'required|integer|min:1|gte:min_members';
        } else {
            $baseRules['min_members'] = 'nullable|integer|min:1';
            $baseRules['max_members'] = 'nullable|integer|min:1';
        }

        $request->validate($baseRules);

        // Superadmin boleh pilih divisi, admin bidang terkunci
        $division = $hakAkses->role === 'superadmin'
            ? $request->division_name
            : $hakAkses->division_name;

        [$min, $max] = $this->resolveMemberRange(
            $request->registration_mode,
            $request->min_members,
            $request->max_members
        );

        VacancyMagang::create([
            'title'             => $request->title,
            'division_name'     => $division,
            'type'              => $request->type,
            'registration_mode' => $request->registration_mode,
            'quota_slots'       => $request->quota_slots,
            'min_members'       => $min,
            'max_members'       => $max,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'description'       => $request->description,
            'status'            => 'open',
        ]);

        DashboardCache::clear();

        return redirect()
            ->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil dibuat.');
    }

    /**
     * =====================================================
     * EDIT
     * =====================================================
     */
    public function edit(VacancyMagang $vacancy)
    {
        /**
         * POLICY CHECK
         * ---------------------------------------------
         * Aturan:
         * - Superadmin → boleh edit semua
         * - Admin bidang → hanya divisinya
         *
         * Jika tidak lolos → otomatis 403
         */
        $this->authorize('update', $vacancy);

        $hasApplicant = $vacancy->applications()->exists();

        return view(
            'admin.vacancies.edit',
            compact('vacancy', 'hasApplicant')
        );
    }

    /**
     * =====================================================
     * UPDATE
     * =====================================================
     */
    public function update(Request $request, VacancyMagang $vacancy)
    {
        /**
         * POLICY CHECK ULANG
         * ---------------------------------------------
         * Penting untuk mencegah update via POST / API
         */
        $this->authorize('update', $vacancy);

        $hakAkses = $this->getHakAkses();
        $hasApplicant = $vacancy->applications()->exists();

        // Validasi dasar (selalu ada)
        $baseRules = [
            'title'       => 'required|string|max:200',
            'type'        => 'required|in:magang,penelitian',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ];

        $request->validate($baseRules);

        $data = [
            'title'         => $request->title,
            'type'          => $request->type,
            'description'   => $request->description,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'division_name' => $hakAkses->role === 'superadmin'
                ? $request->division_name
                : $vacancy->division_name,
        ];

        if (!$hasApplicant) {
            $extraRules = [
                'registration_mode' => 'required|in:individu,kelompok,hybrid',
                'quota_slots'       => 'required|integer|min:1',
            ];

            // Jika bukan individu, wajibkan min/max members
            if ($request->registration_mode !== 'individu') {
                $extraRules['min_members'] = 'required|integer|min:1';
                $extraRules['max_members'] = 'required|integer|min:1|gte:min_members';
            } else {
                $extraRules['min_members'] = 'nullable|integer|min:1';
                $extraRules['max_members'] = 'nullable|integer|min:1';
            }

            $request->validate($extraRules);

            [$min, $max] = $this->resolveMemberRange(
                $request->registration_mode,
                $request->min_members,
                $request->max_members
            );

            $data = array_merge($data, [
                'registration_mode' => $request->registration_mode,
                'quota_slots'       => $request->quota_slots,
                'min_members'       => $min,
                'max_members'       => $max,
            ]);
        }

        $vacancy->update($data);
        DashboardCache::clear();

        return redirect()
            ->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil diperbarui.');
    }

    /**
     * =====================================================
     * TOGGLE STATUS
     * =====================================================
     */
    public function toggleStatus(VacancyMagang $vacancy)
    {
        $this->authorize('update', $vacancy);

        $vacancy->update([
            'status' => $vacancy->status === 'open' ? 'closed' : 'open',
        ]);

        DashboardCache::clear();

        return back()->with('success', 'Status lowongan berhasil diubah.');
    }

    /**
     * =====================================================
     * DELETE
     * =====================================================
     */
    public function destroy(VacancyMagang $vacancy)
    {
        $this->authorize('update', $vacancy);

        if ($vacancy->applications()->exists()) {
            return back()->withErrors([
                'error' => 'Lowongan tidak dapat dihapus karena sudah memiliki pendaftar.',
            ]);
        }

        $vacancy->delete();
        DashboardCache::clear();

        return redirect()
            ->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil dihapus.');
    }

    /**
     * =====================================================
     * HELPER: resolveMemberRange()
     * =====================================================
     */
    private function resolveMemberRange($mode, $min, $max)
    {
        if ($mode === 'individu') {
            return [1, 1];
        }

        // Validasi sudah ditangani di request->validate() dengan gte rule
        return [$min, $max];
    }
}
