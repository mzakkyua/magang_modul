<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\MagangAccessRight;

/**
 * ======================================================================
 * MIDDLEWARE: AdminMagangMiddleware
 * ======================================================================
 */
class AdminMagangMiddleware
{

  // Role yang diizinkan mengakses modul magang
  private const VALID_ROLES = [
    MagangAccessRight::ROLE_SUPERADMIN,
    MagangAccessRight::ROLE_DIVISION_ADMIN,
  ];

  // Cache TTL dalam detik
  private const CACHE_TTL = 600; // 10 menit


  public function handle(Request $request, Closure $next)
  {
    /*
        ==============================================================
        STEP 1 — AMBIL ADMIN LOGIN
        ==============================================================
        */
    $admin = Auth::guard('web')->user();

    /*
        ==============================================================
        STEP 2 — FAIL SAFE (SESSION HABIS / BELUM LOGIN)
        ==============================================================
        Redirect ke route login yang sesuai untuk admin.
        CATATAN: Pastikan route 'login' di project ini mengarah ke
        halaman login admin, bukan login peserta magang.
        Jika berbeda, ganti ke route('admin.login') atau sesuaikan.
        ==============================================================
        */
    if (!$admin) {
      return redirect()
        ->route('login')
        ->withErrors([
          'email' => 'Sesi admin telah habis, silakan login kembali.'
        ]);
    }

    /*
        ==============================================================
        STEP 3 — AMBIL HAK AKSES DARI CACHE
        ==============================================================
        IMPROVEMENT dari versi sebelumnya:

        SEBELUMNYA: Cache hanya simpan boolean (exists())
          → Controller tetap harus query MagangAccessRight sendiri
          → Total = 2 query per request (middleware + controller)

        SEKARANG: Cache simpan object (role + division_name)
          → Controller ambil dari $request->attributes, tidak query DB
          → Total = 1 query per 10 menit per admin (dari cache)

        Jika null (cache miss) → query DB dan simpan hasilnya.
        Jika return null dari DB → tidak punya akses → abort 403.
        ==============================================================
        */
    $cacheKey = 'admin_magang_access_' . $admin->id;

    $access = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($admin) {
      return MagangAccessRight::select('role', 'division_name')
        ->where('user_id', $admin->id)
        ->first();
    });

    /*
        ==============================================================
        STEP 4 — VALIDASI AKSES & ROLE
        ==============================================================
        Dua pengecekan:
        1. Record harus ada (tidak null)
        2. Role harus valid — mencegah record dengan role null/rusak lolos
        ==============================================================
        */
    if (!$access || !in_array($access->role, self::VALID_ROLES, true)) {
      abort(403, 'Akses Ditolak: Akun Anda tidak terdaftar sebagai Admin Magang.');
    }

    /*
        ==============================================================
        STEP 5 — INJECT ACCESS KE REQUEST
        ==============================================================
        Access object di-share ke controller via $request->attributes
        agar controller tidak perlu query MagangAccessRight lagi.

        CARA PAKAI DI CONTROLLER:
          $hakAkses = $request->attributes->get('magang_access');
          $isSuperAdmin = $hakAkses->role === 'superadmin';
          $division = $hakAkses->division_name;

        Ini menggantikan pattern yang sebelumnya ada di setiap controller:
          $hakAkses = MagangAccessRight::where('user_id', Auth::id())->first();
        ==============================================================
        */
    $request->attributes->set('magang_access', $access);

    /*
        ==============================================================
        STEP 6 — LANJUTKAN REQUEST
        ==============================================================
        */
    return $next($request);
  }
}
