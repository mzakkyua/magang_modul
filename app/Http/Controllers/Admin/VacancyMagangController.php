<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VacancyMagang;
use App\Models\MagangAccessRight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\DashboardCache;

class VacancyMagangController extends Controller
{
    /**
     * =========================================================
     * INDEX
     * =========================================================
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', VacancyMagang::class);

        $hakAkses = $this->getHakAkses();
        $base     = $this->buildBaseQuery($hakAkses);

        // Hitung semua status untuk ditampilkan di tab
        $rawCounts = (clone $base)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalVacancies      = $rawCounts->sum();
        $openCount           = $rawCounts->get(VacancyMagang::STATUS_OPEN, 0);
        $closedCount         = $rawCounts->get(VacancyMagang::STATUS_CLOSED, 0);
        $archivedCount       = $rawCounts->get(VacancyMagang::STATUS_ARCHIVED, 0);
        $withApplicantsCount = (clone $base)->has('applications')->count();

        // Filter berdasarkan tab yang dipilih
        // Default: tampilkan hanya yang aktif (open + closed), sembunyikan archived
        $activeTab = $request->query('tab', 'active');

        $query = (clone $base)
            ->select([
                'id',
                'title',
                'division_name',
                'type',
                'registration_mode',
                'quota_slots',
                'status',
                'start_date',
                'end_date',
                'created_at',
            ])
            ->withCount('applications')
            ->orderByDesc('created_at');

        if ($activeTab === 'archived') {
            $query->where('status', VacancyMagang::STATUS_ARCHIVED);
        } elseif ($activeTab === 'all') {
            // Tampilkan semua tanpa filter
        } else {
            // Default 'active': sembunyikan archived agar tidak mengotori daftar utama
            $query->whereIn('status', [
                VacancyMagang::STATUS_OPEN,
                VacancyMagang::STATUS_CLOSED,
            ]);
        }

        $vacancies = $query->paginate(10)->withQueryString();

        return view('admin.vacancies.index', compact(
            'vacancies',
            'totalVacancies',
            'openCount',
            'closedCount',
            'archivedCount',
            'withApplicantsCount',
            'activeTab'
        ));
    }

    /**
     * =========================================================
     * CREATE
     * =========================================================
     */
    public function create()
    {
        $this->authorize('create', VacancyMagang::class);

        return view('admin.vacancies.create');
    }

    /**
     * =========================================================
     * STORE
     * =========================================================
     */
    public function store(Request $request)
    {
        $this->authorize('create', VacancyMagang::class);

        $hakAkses = $this->getHakAkses();

        $rules = [
            'title'             => 'required|string|max:200',
            'type'              => 'required|in:' . implode(',', [
                VacancyMagang::TYPE_MAGANG,
                VacancyMagang::TYPE_PENELITIAN
            ]),
            'registration_mode' => 'required|in:' . implode(',', [
                VacancyMagang::MODE_INDIVIDU,
                VacancyMagang::MODE_KELOMPOK,
                VacancyMagang::MODE_HYBRID
            ]),
            'quota_slots'       => 'required|integer|min:1',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'description'       => 'nullable|string|max:5000',
        ];

        if ($hakAkses->isSuperAdmin()) {
            $rules['division_name'] = 'required|string|max:100';
        }

        if ($request->registration_mode !== VacancyMagang::MODE_INDIVIDU) {
            $rules['min_members'] = 'required|integer|min:1';
            $rules['max_members'] = 'required|integer|min:1|gte:min_members';
        }

        $request->validate($rules);

        $division = $hakAkses->isSuperAdmin()
            ? $request->division_name
            : $hakAkses->division_name;

        [$min, $max] = $this->resolveMemberRange(
            $request->registration_mode,
            $request->min_members,
            $request->max_members
        );

        $vacancy = VacancyMagang::create([
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
            'status'            => VacancyMagang::STATUS_OPEN,
        ]);

        DashboardCache::clear();

        Log::info('Lowongan dibuat', [
            'admin_id'   => Auth::id(),
            'vacancy_id' => $vacancy->id,
        ]);

        return redirect()
            ->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil dibuat.');
    }

    /**
     * =========================================================
     * EDIT
     * =========================================================
     */
    public function edit(VacancyMagang $vacancy)
    {
        $this->authorize('update', $vacancy);

        $hasApplicant = $vacancy->applications()->exists();

        return view('admin.vacancies.edit', compact(
            'vacancy',
            'hasApplicant'
        ));
    }

    /**
     * =========================================================
     * UPDATE
     * =========================================================
     */
    public function update(Request $request, VacancyMagang $vacancy)
    {
        $this->authorize('update', $vacancy);

        $hakAkses     = $this->getHakAkses();
        $hasApplicant = $vacancy->applications()->exists();

        $rules = [
            'title'       => 'required|string|max:200',
            'type'        => 'required|in:' . implode(',', [
                VacancyMagang::TYPE_MAGANG,
                VacancyMagang::TYPE_PENELITIAN
            ]),
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:5000',
        ];

        if ($hakAkses->isSuperAdmin()) {
            $rules['division_name'] = 'required|string|max:100';
        }

        $request->validate($rules);

        $data = [
            'title'       => $request->title,
            'type'        => $request->type,
            'description' => $request->description,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'division_name' => $hakAkses->isSuperAdmin()
                ? $request->division_name
                : $vacancy->division_name,
        ];

        if (!$hasApplicant) {
            $extraRules = [
                'registration_mode' => 'required|in:' . implode(',', [
                    VacancyMagang::MODE_INDIVIDU,
                    VacancyMagang::MODE_KELOMPOK,
                    VacancyMagang::MODE_HYBRID
                ]),
                'quota_slots' => 'required|integer|min:1',
            ];

            if ($request->registration_mode !== VacancyMagang::MODE_INDIVIDU) {
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

        DB::transaction(function () use ($vacancy, $data) {
            $locked = VacancyMagang::where('id', $vacancy->id)
                ->lockForUpdate()
                ->firstOrFail();

            $locked->update($data);
        });

        DashboardCache::clear();

        Log::info('Lowongan diperbarui', [
            'admin_id'   => Auth::id(),
            'vacancy_id' => $vacancy->id,
        ]);

        return redirect()
            ->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil diperbarui.');
    }

    /**
     * =========================================================
     * TOGGLE STATUS
     * =========================================================
     */
    public function toggleStatus(VacancyMagang $vacancy)
    {
        $this->authorize('update', $vacancy);

        DB::transaction(function () use ($vacancy) {
            $locked = VacancyMagang::where('id', $vacancy->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Lowongan yang sudah diarsipkan tidak bisa di-toggle
            if ($locked->isArchived()) {
                abort(422, 'Lowongan yang sudah diarsipkan tidak dapat diubah statusnya.');
            }

            $newStatus = $locked->status === VacancyMagang::STATUS_OPEN
                ? VacancyMagang::STATUS_CLOSED
                : VacancyMagang::STATUS_OPEN;

            $locked->update([
                'status' => $newStatus
            ]);

            $vacancy->status = $newStatus;
        });

        DashboardCache::clear();

        return back()->with(
            'success',
            'Status lowongan berhasil diubah.'
        );
    }

    /**
     * Arsipkan lowongan — hanya bisa dilakukan saat status = closed.
     * Lowongan archived tidak bisa dibuka lagi dan tidak tampil di landing page.
     */
    public function archive(VacancyMagang $vacancy)
    {
        $this->authorize('update', $vacancy);

        if (!$vacancy->status === VacancyMagang::STATUS_CLOSED) {
            return back()->with(
                'error',
                'Hanya lowongan yang sudah ditutup yang dapat diarsipkan.'
            );
        }

        if ($vacancy->isArchived()) {
            return back()->with('error', 'Lowongan ini sudah diarsipkan.');
        }

        $vacancy->update(['status' => VacancyMagang::STATUS_ARCHIVED]);

        DashboardCache::clear();

        Log::info('Lowongan diarsipkan', [
            'vacancy_id'    => $vacancy->id,
            'title'         => $vacancy->title,
            'archived_by'   => Auth::id(),
            'archived_at'   => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Lowongan berhasil diarsipkan dan tidak akan tampil di publik.');
    }

    /**
     * =========================================================
     * DESTROY
     * =========================================================
     */
    public function destroy(VacancyMagang $vacancy)
    {
        $this->authorize('delete', $vacancy);

        if ($vacancy->applications()->exists()) {
            return back()->withErrors([
                'error' => 'Lowongan tidak dapat dihapus karena sudah memiliki pendaftar.'
            ]);
        }

        $vacancy->delete();

        DashboardCache::clear();

        return redirect()
            ->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil dihapus.');
    }

    /**
     * =========================================================
     * HELPER: HAK AKSES DARI MIDDLEWARE
     * =========================================================
     */
    private function getHakAkses(): MagangAccessRight
    {
        $hakAkses = request()->attributes->get('magang_access');

        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki akses ke Modul Magang.');
        }

        return $hakAkses;
    }

    /**
     * =========================================================
     * HELPER: BASE QUERY
     * =========================================================
     */
    private function buildBaseQuery(MagangAccessRight $hakAkses)
    {
        $query = VacancyMagang::query();

        if (!$hakAkses->isSuperAdmin()) {
            $query->where(
                'division_name',
                $hakAkses->division_name
            );
        }

        return $query;
    }

    /**
     * =========================================================
     * HELPER: RANGE MEMBER
     * =========================================================
     */
    private function resolveMemberRange(
        string $mode,
        $min,
        $max
    ): array {
        if ($mode === VacancyMagang::MODE_INDIVIDU) {
            return [1, 1];
        }

        return [(int) $min, (int) $max];
    }
}
