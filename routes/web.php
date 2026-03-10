<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\StudentController;
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
    Route::get('/scholarships', [AdminController::class, 'scholarships'])->name('admin.scholarships');

    Route::get('/admin/scholarships', [ScholarshipController::class, 'index'])
    ->name('admin.scholarships');
    Route::post('/admin/scholarships/store', [ScholarshipController::class, 'store'])
    ->name('admin.scholarships.store');
    Route::put('/admin/scholarships/{id}', [ScholarshipController::class, 'update'])
    ->name('admin.scholarships.update');
    Route::delete('/admin/scholarships/{id}', [ScholarshipController::class, 'destroy'])
    ->name('admin.scholarships.destroy');

    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');

    Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/student/logout', [StudentController::class, 'logout'])->name('student.logout');

    Route::get('/studentdash', [StudentController::class, 'dashboard'])->name('student.dashboard');
});