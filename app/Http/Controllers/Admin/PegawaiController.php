<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MagangAccessRight;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * ======================================================================
 * CONTROLLER: PegawaiController
 * ======================================================================
 */
class PegawaiController extends Controller
{

    // ======================================================================
    // INDEX — DAFTAR PEGAWAI
    // ======================================================================

    public function index(Request $request)
    {
        $hakAkses = $request->attributes->get('magang_access');

        if (!$hakAkses || !$hakAkses->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang dapat mengelola Data Pegawai.');
        }

        $pegawai = User::with('magangAccessRight')
            ->orderBy('name', 'asc')
            ->paginate(15);

        $divisions = \App\Models\Division::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.pegawai.index', compact(
            'pegawai',
            'divisions'
        ));
    }


    // ======================================================================
    // STORE ACCESS — BERI / UBAH HAK AKSES
    // ======================================================================

    public function storeAccess(Request $request, int $userId)
    {
        $hakAkses = $request->attributes->get('magang_access');

        if (!$hakAkses || !$hakAkses->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang dapat mengubah hak akses.');
        }

        // PERBAIKAN: Menggunakan konstanta untuk validasi
        $request->validate([
            'role' => [
                'required',
                'in:' . MagangAccessRight::ROLE_SUPERADMIN . ',' . MagangAccessRight::ROLE_DIVISION_ADMIN,
            ],
            'division_name' => [
                'nullable',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    // PERBAIKAN: Menggunakan konstanta
                    if ($request->role === MagangAccessRight::ROLE_DIVISION_ADMIN && blank($value)) {
                        $fail('Divisi wajib dipilih untuk Admin Bidang.');
                        return;
                    }

                    if (!blank($value)) {
                        $exists = Division::active()->where('name', $value)->exists();
                        if (!$exists) {
                            $fail('Divisi tidak valid atau sudah nonaktif.');
                        }
                    }
                }
            ],
        ]);

        $currentAccess = MagangAccessRight::where('user_id', $userId)->first();

        // PERBAIKAN: Menggunakan konstanta
        if ($currentAccess && $currentAccess->role === MagangAccessRight::ROLE_SUPERADMIN && $request->role !== MagangAccessRight::ROLE_SUPERADMIN) {
            $superadminCount = MagangAccessRight::where('role', MagangAccessRight::ROLE_SUPERADMIN)->count();
            if ($superadminCount <= 1) {
                return back()->with('error', 'Minimal harus ada 1 superadmin aktif.');
            }
        }

        $access = MagangAccessRight::updateOrCreate(
            ['user_id' => $userId],
            [
                'role' => $request->role,
                'division_name' => $request->role === MagangAccessRight::ROLE_SUPERADMIN ? null : $request->division_name,
            ]
        );

        Cache::forget($this->cacheKey($userId));

        Log::info('Hak akses pegawai diperbarui', [
            'by_admin_id' => Auth::id(),
            'user_id' => $userId,
            'role' => $request->role,
            'division' => $request->division_name ?? 'semua',
            'timestamp' => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Hak akses pegawai berhasil diperbarui!');
    }


    // ======================================================================
    // DESTROY ACCESS — CABUT HAK AKSES
    // ======================================================================

    public function destroyAccess(Request $request, int $userId)
    {
        /*
        ==============================================================
        SUPERADMIN ONLY
        ==============================================================
        */
        $hakAkses = $request->attributes->get('magang_access');

        if (!$hakAkses || !$hakAkses->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang dapat mencabut hak akses.');
        }

        /*
        ==============================================================
        GUARD 1:
        TIDAK BISA CABUT AKSES DIRI SENDIRI
        ==============================================================
        */
        if ((int) $userId === (int) Auth::id()) {

            return back()->with(
                'error',
                'Anda tidak dapat mencabut hak akses Anda sendiri!'
            );
        }

        /*
        ==============================================================
        GUARD 2:
        TIDAK BISA HAPUS SUPERADMIN TERAKHIR
        ==============================================================
        */
        $targetAccess = MagangAccessRight::where('user_id', $userId)->first();

        // PERBAIKAN: Menggunakan konstanta
        if ($targetAccess && $targetAccess->role === MagangAccessRight::ROLE_SUPERADMIN) {
            $superadminCount = MagangAccessRight::where('role', MagangAccessRight::ROLE_SUPERADMIN)->count();
            if ($superadminCount <= 1) {
                return back()->with('error', 'Tidak dapat mencabut akses! Minimal harus ada 1 superadmin yang aktif.');
            }
        }

        /*
        ==============================================================
        DELETE ACCESS
        ==============================================================
        */
        MagangAccessRight::where(
            'user_id',
            $userId
        )->delete();

        /*
        ==============================================================
        CLEAR CACHE
        ==============================================================
        */
        Cache::forget($this->cacheKey($userId));

        /*
        ==============================================================
        LOGGING
        ==============================================================
        */
        Log::info('Hak akses pegawai dicabut', [

            'by_admin_id' => Auth::id(),

            'user_id' => $userId,

            'timestamp' => now()->toDateTimeString(),
        ]);

        /*
        ==============================================================
        RESPONSE
        ==============================================================
        */
        return back()->with(
            'success',
            'Hak akses pegawai berhasil dicabut!'
        );
    }


    // ======================================================================
    // PRIVATE HELPERS
    // ======================================================================

    /**
     * Cache key yang identik dengan AdminMagangMiddleware.
     */
    private function cacheKey(int|string $userId): string
    {
        return 'admin_magang_access_' . $userId;
    }
}
