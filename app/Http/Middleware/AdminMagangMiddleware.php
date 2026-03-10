<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\MagangAccessRight;

class AdminMagangMiddleware
{

    /**
     * ===============================================================
     * ADMIN MAGANG ACCESS MIDDLEWARE
     * ===============================================================
     *
     * Middleware ini memastikan bahwa:
     *
     * 1. User login sebagai admin (guard:web)
     * 2. User memiliki hak akses modul magang
     *
     * Catatan:
     * Middleware hanya mengecek akses modul.
     * Pembatasan divisi tetap dilakukan di controller.
     *
     */

    public function handle(Request $request, Closure $next)
    {

        /**
         * ===========================================================
         * STEP 1 — AMBIL ADMIN LOGIN
         * ===========================================================
         */

        $admin = Auth::guard('web')->user();



        /**
         * ===========================================================
         * STEP 2 — FAIL SAFE (JIKA BELUM LOGIN)
         * ===========================================================
         */

        if (!$admin) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Sesi admin telah habis, silakan login kembali.'
                ]);
        }



        /**
         * ===========================================================
         * STEP 3 — CEK HAK AKSES MODUL MAGANG
         * ===========================================================
         *
         * Menggunakan cache untuk menghindari query berulang
         *
         */

        $cacheKey = 'admin_magang_access_' . $admin->id;



        $hasAccess = Cache::remember($cacheKey, 600, function () use ($admin) {

            return MagangAccessRight::where('user_id', $admin->id)
                ->exists();
        });



        if (!$hasAccess) {

            abort(403, 'Akses Ditolak: Akun Anda tidak terdaftar sebagai Admin Magang.');
        }



        /**
         * ===========================================================
         * STEP 4 — LANJUTKAN REQUEST
         * ===========================================================
         */

        return $next($request);
    }
}
