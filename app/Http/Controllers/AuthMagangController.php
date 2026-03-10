<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\UserMagang;
use App\Models\ProfileMagang;
use App\Http\Controllers\Controller;


use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthMagangController extends Controller
{
    // =================================================================
    // 1. BAGIAN REGISTER (DAFTAR AKUN)
    // =================================================================
    /**
     * Constructor
     * Pastikan guest saja yang bisa akses login & register
     */
    public function __construct()
    {
        $this->middleware('guest:magang')->except('logout');
        $this->middleware('guest:web')->except('logout');
    }

    // ==========================================================
    // 1. REGISTER (PESERTA MAGANG)
    // ==========================================================

    public function showRegisterForm()
    {
        return view('auth.magang-register'); // Pastikan view ini nanti dibuat
    }

    public function register(Request $request)
    {
        // ===============================
        // A. VALIDASI INPUT
        // ===============================
        $request->validate([
            'nama_lengkap'     => 'required|string|max:255',
            'email'            => 'required|email|unique:users_magang,email',
            'password'         => 'required|string|min:8|confirmed',
            'education_level'  => 'required|string|max:50',
            'terms'            => 'accepted',
        ], [
            'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ]);

        // ===============================
        // B. NORMALISASI EMAIL
        // ===============================
        $email = strtolower($request->email);

        // ===============================
        // C. GENERATE USERNAME UNIK
        // ===============================
        do {
            $username = explode('@', $email)[0] . rand(100, 999);
        } while (UserMagang::where('username', $username)->exists());

        // ===============================
        // D. TRANSACTION DATABASE
        // ===============================
        DB::transaction(function () use ($request, $email, $username) {

            // 1️⃣ Buat akun login
            $user = UserMagang::create([
                'username'      => $username,
                'email'         => $email,
                'password_hash' => Hash::make($request->password),
            ]);

            // 2️⃣ Buat profil dasar
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

            // 3️⃣ Auto login peserta
            Auth::guard('magang')->login($user);
        });

        // ===============================
        // E. REGENERATE SESSION (ANTI SESSION FIXATION)
        // ===============================
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard.index')
            ->with('success', 'Pendaftaran berhasil! Silakan lengkapi biodata Anda.');
    }

    // ==========================================================
    // 2. LOGIN (ADMIN & PESERTA)
    // ==========================================================

    public function showLoginForm()
    {
        return view('auth.magang-login');
    }

    public function login(Request $request)
    {
        // ==========================================================
        // 1️⃣ VALIDASI INPUT DASAR
        // ==========================================================
        // Pastikan email & password tidak kosong
        // Laravel otomatis redirect balik jika gagal
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // ==========================================================
        // 2️⃣ NORMALISASI EMAIL (ANTI CASE SENSITIVE ISSUE)
        // ==========================================================
        // Supaya Email@Gmail.com dan email@gmail.com dianggap sama
        $email = strtolower($request->email);

        // ==========================================================
        // 3️⃣ BUAT UNIQUE KEY UNTUK RATE LIMITER
        // ==========================================================
        // Key dibuat dari kombinasi:
        // - email
        // - IP address user
        //
        // Tujuannya:
        // Supaya satu IP tidak bisa brute force satu email terus-menerus
        $key = strtolower($email) . '|' . $request->ip();

        // ==========================================================
        // 4️⃣ CEK APAKAH SUDAH TERLALU BANYAK PERCOBAAN LOGIN
        // ==========================================================
        // Maksimal 5 percobaan dalam 60 detik
        if (RateLimiter::tooManyAttempts($key, 5)) {

            // Ambil sisa waktu lock (detik)
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ]);
        }

        // ==========================================================
        // 5️⃣ SIAPKAN CREDENTIAL UNTUK AUTH
        // ==========================================================
        $credentials = [
            'email' => $email,
            'password' => $request->password
        ];

        // ==========================================================
        // 6️⃣ COBA LOGIN SEBAGAI ADMIN (GUARD: web)
        // ==========================================================
        if (Auth::guard('web')->attempt($credentials)) {

            // Jika berhasil, hapus hit rate limiter
            RateLimiter::clear($key);

            // Regenerate session (anti session fixation attack)
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        // ==========================================================
        // 7️⃣ COBA LOGIN SEBAGAI PESERTA MAGANG (GUARD: magang)
        // ==========================================================
        if (Auth::guard('magang')->attempt($credentials)) {

            // Jika berhasil, hapus hit rate limiter
            RateLimiter::clear($key);

            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            return redirect()->route('dashboard.index');
        }

        // ==========================================================
        // 8️⃣ JIKA LOGIN GAGAL
        // ==========================================================
        // Tambahkan 1 hit percobaan login gagal
        // Dan set durasi blokir selama 60 detik
        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'email' => 'Email tidak terdaftar sebagai Pegawai maupun Peserta Magang.',
        ])->onlyInput('email');
    }

    // ==========================================================
    // 3. LOGOUT (UNIVERSAL)
    // ==========================================================

    public function logout(Request $request)
    {
        // Logout admin jika sedang login
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Logout peserta jika sedang login
        if (Auth::guard('magang')->check()) {
            Auth::guard('magang')->logout();
        }

        // Hapus session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
