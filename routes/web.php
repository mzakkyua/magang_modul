<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==========================================================
// IMPORT CONTROLLER
// ==========================================================

// ---------------------
// UMUM & PESERTA MAGANG
// ---------------------
use App\Http\Controllers\AuthMagangController;
use App\Http\Controllers\DashboardMagangController;
use App\Http\Controllers\ProfileMagangController;
use App\Http\Controllers\ApplicationMagangController;
use App\Http\Controllers\LandingController;

// ---------------------
// ADMIN DINAS
// ---------------------
use App\Http\Controllers\Admin\VacancyMagangController;
use App\Http\Controllers\Admin\ApplicationVerificationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\AdminProfileController;

// Auth Reset Password
use App\Http\Controllers\Auth\ForgotPasswordMagangController;
use App\Http\Controllers\Auth\ResetPasswordMagangController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
| Seluruh routing aplikasi magang
| - Memisahkan zona publik, peserta, dan admin
| - Mendukung multi-guard (web & magang)
|--------------------------------------------------------------------------
*/


Route::prefix('password')->group(function () {

    Route::get('/forgot', [ForgotPasswordMagangController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/email', [ForgotPasswordMagangController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset/{token}', [ResetPasswordMagangController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset', [ResetPasswordMagangController::class, 'reset'])
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| LOGOUT UNIVERSAL (MULTI GUARD SAFE)
|--------------------------------------------------------------------------
| - Bisa logout dari admin (guard: web)
| - Bisa logout dari peserta magang (guard: magang)
| - Session dibersihkan total (aman dari session leak)
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {

    // Logout peserta magang
    if (Auth::guard('magang')->check()) {
        Auth::guard('magang')->logout();
    }

    if (Auth::guard('web')->check()) {
        Auth::guard('web')->logout();
    }

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');


/*
|--------------------------------------------------------------------------
| 1. ZONA PUBLIK
|--------------------------------------------------------------------------
| - Bisa diakses tanpa login
| - Landing page & detail lowongan
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])
    ->name('landing.index');

Route::get('/lowongan/{vacancy}', [LandingController::class, 'show'])
    ->name('landing.show');


/*
|--------------------------------------------------------------------------
| 2. ZONA GUEST PESERTA MAGANG
|--------------------------------------------------------------------------
| - Hanya untuk peserta yang BELUM login
| - Guard: magang
|--------------------------------------------------------------------------
*/
Route::middleware('guest:magang')->group(function () {

    Route::get('/login', [AuthMagangController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthMagangController::class, 'login'])
        ->name('login.post');

    Route::get('/register', [AuthMagangController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthMagangController::class, 'register'])
        ->name('register.post');
});


/*
|--------------------------------------------------------------------------
| 3. ZONA AUTH PESERTA MAGANG
|--------------------------------------------------------------------------
| - Peserta magang yang sudah login
| - Guard: magang
|--------------------------------------------------------------------------
*/
Route::middleware('auth:magang')->group(function () {


    Route::get('/dashboard', [DashboardMagangController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileMagangController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileMagangController::class, 'update'])
        ->name('profile.update');

    Route::post('/applications', [ApplicationMagangController::class, 'store'])
        ->name('applications.store');
});


/*
|--------------------------------------------------------------------------
| 4. ZONA ADMIN DINAS
|--------------------------------------------------------------------------
| - Guard default: web
| - Middleware:
|   - auth          → harus login
|   - admin.magang  → harus admin magang (superadmin / admin bidang)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:web', 'adminOnly'])
    ->group(function () {

        // ---------------------
        // DASHBOARD ADMIN
        // ---------------------
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // ---------------------
        // CRUD LOWONGAN
        // ---------------------
        Route::resource('vacancies', VacancyMagangController::class);

        Route::patch(
            '/vacancies/{vacancy}/toggle',
            [VacancyMagangController::class, 'toggleStatus']
        )->name('vacancies.toggle');

        // ---------------------
        // VERIFIKASI LAMARAN
        // ---------------------
        Route::get(
            '/applications',
            [ApplicationVerificationController::class, 'index']
        )->name('applications.index');

        Route::get(
            '/applications/{application}',
            [ApplicationVerificationController::class, 'show']
        )->name('applications.show');

        Route::patch(
            '/applications/{application}/update-status',
            [ApplicationVerificationController::class, 'updateStatus']
        )->name('applications.update-status');

        // ---------------------
        // PENILAIAN PESERTA
        // ---------------------
        Route::get(
            '/assessments',
            [AssessmentController::class, 'index']
        )->name('assessments.index');

        Route::get(
            '/assessments/{member}/create',
            [AssessmentController::class, 'create']
        )->name('assessments.create');

        Route::post(
            '/assessments/{member}/store',
            [AssessmentController::class, 'store']
        )->name('assessments.store');

        // ---------------------
        // PROFIL ADMIN
        // ---------------------
        Route::get(
            '/profile',
            [AdminProfileController::class, 'index']
        )->name('profile');

        Route::put(
            '/profile',
            [AdminProfileController::class, 'update']
        )->name('profile.update');
    });
