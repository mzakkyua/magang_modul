<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DashboardCache;
use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\DivisionSetting;
use App\Models\MagangAccessRight;
use App\Models\VacancyMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DivisionController extends Controller
{
    /**
     * =========================================================
     * INDEX
     * =========================================================
     *
     * Menampilkan seluruh master divisi.
     *
     * Hanya superadmin yang boleh mengakses.
     */

    public function index()
    {
        $this->authorizeSuperAdmin();

        $divisions = Division::query()
            ->withCount([
                'vacancies as active_vacancies_count' => function ($query) {

                    $query->whereIn('status', [
                        VacancyMagang::STATUS_OPEN,
                        VacancyMagang::STATUS_CLOSED,
                    ]);
                }
            ])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.divisions.index', compact(
            'divisions'
        ));
    }

    /**
     * =========================================================
     * STORE
     * =========================================================
     *
     * Tambah master divisi baru.
     */

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',

                /**
                 * =====================================================
                 * VALIDASI CASE INSENSITIVE
                 * =====================================================
                 *
                 * Mencegah:
                 * - IT
                 * - it
                 * - It
                 *
                 * dianggap berbeda.
                 */
                function ($attribute, $value, $fail) {

                    $exists = Division::whereRaw(
                        'LOWER(name) = ?',
                        [strtolower(trim($value))]
                    )->exists();

                    if ($exists) {
                        $fail('Nama divisi sudah terdaftar.');
                    }
                }
            ]
        ]);

        /**
         * =========================================================
         * NORMALIZATION
         * =========================================================
         */

        $name = ucwords(trim($request->name));

        DB::transaction(function () use ($name) {

            /**
             * =====================================================
             * CREATE MASTER DIVISION
             * =====================================================
             */

            Division::create([
                'name'      => $name,
                'is_active' => true,
            ]);

            /**
             * =====================================================
             * AUTO CREATE DIVISION SETTING
             * =====================================================
             *
             * Default:
             * - quota = 6
             * - bisa diubah nanti di halaman kuota
             */

            DivisionSetting::create([
                'division_name'      => $name,
                'max_open_vacancies' => 6,
            ]);
        });

        DashboardCache::clear();

        Log::info('Divisi dibuat', [
            'admin_id' => Auth::id(),
            'name'     => $name,
        ]);

        return redirect()
            ->route('admin.divisions.index')
            ->with('success', "Divisi \"{$name}\" berhasil ditambahkan.");
    }

    /**
     * =========================================================
     * UPDATE
     * =========================================================
     *
     * Rename divisi.
     *
     * Ini termasuk HIGH IMPACT MUTATION karena:
     * - division_name adalah business identity
     * - occupancy bergantung pada division_name
     * - dashboard grouping bergantung pada division_name
     */

    public function update(Request $request, Division $division)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',

                function ($attribute, $value, $fail) use ($division) {

                    $exists = Division::whereRaw(
                        'LOWER(name) = ?',
                        [strtolower(trim($value))]
                    )
                        ->where('id', '!=', $division->id)
                        ->exists();

                    if ($exists) {
                        $fail('Nama divisi sudah digunakan.');
                    }
                }
            ]
        ]);

        $oldName = $division->name;
        $newName = ucwords(trim($request->name));

        /**
         * Tidak ada perubahan.
         */
        if ($oldName === $newName) {

            return redirect()
                ->route('admin.divisions.index')
                ->with('info', 'Tidak ada perubahan nama divisi.');
        }

        DB::transaction(function () use (
            $division,
            $oldName,
            $newName
        ) {

            /**
             * =====================================================
             * LOCK DIVISION
             * =====================================================
             *
             * Karena rename division sekarang adalah:
             * business identity mutation.
             */

            $lockedDivision = Division::where('id', $division->id)
                ->lockForUpdate()
                ->firstOrFail();

            /**
             * =====================================================
             * UPDATE MASTER DIVISION
             * =====================================================
             */

            $lockedDivision->update([
                'name' => $newName
            ]);

            /**
             * =====================================================
             * CASCADE UPDATE
             * =====================================================
             *
             * Menjaga seluruh subsystem tetap sinkron.
             */

            VacancyMagang::where(
                'division_name',
                $oldName
            )->update([
                'division_name' => $newName
            ]);

            DivisionSetting::where(
                'division_name',
                $oldName
            )->update([
                'division_name' => $newName
            ]);

            MagangAccessRight::where(
                'division_name',
                $oldName
            )->update([
                'division_name' => $newName
            ]);
        });

        DashboardCache::clear();

        Log::info('Nama divisi diubah', [
            'admin_id' => Auth::id(),
            'old_name' => $oldName,
            'new_name' => $newName,
        ]);

        return redirect()
            ->route('admin.divisions.index')
            ->with(
                'success',
                "Divisi berhasil diubah dari \"{$oldName}\" menjadi \"{$newName}\"."
            );
    }

    /**
     * =========================================================
     * TOGGLE ACTIVE
     * =========================================================
     */

    public function toggleActive(Division $division)
    {
        $this->authorizeSuperAdmin();

        /**
         * =====================================================
         * PROTECTION
         * =====================================================
         *
         * Tidak boleh nonaktifkan divisi
         * yang masih memiliki occupancy aktif.
         */

        if (
            $division->is_active
            && $division->hasActiveVacancies()
        ) {

            return back()->withErrors([
                'error' =>
                "Divisi \"{$division->name}\" tidak dapat dinonaktifkan karena masih memiliki lowongan aktif."
            ]);
        }

        $division->update([
            'is_active' => !$division->is_active,
        ]);

        DashboardCache::clear();

        $status = $division->is_active
            ? 'diaktifkan'
            : 'dinonaktifkan';

        return back()->with(
            'success',
            "Divisi \"{$division->name}\" berhasil {$status}."
        );
    }

    /**
     * =========================================================
     * DESTROY
     * =========================================================
     */

    public function destroy(Division $division)
    {
        $this->authorizeSuperAdmin();

        /**
         * =====================================================
         * PROTECTION
         * =====================================================
         *
         * Tidak boleh hapus jika masih memiliki
         * data vacancy.
         */

        if ($division->vacancies()->exists()) {

            return back()->withErrors([
                'error' =>
                "Divisi \"{$division->name}\" tidak dapat dihapus karena masih memiliki data lowongan."
            ]);
        }

        $name = $division->name;

        DB::transaction(function () use ($division) {

            /**
             * =====================================================
             * CLEANUP SETTING
             * =====================================================
             */

            DivisionSetting::where(
                'division_name',
                $division->name
            )->delete();

            $division->delete();
        });

        DashboardCache::clear();

        Log::info('Divisi dihapus', [
            'admin_id' => Auth::id(),
            'name'     => $name,
        ]);

        return redirect()
            ->route('admin.divisions.index')
            ->with(
                'success',
                "Divisi \"{$name}\" berhasil dihapus."
            );
    }

    /**
     * =========================================================
     * HELPER
     * =========================================================
     */

    private function authorizeSuperAdmin(): void
    {
        $hakAkses = request()
            ->attributes
            ->get('magang_access');

        if (
            !$hakAkses
            || !$hakAkses->isSuperAdmin()
        ) {

            abort(
                403,
                'Hanya Superadmin yang dapat mengelola divisi.'
            );
        }
    }
}
