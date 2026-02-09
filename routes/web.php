<?php

use Illuminate\Support\Facades\Route;

// --- IMPORT CONTROLLER ---

// 1. Controller Umum & Peserta Magang
use App\Http\Controllers\AuthMagangController;
use App\Http\Controllers\DashboardMagangController;
use App\Http\Controllers\ProfileMagangController;
use App\Http\Controllers\ApplicationMagangController;
use App\Http\Controllers\LandingController;

// 2. Controller Admin
use App\Http\Controllers\Admin\VacancyMagangController;
use App\Http\Controllers\Admin\ApplicationVerificationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\AdminProfileController; // Pastikan ini di-import

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========================================================================
// ROUTE LOGOUT (UNIVERSAL)
// Bisa diakses oleh Admin (web) maupun Peserta (magang)
// ========================================================================
Route::post('/logout', [AuthMagangController::class, 'logout'])->name('logout');

// ========================================================================
// 1. ZONA PUBLIK (BEBAS AKSES)
// Siapapun (Tamu/Member/Admin) bisa buka halaman ini.
// PENTING: Jangan masukkan ke dalam middleware auth.
// ========================================================================

Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/lowongan/{id}', [LandingController::class, 'show'])->name('landing.show');


// ========================================================================
// 2. ZONA TAMU (GUEST)
// Hanya bisa diakses kalau BELUM Login (sebagai peserta magang)
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
// 3. ZONA PESERTA / MAHASISWA (AUTH MAGANG)
// Wajib Login sebagai user magang baru bisa akses
// ========================================================================

Route::middleware(['auth:magang'])->group(function () {

    // Dashboard Peserta
    Route::get('/dashboard', [DashboardMagangController::class, 'index'])->name('dashboard');

    // Profil Peserta (Lengkapi Biodata & Upload CV)
    Route::get('/profile', [ProfileMagangController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileMagangController::class, 'update'])->name('profile.update');

    // Proses Pendaftaran (Submit Lamaran)
    Route::post('/apply', [ApplicationMagangController::class, 'store'])->name('apply.store');
});


// ========================================================================
// 4. ZONA ADMIN DINAS (AUTH PEGAWAI)
// Wajib Login sebagai Admin (Guard: web/default)
// ========================================================================

// Prefix 'admin' -> URL jadi: domain.com/admin/vacancies
// Name 'admin.' -> Route name jadi: admin.vacancies.index, admin.dashboard, dst.
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // --- DASHBOARD ADMIN ---
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // --- MANAJEMEN LOWONGAN (CRUD) ---
    Route::resource('vacancies', VacancyMagangController::class);

    // Route Spesial: Manual Close/Open Lowongan
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

    // --- PENILAIAN / ASSESSMENT ---
    // Menu daftar mahasiswa yang siap dinilai
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');

    // Form Input Nilai
    Route::get('/assessments/{member_id}/create', [AssessmentController::class, 'create'])->name('assessments.create');

    // Proses Simpan Nilai
    Route::post('/assessments/{member_id}/store', [AssessmentController::class, 'store'])->name('assessments.store');

    // --- PENGATURAN PROFIL ADMIN ---
    // Penjelasan: name('profile') akan digabung dengan prefix 'admin.' 
    // Hasil akhirnya = 'admin.profile' (Sesuai yang dipanggil di Sidebar/View)
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
});
