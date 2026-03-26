<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationsController;
use App\Http\Controllers\ApplyScholarshipsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
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

Route::post('/weblogin', [AuthController::class, 'webLogin'])->name('login.submit');
Route::post('/webregister', [AuthController::class, 'webRegister'])->name('register.submit');

Route::get('/studentdash', [StudentController::class, 'dashboard'])
     ->name('student.dashboard');

Route::middleware(['auth'])->group(function() {
    Route::get('/admindash', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/admin/scholarships', [ScholarshipController::class, 'AdminwebIndex'])
    ->name('admin.scholarships');
    Route::post('/admin/scholarships/store', [ScholarshipController::class, 'AdminwebStore'])
    ->name('admin.scholarships.store');
    Route::put('/admin/scholarships/{id}', [ScholarshipController::class, 'webUpdate'])
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

    Route::get('/student/logout', [StudentController::class, 'logout'])->name('student.logout');
    Route::get('/studentdash', [StudentController::class, 'dashboard'])->name('student.dashboard');

    Route::get('/student/applications', [ApplicationsController::class, 'index'])
        ->name('student.applications');

    Route::prefix('student')->name('student.')->group(function () {
        Route::get('scholarships', [StudentScholarshipController::class, 'index'])
            ->name('scholarships');
    
        Route::get('applications/create/{scholarship}', [ApplyScholarshipsController::class, 'create'])
        ->name('scholarships.view');
        Route::post('applications/apply/{scholarship}', [ApplyScholarshipsController::class, 'ScholarshipApply'])
            ->name('scholarships.apply');
        
    });
    
    Route::get('/student/profile', [StudentController::class, 'StudentProfile'])
        ->name('student.profile');
});