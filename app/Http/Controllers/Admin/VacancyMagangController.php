<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\MagangAccessRight;
use App\Models\VacancyMagang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Helpers\DashboardCache;
use App\Models\DivisionSetting;
use App\Models\Division;

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
            // tampilkan semua
        } else {
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

        $hakAkses = $this->getHakAkses();

        /*
    |--------------------------------------------------------------------------
    | Master Division Dropdown
    |--------------------------------------------------------------------------
    | Superadmin:
    |   Bisa memilih divisi dari master data.
    |
    | Admin Divisi:
    |   Tidak perlu dropdown karena otomatis terkunci
    |   sesuai division_name miliknya.
    |--------------------------------------------------------------------------
    */
        $divisions = $hakAkses->isSuperAdmin()
            ? Division::active()
            ->orderBy('name')
            ->pluck('name', 'name')
            : collect();

        return view('admin.vacancies.create', compact(
            'divisions',
            'hakAkses'
        ));
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

            /*
    |--------------------------------------------------------------------------
    | Division harus berasal dari master division aktif
    |--------------------------------------------------------------------------
    */
            $rules['division_name'] = [
                'required',
                'string',
                'max:100',

                Rule::in(
                    Division::active()
                        ->pluck('name')
                        ->toArray()
                ),
            ];
        }

        if ($request->registration_mode !== VacancyMagang::MODE_INDIVIDU) {
            $rules['min_members'] = 'required|integer|min:1';
            $rules['max_members'] = 'required|integer|min:1|gte:min_members';
        }

        $request->validate($rules);

        /*
|--------------------------------------------------------------------------
| Ambil nama resmi division dari master table
|--------------------------------------------------------------------------
| Mencegah:
| - typo
| - casing inconsistency
| - orphan division
|--------------------------------------------------------------------------
*/
        $division = $hakAkses->isSuperAdmin()

            ? Division::where('name', $request->division_name)
            ->firstOrFail()
            ->name

            : $hakAkses->division_name;

        [$min, $max] = $this->resolveMemberRange(
            $request->registration_mode,
            $request->min_members,
            $request->max_members
        );

        /**
         * =========================================================
         * FIX RACE CONDITION + QUOTA VALIDATION
         * =========================================================
         */
        $vacancy = null;

        DB::transaction(function () use (
            $request,
            $division,
            $min,
            $max,
            &$vacancy
        ) {
            /**
             * ---------------------------------------------------------
             * VALIDASI KAPASITAS DIVISI
             * ✅ Menggunakan isDivisionFull() sebagai single point
             *    of truth untuk mencegah duplikasi logic
             * ---------------------------------------------------------
             */
            if ($this->isDivisionFull($division)) {
                throw ValidationException::withMessages([
                    'division_name' =>
                    "Slot lowongan divisi \"{$division}\" sudah penuh. "
                        . 'Arsipkan lowongan yang sudah selesai atau naikkan batas kuota divisi.'
                ]);
            }

            /**
             * ---------------------------------------------------------
             * CREATE VACANCY
             * ✅ Nested DB::transaction dihapus — sudah terlindungi
             *    oleh outer transaction di atas
             * ---------------------------------------------------------
             */
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
        });

        /**
         * ✅ FIX P1006: Docblock memberitahu Intelephense bahwa
         *    $vacancy dijamin bertipe VacancyMagang setelah
         *    transaction selesai sukses
         *
         * @var VacancyMagang $vacancy
         */

        DashboardCache::clear();

        Log::info('Lowongan dibuat', [
            'admin_id'      => Auth::id(),
            'vacancy_id'    => $vacancy->id,
            'division_name' => $division,
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

        $hakAkses     = $this->getHakAkses();
        $hasApplicant = $vacancy->applications()->exists();

        /*
    |--------------------------------------------------------------------------
    | Master Division Dropdown
    |--------------------------------------------------------------------------
    */
        $divisions = $hakAkses->isSuperAdmin()
            ? Division::active()
            ->orderBy('name')
            ->pluck('name', 'name')
            : collect();

        return view('admin.vacancies.edit', compact(
            'vacancy',
            'hasApplicant',
            'divisions',
            'hakAkses'
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

            /*
    |--------------------------------------------------------------------------
    | Division harus berasal dari master division aktif
    |--------------------------------------------------------------------------
    */
            $rules['division_name'] = [
                'required',
                'string',
                'max:100',

                Rule::in(
                    Division::active()
                        ->pluck('name')
                        ->toArray()
                ),
            ];
        }

        $request->validate($rules);

        /*
|--------------------------------------------------------------------------
| Ambil nama resmi division dari master table
|--------------------------------------------------------------------------
| Mencegah:
| - typo
| - casing inconsistency
| - orphan division
|--------------------------------------------------------------------------
*/
        $newDivision = $hakAkses->isSuperAdmin()

            ? Division::where('name', $request->division_name)
            ->firstOrFail()
            ->name

            : $vacancy->division_name;

        // ✅ FIX PRIORITAS 1: Deteksi apakah divisi berubah
        //    agar bisa validasi kapasitas divisi tujuan
        $divisionChanged = $hakAkses->isSuperAdmin()
            && $newDivision !== $vacancy->division_name;

        $data = [
            'title'         => $request->title,
            'type'          => $request->type,
            'description'   => $request->description,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'division_name' => $newDivision,
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

        DB::transaction(function () use ($vacancy, $data, $newDivision, $divisionChanged) {

            // Lock vacancy terlebih dahulu
            $locked = VacancyMagang::where('id', $vacancy->id)
                ->lockForUpdate()
                ->firstOrFail();

            /**
             * ✅ FIX PRIORITAS 1: Validasi kapasitas divisi tujuan
             *    HANYA jika divisi benar-benar berubah.
             *
             * Tanpa ini, superadmin bisa memindahkan vacancy
             * ke divisi yang sudah penuh tanpa ada error —
             * menyebabkan occupancy overflow yang silent.
             */
            if ($divisionChanged) {

                if ($this->isDivisionFull($newDivision)) {
                    throw ValidationException::withMessages([
                        'division_name' =>
                        "Slot lowongan divisi \"{$newDivision}\" sudah penuh. "
                            . 'Arsipkan lowongan yang sudah selesai atau naikkan batas kuota divisi.'
                    ]);
                }
            }

            $locked->update($data);
        });

        DashboardCache::clear();

        Log::info('Lowongan diperbarui', [
            'admin_id'      => Auth::id(),
            'vacancy_id'    => $vacancy->id,
            'division_from' => $vacancy->division_name,
            'division_to'   => $newDivision,
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
     * =========================================================
     * ARCHIVE
     * =========================================================
     */
    public function archive(VacancyMagang $vacancy)
    {
        $this->authorize('update', $vacancy);

        DB::transaction(function () use ($vacancy) {

            $locked = VacancyMagang::where('id', $vacancy->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->isArchived()) {
                abort(422, 'Lowongan ini sudah diarsipkan.');
            }

            if ($locked->status !== VacancyMagang::STATUS_CLOSED) {
                abort(422, 'Hanya lowongan yang sudah ditutup yang dapat diarsipkan.');
            }

            $locked->update([
                'status' => VacancyMagang::STATUS_ARCHIVED
            ]);

            $vacancy->status = VacancyMagang::STATUS_ARCHIVED;
        });

        DashboardCache::clear();

        Log::info('Lowongan diarsipkan', [
            'vacancy_id'  => $vacancy->id,
            'title'       => $vacancy->title,
            'archived_by' => Auth::id(),
            'archived_at' => now()->toDateTimeString(),
        ]);

        return back()->with(
            'success',
            'Lowongan berhasil diarsipkan dan tidak akan tampil di publik.'
        );
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
     * HELPER: HAK AKSES
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
        int|string|null $min,
        int|string|null $max
    ): array {

        if ($mode === VacancyMagang::MODE_INDIVIDU) {
            return [1, 1];
        }

        return [
            (int) $min,
            (int) $max
        ];
    }

    /**
     * =========================================================
     * HELPER: CEK KAPASITAS DIVISI.
     * =========================================================
     */
    private function isDivisionFull(string $division): bool
    {
        $setting = DivisionSetting::where('division_name', $division)
            ->lockForUpdate()
            ->first();

        if (!$setting || !$setting->hasLimit()) {
            return false;
        }

        $currentCount = VacancyMagang::where('division_name', $division)
            ->whereIn('status', [
                VacancyMagang::STATUS_OPEN,
                VacancyMagang::STATUS_CLOSED,
            ])
            ->count();

        return $currentCount >= $setting->max_open_vacancies;
    }
}
