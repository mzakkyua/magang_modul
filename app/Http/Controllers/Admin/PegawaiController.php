<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MagangAccessRight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * ======================================================================
 * CONTROLLER: PegawaiController
 * ======================================================================
 *
 * Mengelola data pegawai dan hak akses modul magang.
 * Hanya bisa diakses oleh Superadmin.
 *
 * IMPROVEMENT DARI VERSI SEBELUMNYA:
 *
 * 🔴 CRITICAL
 *   - [FIX] Cache::forget() dipanggil setiap kali hak akses diubah
 *           atau dicabut — agar AdminMagangMiddleware tidak melayani
 *           akses yang sudah tidak valid sampai cache 10 menit habis
 *
 *   - [FIX] Superadmin tidak bisa mencabut akses superadmin lain
 *           jika hanya tersisa 1 superadmin — mencegah sistem terkunci
 *
 *   - [FIX] resolveHakAkses() dipakai via $request->attributes
 *           (injected oleh AdminMagangMiddleware) — tidak query DB lagi
 *
 * 🟡 MAINTAINABILITY
 *   - [FIX] Audit log ditambahkan di setiap mutasi hak akses
 *   - [FIX] Cache key dipusatkan ke helper private agar konsisten
 *           dengan key yang dipakai AdminMagangMiddleware
 *
 * ======================================================================
 */
class PegawaiController extends Controller
{

    // ======================================================================
    // INDEX — DAFTAR PEGAWAI
    // ======================================================================

    public function index(Request $request)
    {
        /*
        ==============================================================
        VALIDASI: HANYA SUPERADMIN
        ==============================================================
        Ambil dari $request->attributes yang sudah di-inject
        oleh AdminMagangMiddleware — tidak perlu query DB lagi.
        ==============================================================
        */
        $hakAkses = $request->attributes->get('magang_access');

        if (!$hakAkses || !$hakAkses->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang dapat mengelola Data Pegawai.');
        }

        $pegawai = User::with('magangAccess')
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('admin.pegawai.index', compact('pegawai'));
    }


    // ======================================================================
    // STORE ACCESS — BERI / UBAH HAK AKSES
    // ======================================================================

    public function storeAccess(Request $request, $userId)
    {
        // Superadmin only
        $hakAkses = $request->attributes->get('magang_access');
        if (!$hakAkses || !$hakAkses->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang dapat mengubah hak akses.');
        }

        $request->validate([
            'role' => 'required|in:superadmin,admin_bidang',
            'division_name' =>
            'required_if:role,admin_bidang|nullable|string|max:100',
        ]);

        $access = MagangAccessRight::updateOrCreate(
            ['user_id' => $userId],
            [
                'role'          => $request->role,
                'division_name' => $request->role === 'superadmin' ? null : $request->division_name,
            ]
        );

        /*
        ==============================================================
        WAJIB: CLEAR CACHE MIDDLEWARE
        ==============================================================
        AdminMagangMiddleware menyimpan hak akses di cache 10 menit.
        Jika tidak di-clear, perubahan hak akses tidak langsung berlaku
        — admin lama masih bisa akses sampai cache habis.

        Cache key harus identik dengan yang dibuat di middleware:
        'admin_magang_access_' . $admin->id
        ==============================================================
        */
        Cache::forget($this->cacheKey($userId));

        Log::info('Hak akses pegawai diperbarui', [
            'by_admin_id' => Auth::id(),
            'user_id'     => $userId,
            'role'        => $request->role,
            'division'    => $request->division_name ?? 'semua',
            'timestamp'   => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Hak akses pegawai berhasil diperbarui!');
    }


    // ======================================================================
    // DESTROY ACCESS — CABUT HAK AKSES
    // ======================================================================

    public function destroyAccess(Request $request, $userId)
    {
        // Superadmin only
        $hakAkses = $request->attributes->get('magang_access');
        if (!$hakAkses || !$hakAkses->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang dapat mencabut hak akses.');
        }

        /*
        ==============================================================
        GUARD 1: TIDAK BISA CABUT AKSES DIRI SENDIRI
        ==============================================================
        */
        if ((int) $userId === (int) Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mencabut hak akses Anda sendiri!');
        }

        /*
        ==============================================================
        GUARD 2: TIDAK BISA HAPUS SUPERADMIN TERAKHIR
        ==============================================================
        Mencegah kondisi di mana tidak ada superadmin tersisa,
        sehingga sistem terkunci dan tidak ada yang bisa masuk.
        ==============================================================
        */
        $targetAccess = MagangAccessRight::where('user_id', $userId)->first();

        if ($targetAccess && $targetAccess->role === 'superadmin') {
            $superadminCount = MagangAccessRight::where('role', 'superadmin')->count();

            if ($superadminCount <= 1) {
                return back()->with(
                    'error',
                    'Tidak dapat mencabut akses! Minimal harus ada 1 superadmin yang aktif.'
                );
            }
        }

        MagangAccessRight::where('user_id', $userId)->delete();

        // Clear cache agar middleware langsung tahu akses sudah dicabut
        Cache::forget($this->cacheKey($userId));

        Log::info('Hak akses pegawai dicabut', [
            'by_admin_id' => Auth::id(),
            'user_id'     => $userId,
            'timestamp'   => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Hak akses pegawai berhasil dicabut!');
    }


    // ======================================================================
    // PRIVATE HELPERS
    // ======================================================================

    /**
     * Cache key yang identik dengan AdminMagangMiddleware.
     * Dipusatkan di sini agar tidak typo jika ada perubahan format.
     */
    private function cacheKey(int|string $userId): string
    {
        return 'admin_magang_access_' . $userId;
    }
}
