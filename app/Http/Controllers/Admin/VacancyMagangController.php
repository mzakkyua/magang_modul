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
     * HELPER: AMBIL HAK AKSES ADMIN
     * ========================================================= */
    private function getHakAkses()
    {
        // Ambil hak akses berdasarkan user admin yang sedang login (guard web)
        $hakAkses = MagangAccessRight::where('user_id', Auth::id())->first();

        // Jika tidak terdaftar di tabel access_right → tolak akses
        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki akses ke Modul Magang.');
        }

        return $hakAkses;
    }

    /* =========================================================
     * HELPER: PROTEKSI AKSES BERDASARKAN DIVISION
     * ========================================================= */
    private function authorizeDivision(VacancyMagang $vacancy)
    {
        $hakAkses = $this->getHakAkses();

        // Jika bukan superadmin dan mencoba akses divisi lain → tolak
        if (
            $hakAkses->role !== 'superadmin' &&
            $vacancy->division_name !== $hakAkses->division_name
        ) {
            abort(403, 'Anda tidak memiliki akses ke divisi ini.');
        }
    }

    /* =========================================================
     * INDEX - LIST LOWONGAN
     * ========================================================= */
    public function index()
    {
        $hakAkses = $this->getHakAkses();

        $query = VacancyMagang::withCount('applications');

        // Jika bukan superadmin → hanya lihat divisi sendiri
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

    /* =========================================================
     * STORE - BUAT LOWONGAN
     * ========================================================= */
    public function store(Request $request)
    {
        $hakAkses = $this->getHakAkses();

        // ===============================
        // VALIDASI DASAR
        // ===============================
        $baseRules = [
            'title'             => 'required|string|max:200',
            'type'              => 'required|in:magang,penelitian',
            'registration_mode' => 'required|in:individu,kelompok,hybrid',
            'quota_slots'       => 'required|integer|min:1',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'description'       => 'nullable|string',
        ];

        // Jika superadmin → wajib pilih division
        if ($hakAkses->role === 'superadmin') {
            $baseRules['division_name'] = 'required|string|max:100';
        }

        // Jika bukan individu → wajib min/max anggota
        if ($request->registration_mode !== 'individu') {
            $baseRules['min_members'] = 'required|integer|min:1';
            $baseRules['max_members'] = 'required|integer|min:1|gte:min_members';
        } else {
            $baseRules['min_members'] = 'nullable|integer|min:1';
            $baseRules['max_members'] = 'nullable|integer|min:1';
        }

        $request->validate($baseRules);

        // Tentukan division berdasarkan role
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
        $this->authorizeDivision($vacancy);

        $hasApplicant = $vacancy->applications()->exists();

        return view('admin.vacancies.edit', compact('vacancy', 'hasApplicant'));
    }

    /* =========================================================
     * UPDATE
     * ========================================================= */
    public function update(Request $request, VacancyMagang $vacancy)
    {
        $this->authorizeDivision($vacancy);

        $hakAkses = $this->getHakAkses();
        $hasApplicant = $vacancy->applications()->exists();

        $baseRules = [
            'title'       => 'required|string|max:200',
            'type'        => 'required|in:magang,penelitian',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ];

        if ($hakAkses->role === 'superadmin') {
            $baseRules['division_name'] = 'required|string|max:100';
        }

        $request->validate($baseRules);

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

        // Jika belum ada pendaftar → boleh ubah kuota dan mode
        if (!$hasApplicant) {

            $extraRules = [
                'registration_mode' => 'required|in:individu,kelompok,hybrid',
                'quota_slots'       => 'required|integer|min:1',
            ];

            if ($request->registration_mode !== 'individu') {
                $extraRules['min_members'] = 'required|integer|min:1';
                $extraRules['max_members'] = 'required|integer|min:1|gte:min_members';
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

    /* =========================================================
     * TOGGLE STATUS
     * ========================================================= */
    public function toggleStatus(VacancyMagang $vacancy)
    {
        $this->authorizeDivision($vacancy);

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
        $this->authorizeDivision($vacancy);

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
     * HELPER: ATUR RANGE ANGGOTA
     * ========================================================= */
    private function resolveMemberRange($mode, $min, $max)
    {
        if ($mode === 'individu') {
            return [1, 1];
        }

        return [$min, $max];
    }
}
