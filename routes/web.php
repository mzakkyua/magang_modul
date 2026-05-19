<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| IMPORT CONTROLLER
|--------------------------------------------------------------------------
*/

// ---------------------
// UMUM & PESERTA MAGANG
// ---------------------
use App\Http\Controllers\AuthMagangController;
use App\Http\Controllers\DashboardMagangController;
use App\Http\Controllers\ProfileMagangController;
use App\Http\Controllers\ApplicationMagangController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\StatusMagangController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CertificateController;

// ---------------------
// ADMIN DINAS
// ---------------------
use App\Http\Controllers\Admin\VacancyMagangController;
use App\Http\Controllers\Admin\ApplicationVerificationController;
use App\Http\Controllers\Admin\DivisionSettingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\PesertaController;
use App\Http\Controllers\Admin\DivisionController;

// ---------------------
// AUTH RESET PASSWORD
// ---------------------
use App\Http\Controllers\Requests\Auth\ForgotPasswordMagangController;
use App\Http\Controllers\Requests\Auth\ResetPasswordMagangController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
|
| Seluruh routing aplikasi magang.
|
| ZONA:
|   1. Reset Password  — publik, tidak butuh login
|   2. Logout          — universal multi-guard
|   3. Publik          — landing page & kalender
|   4. Guest Peserta   — login & register (hanya jika belum login)
|   5. Auth Peserta    — area peserta yang sudah login (guard: magang)
|   6. Admin           — area admin dinas (guard: web + admin.magang)
|
| IMPROVEMENT DARI VERSI SEBELUMNYA:
|   - [FIX] Logout closure dipindah ke AuthMagangController::logout()
|           agar testable, loggable, dan route:cache friendly
|   - [FIX] Certificate routes pakai route model binding {certificate}
|           agar otomatis 404 jika ID tidak ada + lebih clean
|   - [FIX] Rate limiting ditambahkan pada POST login & register
|           sebagai defense in depth di level route
|   - [FIX] Naming route certificate distandarkan ke plural (certificates.*)
|           agar konsisten dengan assessments.* dan vacancies.*
|   - [FIX] Import Auth facade dihapus karena logout sudah di controller
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| RESET PASSWORD
|--------------------------------------------------------------------------
| Tidak butuh login — akses dengan token dari email.
|--------------------------------------------------------------------------
*/

Route::prefix('password')->group(function () {

    Route::get('/forgot', [ForgotPasswordMagangController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/email', [ForgotPasswordMagangController::class, 'sendResetLinkEmail'])
        ->name('password.email')
        ->middleware('throttle:5,1'); // max 5 request reset per menit

    Route::get('/reset/{token}', [ResetPasswordMagangController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset', [ResetPasswordMagangController::class, 'reset'])
        ->name('password.update');
});


/*
|--------------------------------------------------------------------------
| LOGOUT UNIVERSAL
|--------------------------------------------------------------------------
| IMPROVEMENT: Dipindah ke controller agar:
|   - Testable (bisa unit test method logout)
|   - Audit log bisa ditambahkan di controller
|   - php artisan route:cache tidak protes closure
|   - Konsisten dengan controller lain
|
| Method AuthMagangController::logout() menangani:
|   - guard web (admin)
|   - guard magang (peserta)
|   - session invalidate + regenerate token
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthMagangController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| 1. ZONA PUBLIK
|--------------------------------------------------------------------------
| Bisa diakses tanpa login.
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])
    ->name('landing.index');

Route::get('/lowongan/{vacancy}', [LandingController::class, 'show'])
    ->name('landing.show');

// Kalender publik
Route::get('/calendar', [CalendarController::class, 'index'])
    ->name('calendar');

Route::get('/calendar/events', [CalendarController::class, 'fetch'])
    ->name('calendar.events')
    ->middleware('throttle:60,1');

/*
|--------------------------------------------------------------------------
| 2. ZONA GUEST PESERTA MAGANG
|--------------------------------------------------------------------------
| Hanya untuk peserta yang BELUM login (guard: magang).
| Jika sudah login → otomatis redirect oleh middleware guest:magang.
|--------------------------------------------------------------------------
*/
Route::middleware('guest:magang')->group(function () {

    Route::get('/login', [AuthMagangController::class, 'showLoginForm'])
        ->name('login');

    // IMPROVEMENT: throttle sebagai defense in depth di level route
    // RateLimiter di controller sudah ada, ini lapisan kedua
    Route::post('/login', [AuthMagangController::class, 'login'])
        ->name('login.post')
        ->middleware('throttle:10,1');

    Route::get('/register', [AuthMagangController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthMagangController::class, 'register'])
        ->name('register.post')
        ->middleware('throttle:5,1');
});


/*
|--------------------------------------------------------------------------
| 3. ZONA AUTH PESERTA MAGANG
|--------------------------------------------------------------------------
| Peserta yang sudah login (guard: magang).
|--------------------------------------------------------------------------
*/
Route::middleware('auth:magang')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardMagangController::class, 'index'])
        ->name('dashboard.index');

    Route::get('/dashboard/lowongan/{id}', [DashboardMagangController::class, 'show'])
        ->name('dashboard.show');

    // Profil
    Route::get('/profile', [ProfileMagangController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileMagangController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile/delete-cv', [ProfileMagangController::class, 'deleteCv'])
        ->name('profile.delete.cv');

    // Pendaftaran magang
    Route::post('/applications', [ApplicationMagangController::class, 'store'])
        ->name('applications.store')
        ->middleware('throttle:10,1'); // mencegah spam submit lamaran

    // Status lamaran
    Route::get('/status', [StatusMagangController::class, 'index'])
        ->name('status');

    // Nilai
    Route::get('/nilai', [DashboardMagangController::class, 'nilai'])
        ->name('nilai');

    /*
    | Sertifikat Peserta
    |
    | IMPROVEMENT: Pakai route model binding {certificate} bukan {id} mentah.
    | Keuntungan:
    |   - Laravel otomatis throw 404 jika certificate tidak ditemukan
    |   - Tidak perlu findOrFail() manual di controller
    |   - URL lebih semantik
    |   - Konsisten dengan pattern Laravel
    |
    | CATATAN: Pastikan controller menggunakan type-hint Certificate $certificate
    | bukan $id agar binding bekerja. Ownership check tetap ada di controller.
    */
    Route::get('/sertifikat', [CertificateController::class, 'index'])
        ->name('certificates.index');

    Route::get('/sertifikat/{certificate}/view', [CertificateController::class, 'view'])
        ->name('certificates.view');

    Route::get('/sertifikat/{certificate}/download', [CertificateController::class, 'download'])
        ->name('certificates.download');

    Route::get('/vacancies/{vacancy}/snapshot', [LandingController::class, 'snapshot'])
        ->middleware(['throttle:60,1'])
        ->name('vacancies.snapshot');
});


/*
|--------------------------------------------------------------------------
| 4. ZONA ADMIN DINAS
|--------------------------------------------------------------------------
| Guard: web + middleware admin.magang.
| Superadmin & admin divisi dibedakan di dalam controller.
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:web', 'admin.magang'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Kalender admin
        Route::get('/calendar', [CalendarController::class, 'indexAdmin'])
            ->name('calendar.index');
        Route::post('/calendar', [CalendarController::class, 'store'])
            ->name('calendar.store');
        Route::put('/calendar/{event}', [CalendarController::class, 'update'])
            ->name('calendar.update');
        Route::delete('/calendar/{event}', [CalendarController::class, 'destroy'])
            ->name('calendar.destroy');

        // CRUD Lowongan
        Route::resource('vacancies', VacancyMagangController::class);
        Route::patch('/vacancies/{vacancy}/toggle', [VacancyMagangController::class, 'toggleStatus'])
            ->name('vacancies.toggle');
        Route::patch('/vacancies/{vacancy}/archive', [VacancyMagangController::class, 'archive'])
            ->name('vacancies.archive');

        // Verifikasi Lamaran
        Route::get('/applications', [ApplicationVerificationController::class, 'index'])
            ->name('applications.index');
        Route::get('/applications/{application}', [ApplicationVerificationController::class, 'show'])
            ->name('applications.show');
        Route::patch('/applications/{application}/update-status', [ApplicationVerificationController::class, 'updateStatus'])
            ->name('applications.update-status');

        // Penilaian Peserta
        Route::get('/assessments', [AssessmentController::class, 'index'])
            ->name('assessments.index');
        Route::get('/assessments/{member}/create', [AssessmentController::class, 'create'])
            ->name('assessments.create');
        Route::post('/assessments/{member}/store', [AssessmentController::class, 'store'])
            ->name('assessments.store');

        // Profil Admin
        Route::get('/profile', [AdminProfileController::class, 'index'])
            ->name('profile');
        Route::put('/profile', [AdminProfileController::class, 'update'])
            ->name('profile.update');

        /*
        | Sertifikat Admin
        |
        | IMPROVEMENT: Naming distandarkan ke certificates.* (plural)
        | agar konsisten dengan assessments.* dan vacancies.*
        */
        Route::get('/certificate/create', [CertificateController::class, 'create'])
            ->name('certificates.create');
        Route::post('/certificate', [CertificateController::class, 'store'])
            ->name('certificates.store');

        // Manajemen Pegawai & Hak Akses
        Route::get('/pegawai', [PegawaiController::class, 'index'])
            ->name('pegawai.index');
        Route::post('/pegawai/{id}/access', [PegawaiController::class, 'storeAccess'])
            ->name('pegawai.access.store');
        Route::delete('/pegawai/{id}/access', [PegawaiController::class, 'destroyAccess'])
            ->name('pegawai.access.destroy');

        // Kelola Kuota Divisi
        Route::get('/division-settings', [DivisionSettingController::class, 'index'])
            ->name('division-settings.index');

        Route::patch('/division-settings/{divisionSetting}', [DivisionSettingController::class, 'update'])
            ->name('division-settings.update');

        // Di dalam group middleware admin
        Route::get('/peserta', [PesertaController::class, 'index'])
            ->name('peserta.index');

        /*
|--------------------------------------------------------------------------
| Master Divisi Magang
|--------------------------------------------------------------------------
|
| Source of truth seluruh division_name.
| Hanya superadmin yang dapat mengelola.
|
*/
        Route::prefix('divisions')
            ->name('divisions.')
            ->group(function () {

                Route::get('/', [DivisionController::class, 'index'])
                    ->name('index');

                Route::post('/', [DivisionController::class, 'store'])
                    ->name('store');

                Route::put('/{division}', [DivisionController::class, 'update'])
                    ->name('update');

                Route::patch('/{division}/toggle-active', [DivisionController::class, 'toggleActive'])
                    ->name('toggle-active');

                Route::delete('/{division}', [DivisionController::class, 'destroy'])
                    ->name('destroy');
            });
    });
