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
        // 1. Karena di route kita sudah pakai 'auth:web', 
        // kita tinggal pastikan mengambil data user dari guard yang benar.
        $admin = Auth::guard('web')->user();

        // 2. Jika entah kenapa lolos tapi belum login di guard web, lempar ke halaman login
        if (!$admin) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi admin telah habis, silakan login kembali.']);
        }

        // 3. Cek apakah admin instansi ini punya hak akses untuk modul magang
        $hakAkses = MagangAccessRight::where('user_id', $admin->id)->first();

        if (!$hakAkses) {
            abort(403, 'Akses Ditolak: Akun Anda tidak terdaftar sebagai Admin Magang.');
        }

        /**
         * PENTING:
         * - role = superadmin  → boleh semuanya
         * - role = admin       → tetap boleh masuk
         * Middleware TIDAK membedakan divisi. Divisi tetap dicek di controller.
         */

        // 4. Kalau lolos semua → lanjut ke controller
        return $next($request);
    }
}
