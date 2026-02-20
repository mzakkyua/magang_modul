<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\UserMagang;
use App\Models\ProfileMagang;

class AuthMagangController extends Controller
{
    // =================================================================
    // 1. BAGIAN REGISTER (DAFTAR AKUN)
    // =================================================================

    public function showRegisterForm()
    {
        return view('auth.magang-register'); // Pastikan view ini nanti dibuat
    }

    public function register(Request $request)
    {
        /*
    |=====================================================
    | A. VALIDASI INPUT REGISTER
    |=====================================================
    | Catatan:
    | - education_level sengaja TIDAK diwajibkan di form
    | - Akan di-set default otomatis di backend
    |=====================================================
    */
        $request->validate([
            'username' => 'required|string|max:50|unique:users_magang,username',
            'email'    => 'required|email|max:100|unique:users_magang,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        /*
    |=====================================================
    | B. PROSES SIMPAN DATA (TRANSACTION)
    |=====================================================
    | Alasan:
    | - User & Profile harus konsisten
    | - Jika salah satu gagal → rollback
    |=====================================================
    */
        DB::transaction(function () use ($request) {

            /*
        |---------------------------------
        | 1. BUAT AKUN LOGIN PESERTA
        |---------------------------------
        */
            $user = UserMagang::create([
                'username'      => $request->username,
                'email'         => $request->email,
                'password_hash' => Hash::make($request->password),
            ]);

            /*
        |---------------------------------
        | 2. BUAT PROFIL AWAL PESERTA
        |---------------------------------
        | Catatan Penting:
        | - education_level WAJIB ADA
        | - Jika tidak dikirim form → default 'mahasiswa'
        |---------------------------------
        */
            ProfileMagang::create([
                'user_id'          => $user->id,
                'education_level'  => $request->education_level ?? 'mahasiswa',

                // Data opsional (boleh null / placeholder)
                'full_name'        => $request->full_name,
                'nim_nisn'         => $request->nim_nisn,
                'institution_name' => '-',
                'major'            => '-',
                'phone_number'     => '-',
            ]);

            /*
        |---------------------------------
        | 3. AUTO LOGIN PESERTA
        |---------------------------------
        */
            Auth::guard('magang')->login($user);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Akun berhasil dibuat! Silakan lengkapi profil Anda.');
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
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. CEK PERTAMA: Apakah ini ADMIN/PEGAWAI?
        // Kita suruh cek ke Guard 'web' (tabel users)
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            // Kalau ketemu, lempar ke Dashboard Admin
            return redirect()->intended(route('admin.dashboard'));
        }

        // 3. CEK KEDUA: Apakah ini MAHASISWA?
        // Kita suruh cek ke Guard 'magang' (tabel users_magang)
        if (Auth::guard('magang')->attempt($credentials)) {
            $request->session()->regenerate();
            // Kalau ketemu, lempar ke Dashboard Mahasiswa
            return redirect()->intended(route('dashboard'));
        }

        // 4. Kalau Dua-duanya Gagal
        return back()->withErrors([
            'email' => 'Email tidak terdaftar sebagai Pegawai maupun Peserta Magang.',
        ])->onlyInput('email');
    }

    // =================================================================
    // 3. BAGIAN LOGOUT (KELUAR)
    // =================================================================

    public function logout(Request $request)
    {
        Auth::guard('magang')->logout(); // Logout cuma yang magang

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
