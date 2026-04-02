<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationsController;
use App\Http\Controllers\ApplyScholarshipsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentScholarshipController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('loginRegister');
});

Route::get('/auth', function () {
    return response()
        ->view('loginRegister')
        ->header('Cache-Control','no-cache, no-store, must-revalidate')
        ->header('Pragma','no-cache')
        ->header('Expires','0');
})->name('auth.form');

Route::get('/logout', [AuthController::class, 'webLogout'])->name('logout');
Route::post('/weblogin', [AuthController::class, 'webLogin'])->name('login.submit');
Route::post('/webregister', [AuthController::class, 'webRegister'])->name('register.submit');

Route::get('/studentdash', [StudentController::class, 'dashboard'])
     ->name('student.dashboard');

Route::middleware(['auth'])->group(function() {
    Route::get('/admindash', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/scholarships/{id}', [ScholarshipController::class, 'show'])->name('scholarships.show');

    Route::get('/admin/scholarships', [ScholarshipController::class, 'AdminwebIndex'])
    ->name('admin.scholarships');
    Route::post('/admin/scholarships/store', [ScholarshipController::class, 'AdminwebStore'])
    ->name('admin.scholarships.store');
    Route::put('/admin/scholarships/{scholarship}', [ScholarshipController::class, 'webUpdate'])
    ->name('admin.scholarships.update');    
    Route::delete('/admin/scholarships/{id}', [ScholarshipController::class, 'webDestroy'])
    ->name('admin.scholarships.destroy');

    Route::get('/admin/users/', [UserController::class, 'AdminwebUsers'])
    ->name('admin.users');
    Route::post('/admin/users/store', [UserController::class, 'AdminwebUserStore'])
    ->name('admin.users.store');
    Route::put('/admin/users/{id}', [UserController::class, 'AdminwebUserUpdate'])
    ->name('admin.users.update');    
    Route::delete('/admin/users/{id}', [UserController::class, 'AdminwebUserDestroy'])
    ->name('admin.users.destroy');

    Route::get('/reports', [LogController::class, 'WebLogs'])->name('admin.reports');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');

    Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/logout', [StudentController::class, 'logout'])->name('logout');
        Route::get('/studentdash', [StudentController::class, 'dashboard'])->name('dashboard');

        Route::get('/student/applications', [ApplicationsController::class, 'index'])
            ->name('applications');

        Route::get('scholarships', [StudentScholarshipController::class, 'index'])
            ->name('scholarships');

        Route::get('applications', [ApplyScholarshipsController::class, 'index'])
            ->name('application.view');
    
        Route::get('applications/create/{scholarship}', [ApplyScholarshipsController::class, 'Webcreate'])
            ->name('scholarships.view');
        Route::get('applications/{id}', [ApplicationsController::class, 'show'])
            ->name('application.show');

        Route::post('applications/apply/{scholarship}', [ApplyScholarshipsController::class, 'ScholarshipApply'])
            ->name('scholarships.apply');
        Route::delete('application/{id}', [ApplyScholarshipsController::class, 'ApplicationDestroy'])
            ->name('application.delete');
        Route::put('/application/{id}', [ApplyScholarshipsController::class, 'ApplicationUpdate'])
            ->name('application.update');
    });

    Route::get('/student/profile', [ProfileController::class, 'StudentProfile'])
        ->name('student.profile');
    Route::get('/student/profile', [ProfileController::class, 'index'])
        ->name('student.profile');

    Route::post('/student/profile', [ProfileController::class, 'store'])
        ->name('student.profile.store');
    Route::post('/student/profile/update', [ProfileController::class, 'store'])
        ->name('student.profile.update');

});

Route::middleware(['auth'])->group(function () {
    Route::prefix('provider')->name('provider.')->group(function () {
        Route::get('/logout', [ProfileController::class, 'logout'])->name('logout');
        // Route::get('profile', [ProfileController::class, 'index'])
        //     ->name('profile');

        Route::get('/dashboard', [ProviderController::class, 'index'])->name('dashboard');
        
        Route::get('/scholarships', [ProviderController::class, 'scholarships'])->name('scholarships');
        Route::get('/applications', [ProviderController::class, 'applications'])->name('applications');
        Route::get('/reports', [ProviderController::class, 'reports'])->name('reports');
        Route::get('/settings', [ProviderController::class, 'settings'])->name('settings');

        Route::post('/profile', [ProfileController::class, 'store'])
            ->name('admin.profile.store');
        Route::post('/admin/profile/update', [ProfileController::class, 'store'])
            ->name('admin.profile.update');
    });
});