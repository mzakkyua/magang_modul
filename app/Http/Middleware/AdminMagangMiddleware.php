<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MagangAccessRight;

class AdminMagangMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Pastikan user sudah login
        if (!Auth::check()) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        // 2. Cek apakah user punya SK admin magang
        $hakAkses = MagangAccessRight::where('user_id', Auth::id())->first();

        if (!$hakAkses) {
            abort(403, 'Anda bukan Admin Magang.');
        }

        /**
         * PENTING:
         * - role = superadmin  → boleh semuanya
         * - role = admin       → tetap boleh masuk
         *
         * Middleware TIDAK membedakan divisi.
         * Divisi tetap dicek di controller.
         */

        // 3. Kalau lolos semua → lanjut ke controller
        return $next($request);
    }
}
