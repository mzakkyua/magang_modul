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
        // A. Validasi Input
        // Pastikan email & username belum pernah dipakai orang lain
        $request->validate([
            'username' => 'required|string|max:50|unique:users_magang,username',
            'email'    => 'required|email|max:100|unique:users_magang,email',
            'password' => 'required|string|min:6|confirmed', // confirmed = harus ada field password_confirmation di form
            
            // Validasi Data Profil Awal (Bisa ditambah sesuai form register)
            'full_name' => 'required|string|max:150',
            'nim_nisn'  => 'required|string|max:50',
            'education_level' => 'required|in:siswa_smk,mahasiswa', // Penanda SMK/Mahasiswa
        ]);

        // B. Proses Simpan (Pakai Transaction lagi biar aman)
        // Kita harus simpan ke 2 Tabel: users_magang DAN profiles_magang
        DB::transaction(function () use ($request) {
            
            // 1. Buat Akun Login
            $user = UserMagang::create([
                'username' => $request->username,
                'email'    => $request->email,
                'password_hash' => Hash::make($request->password), // Enkripsi Password!
            ]);

            // 2. Buat Profil Biodata
            // Profil otomatis dibuat saat register, sisanya bisa diedit nanti (update profile)
            ProfileMagang::create([
                'user_id'   => $user->id,
                'full_name' => $request->full_name,
                'nim_nisn'  => $request->nim_nisn,
                'education_level' => $request->education_level,
                
                // Field lain kita kosongkan dulu (nullable di database),
                // Nanti user disuruh "Lengkapi Profil" setelah login.
                'institution_name' => '-', 
                'major' => '-',
                'phone_number' => '-',
            ]);
            
            // 3. Auto Login (Opsional)
            // Begitu daftar, langsung masuk dashboard tanpa perlu login ulang
            Auth::guard('magang')->login($user);
        });

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat! Silakan lengkapi profil Anda.');
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
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // B. Cek Login Pakai Guard 'magang'
        // PENTING: Kita pakai Auth::guard('magang'), bukan Auth::attempt() biasa.
        // Karena Auth::attempt() biasa itu ngecek ke tabel 'users' (Pegawai).
        if (Auth::guard('magang')->attempt(['username' => $request->username, 'password' => $request->password])) {
            
            // Jika Sukses:
            $request->session()->regenerate(); // Security: Cegah session fixation
            return redirect()->intended('dashboard'); // Redirect ke halaman yg mau dituju
        }

        // C. Jika Gagal:
        return back()->withErrors([
            'username' => 'Login gagal! Username atau password salah.',
        ])->onlyInput('username');
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