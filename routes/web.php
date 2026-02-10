<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// =====================
// IMPORT CONTROLLER
// =====================

// Umum & Peserta Magang
use App\Http\Controllers\AuthMagangController;
use App\Http\Controllers\DashboardMagangController;
use App\Http\Controllers\ProfileMagangController;
use App\Http\Controllers\ApplicationMagangController;
use App\Http\Controllers\LandingController;

// Admin
use App\Http\Controllers\Admin\VacancyMagangController;
use App\Http\Controllers\Admin\ApplicationVerificationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\AdminProfileController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| LOGOUT (UNIVERSAL - MULTI GUARD SAFE)
|--------------------------------------------------------------------------
| Bisa logout dari admin (web) atau peserta (magang)
| TANPA MERUSAK AUTH YANG SUDAH ADA
*/


Route::post('/logout', function () {

    if (Auth::guard('magang')->check()) {
        Auth::guard('magang')->logout();
    }

    if (Auth::check()) {
        Auth::logout();
    }

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');


/*
|--------------------------------------------------------------------------
| 1. ZONA PUBLIK
|--------------------------------------------------------------------------
| Bisa diakses siapa saja
*/
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/lowongan/{vacancy}', [LandingController::class, 'show'])->name('landing.show');


/*
|--------------------------------------------------------------------------
| 2. ZONA GUEST (BELUM LOGIN - PESERTA MAGANG)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:magang')->group(function () {

    Route::get('/login', [AuthMagangController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthMagangController::class, 'login']);

    Route::get('/register', [AuthMagangController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthMagangController::class, 'register']);
});


/*
|--------------------------------------------------------------------------
| 3. ZONA AUTH PESERTA MAGANG
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
| Guard default (web)
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth']) // TODO: tambahkan role admin middleware
    ->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // CRUD Lowongan
        Route::resource('vacancies', VacancyMagangController::class);

        // Open / Close Lowongan
        Route::patch(
            '/vacancies/{vacancy}/toggle',
            [VacancyMagangController::class, 'toggleStatus']
        )->name('vacancies.toggle');

        // Verifikasi Lamaran
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

        // Assessment / Penilaian
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

        // Profil Admin
        Route::get(
            '/profile',
            [AdminProfileController::class, 'index']
        )->name('profile');

        Route::put(
            '/profile',
            [AdminProfileController::class, 'update']
        )->name('profile.update');
    });
