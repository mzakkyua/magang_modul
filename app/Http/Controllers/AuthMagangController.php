<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Database\QueryException;

use App\Models\UserMagang;
use App\Models\ProfileMagang;

use App\Http\Controllers\Requests\Auth\RegisterMagangRequest;
use App\Http\Controllers\Requests\Auth\LoginMagangRequest;

/**
 * ==========================================================
 * AuthMagangController (FINAL VERSION)
 * ==========================================================
 *
 * FITUR:
 * - Register peserta magang
 * - Login admin + peserta
 * - Logout aman
 * - Rate limiter login
 * - Session hardening
 * - Username collision safe
 * - Audit log
 *
 * ==========================================================
 */
class AuthMagangController extends Controller
{
    /**
     * ------------------------------------------------------
     * Constructor
     * ------------------------------------------------------
     */
    public function __construct()
    {
        $this->middleware('guest:magang')->except('logout');
        $this->middleware('guest:web')->except('logout');
    }

    /**
     * ======================================================
     * SHOW REGISTER FORM
     * ======================================================
     */
    public function showRegisterForm()
    {
        return view('auth.magang-register');
    }

    /**
     * ======================================================
     * REGISTER
     * ======================================================
     */
    public function register(RegisterMagangRequest $request)
    {
        $email = $request->email;
        /** @var UserMagang|null $user */
        $user = null;

        DB::transaction(function () use ($request, $email, &$user) {

            for ($i = 0; $i < 10; $i++) {

                try {
                    $username = $this->generateUsernameCandidate($email);

                    $user = UserMagang::create([
                        'username'      => $username,
                        'email'         => $email,
                        'password_hash' => Hash::make($request->password),
                    ]);

                    ProfileMagang::create([
                        'user_id' => $user->id,
                        'full_name' => $request->nama_lengkap,
                        'education_level' => $request->education_level,
                    ]);

                    return;
                } catch (QueryException $e) {

                    if ($e->getCode() == 23000) {
                        continue;
                    }

                    throw $e;
                }
            }

            throw new \Exception('Gagal membuat username unik.');
        });

        if (!$user) {
            throw new \Exception('Registrasi gagal.');
        }

        Auth::guard('magang')->login($user);
        /**
         * Anti session fixation
         */
        $request->session()->regenerate();

        DB::table('sessions')
            ->where('id', $request->session()->getId())
            ->update(['auth_guard' => 'magang']);


        Log::info('Register peserta berhasil', [
            'user_id' => $user->id,
            'email'   => $email,
            'ip'      => $request->ip(),
        ]);

        return redirect()
            ->route('dashboard.index')
            ->with('success', 'Pendaftaran berhasil! Selamat datang.');
    }

    /**
     * ======================================================
     * SHOW LOGIN FORM
     * ======================================================
     */
    public function showLoginForm()
    {
        return view('auth.magang-login');
    }

    /**
     * ======================================================
     * LOGIN
     * ======================================================
     */
    public function login(LoginMagangRequest $request)
    {
        $email = $request->email;

        /**
         * Key limiter
         */
        $key = $email . '|' . $request->ip();

        /**
         * Max 5 attempt / menit
         */
        if (RateLimiter::tooManyAttempts($key, 5)) {

            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ]);
        }

        $remember = $request->boolean('remember');

        $credentials = [
            'email'    => $email,
            'password' => $request->password,
        ];

        /**
         * ==========================================
         * LOGIN ADMIN
         * ==========================================
         */
        if (Auth::guard('web')->attempt($credentials, $remember)) {

            RateLimiter::clear($key);

            $request->session()->regenerate();

            DB::table('sessions')
                ->where('id', $request->session()->getId())
                ->update([
                    'auth_guard' => 'web',
                ]);

            Log::info('Login admin berhasil', [
                'user_id' => Auth::guard('web')->id(),
                'email'   => $email,
                'ip'      => $request->ip(),
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        /**
         * ==========================================
         * LOGIN PESERTA
         * ==========================================
         */
        if (Auth::guard('magang')->attempt($credentials, $remember)) {

            RateLimiter::clear($key);

            $request->session()->regenerate();

            DB::table('sessions')
                ->where('id', $request->session()->getId())
                ->update([
                    'auth_guard' => 'magang',
                ]);

            Log::info('Login peserta berhasil', [
                'user_id' => Auth::guard('magang')->id(),
                'email'   => $email,
                'ip'      => $request->ip(),
            ]);

            return redirect()->route('dashboard.index');
        }

        /**
         * Login gagal
         */
        RateLimiter::hit($key, 60);

        Log::warning('Login gagal', [
            'email'   => $email,
            'ip'      => $request->ip(),
            'attempt' => RateLimiter::attempts($key),
        ]);

        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
            ->onlyInput('email');
    }

    /**
     * ======================================================
     * LOGOUT
     * ======================================================
     */
    public function logout(Request $request)
    {
        $guard = null;
        $userId = null;

        if (Auth::guard('web')->check()) {
            $guard = 'web';
            $userId = Auth::guard('web')->id();

            Auth::guard('web')->logout();
        }

        if (Auth::guard('magang')->check()) {
            $guard = 'magang';
            $userId = Auth::guard('magang')->id();

            Auth::guard('magang')->logout();
        }

        /**
         * Hancurkan session lama
         */
        $request->session()->invalidate();

        /**
         * Token baru
         */
        $request->session()->regenerateToken();

        Log::info('Logout berhasil', [
            'guard'   => $guard,
            'user_id' => $userId,
            'ip'      => $request->ip(),
        ]);

        return redirect('/');
    }

    /**
     * ======================================================
     * HELPER USERNAME
     * ======================================================
     */
    private function generateUsernameCandidate(string $email): string
    {
        $prefix = explode('@', $email)[0];

        $prefix = preg_replace('/[^a-zA-Z0-9]/', '', $prefix);

        return strtolower($prefix) . rand(100, 9999);
    }
}
