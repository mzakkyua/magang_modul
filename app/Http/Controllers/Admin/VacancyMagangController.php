<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

class VacancyMagangController extends Controller
{
    /* =========================================================
     * HELPER: HAK AKSES
     * ========================================================= */
    private function getHakAkses()
    {
        $hakAkses = MagangAccessRight::where('user_id', Auth::id())->first();

        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki akses ke Modul Magang.');
        }

        return $hakAkses;
    }

    /* =========================================================
     * INDEX
     * ========================================================= */
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

    /* =========================================================
     * CREATE
     * ========================================================= */
    public function create()
    {
        return view('admin.vacancies.create');
    }

    public function store(Request $request)
    {
        $hakAkses = $this->getHakAkses();

        $request->validate([
            'title'             => 'required|string|max:200',
            'type'              => 'required|in:magang,penelitian',
            'registration_mode' => 'required|in:individu,kelompok,hybrid',
            'quota_slots'       => 'required|integer|min:1',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'description'       => 'nullable|string',
            'min_members'       => 'nullable|integer|min:1',
            'max_members'       => 'nullable|integer|min:1',
        ]);

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

    /* =========================================================
     * EDIT
     * ========================================================= */
    public function edit(VacancyMagang $vacancy)
    {
        $hakAkses = $this->getHakAkses();

        if (
            $hakAkses->role !== 'superadmin' &&
            $vacancy->division_name !== $hakAkses->division_name
        ) {
            abort(403, 'Anda tidak boleh mengedit lowongan divisi lain.');
        }

        $hasApplicant = $vacancy->applications()->exists();

        return view('admin.vacancies.edit', compact('vacancy', 'hasApplicant', 'hakAkses'));
    }

    /* =========================================================
     * UPDATE
     * ========================================================= */
    public function update(Request $request, VacancyMagang $vacancy)
    {
        $hakAkses = $this->getHakAkses();
        $hasApplicant = $vacancy->applications()->exists();

        $request->validate([
            'title'       => 'required|string|max:200',
            'type'        => 'required|in:magang,penelitian',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        // ===============================
        // DATA YANG SELALU BOLEH DIUBAH
        // ===============================
        $data = [
            'title'       => $request->title,
            'type'        => $request->type,
            'description' => $request->description,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'division_name' => $hakAkses->role === 'superadmin'
                ? $request->division_name
                : $vacancy->division_name,
        ];

        // ===============================
        // JIKA BELUM ADA PENDAFTAR
        // ===============================
        if (!$hasApplicant) {
            $request->validate([
                'registration_mode' => 'required|in:individu,kelompok,hybrid',
                'quota_slots'       => 'required|integer|min:1',
                'min_members'       => 'nullable|integer|min:1',
                'max_members'       => 'nullable|integer|min:1',
            ]);

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

    /* =========================================================
     * TOGGLE STATUS
     * ========================================================= */
    public function toggleStatus(VacancyMagang $vacancy)
    {
        $vacancy->update([
            'status' => $vacancy->status === 'open' ? 'closed' : 'open',
        ]);

        DashboardCache::clear();

        return back()->with('success', 'Status lowongan berhasil diubah.');
    }

    /* =========================================================
     * DELETE
     * ========================================================= */
    public function destroy(VacancyMagang $vacancy)
    {
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

    /* =========================================================
     * HELPER: ATUR MIN / MAX ANGGOTA
     * ========================================================= */
    private function resolveMemberRange($mode, $min, $max)
    {
        if ($mode === 'individu') {
            return [1, 1];
        }

        if ($max < $min) {
            abort(422, 'Jumlah maksimal anggota harus ≥ minimal.');
        }

        return [$min, $max];
    }
}
