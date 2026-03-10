<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

use App\Models\UserMagang;
use App\Models\ProfileMagang;

class AuthMagangController extends Controller
{

    /**
     * ===============================================================
     * CONSTRUCTOR
     * ===============================================================
     *
     * Membatasi akses halaman login & register hanya untuk user
     * yang BELUM login.
     *
     * Guard yang digunakan:
     *
     * web    → admin / pegawai
     * magang → peserta magang
     *
     * logout tetap bisa diakses user yang sudah login.
     *
     */
    public function __construct()
    {
        $this->middleware('guest:magang')->except('logout');
        $this->middleware('guest:web')->except('logout');
    }



    /**
     * ===============================================================
     * SHOW REGISTER FORM
     * ===============================================================
     */
    public function showRegisterForm()
    {
        return view('auth.magang-register');
    }



    /**
     * ===============================================================
     * REGISTER PESERTA MAGANG
     * ===============================================================
     *
     * Flow:
     *
     * 1. Validasi input
     * 2. Normalisasi email
     * 3. Generate username unik
     * 4. Simpan user + profile (transaction)
     * 5. Auto login
     *
     */
    public function register(Request $request)
    {

        /**
         * ===========================================================
         * STEP 1 — VALIDASI INPUT
         * ===========================================================
         */

        $request->validate([
            'nama_lengkap'     => 'required|string|max:255',
            'email'            => 'required|email|unique:users_magang,email',
            'password'         => 'required|string|min:8|confirmed',
            'education_level'  => 'required|string|max:50',
            'nim_nisn'         => 'nullable|string|max:50',
            'terms'            => 'accepted',
        ], [
            'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ]);



        /**
         * ===========================================================
         * STEP 2 — NORMALISASI EMAIL
         * ===========================================================
         */

        $email = strtolower($request->email);



        /**
         * ===========================================================
         * STEP 3 — GENERATE USERNAME UNIK
         * ===========================================================
         *
         * Format:
         * username + random number
         *
         */

        do {

            $username = explode('@', $email)[0] . rand(100, 999);
        } while (UserMagang::where('username', $username)->exists());



        /**
         * ===========================================================
         * STEP 4 — DATABASE TRANSACTION
         * ===========================================================
         *
         * Semua proses dibungkus transaction untuk mencegah
         * data setengah tersimpan.
         *
         */

        DB::transaction(function () use ($request, $email, $username) {

            /**
             * CREATE USER ACCOUNT
             */
            $user = UserMagang::create([
                'username'      => $username,
                'email'         => $email,
                'password_hash' => Hash::make($request->password),
            ]);


            /**
             * CREATE BASIC PROFILE
             */

            ProfileMagang::create([
                'user_id'          => $user->id,
                'full_name'        => $request->nama_lengkap,
                'status'           => 'active',
                'nim_nisn'         => $request->nim_nisn,
                'education_level'  => $request->education_level,
                'institution_name' => '-',
                'major'            => '-',
                'phone_number'     => '-',
            ]);


            /**
             * AUTO LOGIN USER
             */

            Auth::guard('magang')->login($user);
        });



        /**
         * ===========================================================
         * STEP 5 — REGENERATE SESSION
         * ===========================================================
         *
         * Mencegah session fixation attack
         *
         */

        $request->session()->regenerate();



        return redirect()
            ->route('dashboard.index')
            ->with('success', 'Pendaftaran berhasil! Silakan lengkapi biodata Anda.');
    }



    /**
     * ===============================================================
     * SHOW LOGIN FORM
     * ===============================================================
     */

    public function showLoginForm()
    {
        return view('auth.magang-login');
    }



    /**
     * ===============================================================
     * LOGIN SYSTEM (ADMIN + PESERTA)
     * ===============================================================
     *
     * Flow login:
     *
     * 1. Validasi input
     * 2. Rate limiter check
     * 3. Login admin
     * 4. Login peserta
     * 5. Tambah hit jika gagal
     *
     */

    public function login(Request $request)
    {

        /**
         * ===========================================================
         * STEP 1 — VALIDASI INPUT
         * ===========================================================
         */

        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);



        /**
         * ===========================================================
         * STEP 2 — NORMALISASI EMAIL
         * ===========================================================
         */

        $email = strtolower($request->email);



        /**
         * ===========================================================
         * STEP 3 — RATE LIMITER KEY
         * ===========================================================
         *
         * Menggunakan kombinasi:
         * email + ip address
         *
         */

        $key = $email . '|' . $request->ip();



        /**
         * ===========================================================
         * STEP 4 — CEK BRUTE FORCE
         * ===========================================================
         */

        if (RateLimiter::tooManyAttempts($key, 5)) {

            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ]);
        }



        /**
         * ===========================================================
         * STEP 5 — PREPARE CREDENTIALS
         * ===========================================================
         */

        $credentials = [
            'email' => $email,
            'password' => $request->password
        ];



        /**
         * ===========================================================
         * STEP 6 — LOGIN ADMIN
         * ===========================================================
         */

        if (Auth::guard('web')->attempt($credentials)) {

            RateLimiter::clear($key);

            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }



        /**
         * ===========================================================
         * STEP 7 — LOGIN PESERTA
         * ===========================================================
         */

        if (Auth::guard('magang')->attempt($credentials)) {

            RateLimiter::clear($key);

            $request->session()->regenerate();

            return redirect()->route('dashboard.index');
        }



        /**
         * ===========================================================
         * STEP 8 — LOGIN GAGAL
         * ===========================================================
         */

        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }



    /**
     * ===============================================================
     * LOGOUT SYSTEM
     * ===============================================================
     *
     * Menghandle logout untuk:
     *
     * - admin
     * - peserta magang
     *
     */

    public function logout(Request $request)
    {

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        if (Auth::guard('magang')->check()) {
            Auth::guard('magang')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
