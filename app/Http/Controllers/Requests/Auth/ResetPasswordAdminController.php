<?php

namespace App\Http\Controllers\Requests\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordAdminController extends Controller
{
  // Menampilkan halaman form password baru setelah klik link dari email
  public function showResetForm(Request $request, $token = null)
  {
    return view('auth.passwords.reset-admin', [
      'token' => $token,
      'email' => $request->email
    ]);
  }

  // Memproses update password baru ke database
  public function reset(Request $request)
  {
    $request->validate([
      'token'    => 'required',
      'email'    => 'required|email',
      'password' => 'required|min:8|confirmed',
    ]);

    // Menggunakan broker 'users'
    $status = Password::broker('users')->reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function ($user, $password) {
        // Simpan password baru ke tabel users admin
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Bersihkan session admin ini di device lain demi keamanan
        DB::table('sessions')
          ->where('user_id', $user->id)
          ->delete();

        // Catat di log server
        Log::info('Password admin berhasil direset via email', [
          'admin_id'  => $user->id,
          'email'     => $user->email,
          'timestamp' => now()->toDateTimeString(),
        ]);

        event(new PasswordReset($user));
      }
    );

    return $status === Password::PASSWORD_RESET
      ? redirect()->route('login')->with('success', 'Password admin berhasil direset. Silakan login kembali.')
      : back()->withErrors(['email' => [__($status)]]);
  }
}
