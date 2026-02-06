<?php

use Illuminate\Support\Facades\Route;

// Panggil semua Controller yang sudah kita buat biar rapi
use App\Http\Controllers\AuthMagangController;
use App\Http\Controllers\DashboardMagangController;
use App\Http\Controllers\ProfileMagangController;
use App\Http\Controllers\ApplicationMagangController;
use App\Http\Controllers\LandingController;

// Panggil Controller Admin
use App\Http\Controllers\Admin\VacancyMagangController;
use App\Http\Controllers\Admin\ApplicationVerificationController;
use App\Http\Controllers\Admin\AdminDashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini kita mendaftarkan semua jalur URL aplikasi.
|
*/

// ========================================================================
// 1. ZONA PUBLIK (BEBAS AKSES)
// Siapapun (Tamu/Member/Admin) bisa buka halaman ini
// ========================================================================

Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/lowongan/{id}', [LandingController::class, 'show'])->name('landing.show');

// ========================================================================
// 1. ZONA TAMU (GUEST)
// Hanya bisa diakses kalau BELUM Login
// ========================================================================

Route::middleware(['guest:magang'])->group(function () {
    
    // Login Routes
    Route::get('/login', [AuthMagangController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthMagangController::class, 'login']);

    // Register Routes
    Route::get('/register', [AuthMagangController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthMagangController::class, 'register']);
});

// ========================================================================
// 2. ZONA PESERTA / MAHASISWA (AUTH MAGANG)
// Wajib Login sebagai user magang baru bisa akses
// ========================================================================

Route::middleware(['auth:magang'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthMagangController::class, 'logout'])->name('logout');

    // Dashboard (Pemisahan Magang vs Penelitian ada di sini)
    Route::get('/dashboard', [DashboardMagangController::class, 'index'])->name('dashboard');

    // Profil (Lengkapi Biodata & Upload CV)
    Route::get('/profile', [ProfileMagangController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileMagangController::class, 'update'])->name('profile.update');

    // Proses Pendaftaran (Submit Lamaran)
    // Note: Kita tidak butuh form 'create' khusus karena formnya ada di modal dashboard
    Route::post('/apply', [ApplicationMagangController::class, 'store'])->name('apply.store');

});

// ========================================================================
// 3. ZONA ADMIN DINAS (AUTH PEGAWAI)
// Wajib Login sebagai Admin (Guard: web/default)
// ========================================================================

// Prefix 'admin' -> URL jadi: domain.com/admin/vacancies
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    // --- MANAJEMEN LOWONGAN (CRUD) ---
    Route::resource('vacancies', VacancyMagangController::class);

    // --- DASHBOARD ADMIN GRAFIK ---
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Route Spesial: Manual Close (Saklar Admin)
    // Pakai PATCH karena kita mengubah sebagian data (status)
    Route::patch('/vacancies/{id}/toggle', [VacancyMagangController::class, 'toggleStatus'])
        ->name('vacancies.toggle');

    // --- VERIFIKASI LAMARAN ---
    // Lihat Daftar Pelamar
    Route::get('/applications', [ApplicationVerificationController::class, 'index'])
        ->name('applications.index');
        
    // Lihat Detail Satu Pelamar
    Route::get('/applications/{id}', [ApplicationVerificationController::class, 'show'])
        ->name('applications.show');
        
    // Eksekusi Terima/Tolak
    Route::patch('/applications/{id}/update-status', [ApplicationVerificationController::class, 'updateStatus'])
        ->name('applications.update-status');

});