<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserMagang;
use App\Models\ProfileMagang;

class AuthMagangController extends Controller
{
    // =================================================================
    // 1. BAGIAN REGISTER (DAFTAR AKUN BARU)
    // =================================================================

    public function showRegisterForm()
    {
        return view('auth.magang-register');
    }

    public function register(Request $request)
    {
        // A. Validasi Input
        // Kita hanya minta Nama, Email, dan Password.
        // Pastikan di View, input namenya: nama_lengkap, email, password, password_confirmation
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:users_magang,email',
            'password'     => 'required|min:8|confirmed',
        ]);

        // B. Generate Username Otomatis
        // Ambil nama depan dari email + angka acak (Contoh: budi@gmail.com -> budi882)
        $username = explode('@', $request->email)[0] . rand(100, 999);

        // C. Simpan ke Database (User & Profile)
        // Kita bungkus transaction biar kalau gagal satu, gagal semua (aman)
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $username) {

            // 1. Buat User Login (Tabel users_magang)
            $user = UserMagang::create([
                'username'      => $username,
                'email'         => $request->email,
                'password_hash' => Hash::make($request->password), // Enkripsi password
            ]);

            // 2. Buat Profile Dasar (Tabel profile_magang)
            // Kolom lain (NIM, Univ, dll) biarkan NULL dulu karena belum diisi
            ProfileMagang::create([
                'user_id'      => $user->id,
                'full_name' => $request->nama_lengkap,
                'status'       => 'active',
            ]);

            // 3. Auto Login
            // Setelah daftar langsung masuk, tidak perlu login ulang
            Auth::guard('magang')->login($user);
        });

        // D. Redirect ke Dashboard
        return redirect()->route('dashboard')->with('success', 'Pendaftaran Berhasil! Silakan lengkapi biodata Anda.');
    }

    // =================================================================
    // 2. BAGIAN LOGIN (MASUK SISTEM)
    // =================================================================

    public function showLoginForm()
    {
        return view('auth.magang-login');
    }

    public function login(Request $request)
    {
        // A. Validasi Input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // B. Cek Login: ADMIN / PEGAWAI (Guard: web)
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect ke Dashboard Admin
            return redirect()->intended(route('admin.dashboard'));
        }

        // C. Cek Login: MAHASISWA / PESERTA (Guard: magang)
        if (Auth::guard('magang')->attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect ke Dashboard Peserta
            return redirect()->intended(route('landing.index'));
        }

        // D. Jika Gagal Login
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // =================================================================
    // 3. BAGIAN LOGOUT (KELUAR) - UNIVERSAL
    // =================================================================

    public function logout(Request $request)
    {
        // Cek siapa yang sedang login, lalu logout sesuai guard-nya

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();       // Logout Admin
        } elseif (Auth::guard('magang')->check()) {
            Auth::guard('magang')->logout();    // Logout Mahasiswa
        }

        // Bersihkan Sesi Browser
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Lempar kembali ke Halaman Depan (Landing Page)
        return redirect('/');
    }
}
