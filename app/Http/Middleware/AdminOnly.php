<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    // Cek apakah user sudah login
    if (!Auth::check()) {
      return redirect('/login');
    }

    // Cek apakah user memiliki hak akses admin di tabel MagangAccessRight
    $hakAkses = MagangAccessRight::where('user_id', Auth::id())->first();

    if (!$hakAkses) {
      abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai admin magang.');
    }

    return $next($request);
  }
}
