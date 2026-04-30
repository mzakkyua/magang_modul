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

class ResetPasswordMagangController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset-magang', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('magang')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {

                // 1. Simpan password baru
                $user->password_hash = Hash::make($password);

                // 2. Invalidate remember_token lama
                //    Mencegah sesi "Ingat Saya" lama tetap aktif
                $user->setRememberToken(Str::random(60));

                $user->save();

                // 3. Hapus semua session aktif user ini di database
                //    Jika ada yang login di device lain, mereka akan ter-logout
                DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();

                // 4. Audit log
                Log::info('Password peserta direset via email', [
                    'user_id'   => $user->id,
                    'email'     => $user->email,
                    'timestamp' => now()->toDateTimeString(),
                ]);

                // 5. Trigger event Laravel (best practice)
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login kembali.')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
