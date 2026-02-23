<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
  public function handle(Request $request, Closure $next)
  {
    // Paksa cek guard web (admin)
    if (!Auth::guard('web')->check()) {
      return redirect()->route('login');
    }

    $admin = Auth::guard('web')->user();

    $hakAkses = MagangAccessRight::where('user_id', $admin->id)->first();

    if (!$hakAkses) {
      abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai admin magang.');
    }

    return $next($request);
  }
}
