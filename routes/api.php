<?php

use App\Http\Controllers\ApplyScholarshipsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'Apilogout']);

    Route::get('/scholarships', [ScholarshipController::class, 'getScholarships']);
    Route::get('/scholarships/{id}', [ScholarshipController::class, 'getScholarship']);
});

Route::middleware(['auth:sanctum', 'role:1'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/user', [UserController::class, 'getUsers']);
    Route::get('/user/{id}', [UserController::class, 'getUser']);
    Route::get('/user/search?email={email}', [UserController::class, 'getUserSearch']); 

    Route::prefix('admin')->group(function () {

        Route::post('/scholarships', [ScholarshipController::class, 'store']);
        Route::put('/scholarships/{id}', [ScholarshipController::class, 'update']);
        Route::delete('/scholarships/{id}', [ScholarshipController::class, 'destroy']);

        Route::post('/users', [UserController::class, 'userStore']);
        Route::put('/users/{id}', [UserController::class, 'userUpdate']);
        Route::delete('/users/{id}', [UserController::class, 'userDestroy']);
        
        Route::get('/logs', [LogController::class, 'index']);
        Route::get('/logs/search?user_id={user_id}', [LogController::class, 'getLogSearch']);
    });
});

Route::middleware('auth:sanctum', 'role:3')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/scholarships', [ScholarshipController::class, 'getScholarships']);
    Route::get('/scholarships/{id}', [ScholarshipController::class, 'getScholarship']);

    Route::prefix('student')->group(function () {
        Route::get('/applications', [ApplyScholarshipsController::class, 'getAllApplications']);
        Route::get('/application/{id}', [ApplyScholarshipsController::class, 'getApplication']);
        Route::post('/application', [ApplyScholarshipsController::class, 'storeApplication']);
        Route::put('/application/{id}', [ApplyScholarshipsController::class, 'updateApplication']);
        Route::delete('/application/{id}', [ApplyScholarshipsController::class, 'destroyApplication']);
    });
});

Route::get('/debug-user', function () {
    return App\Models\User::withoutGlobalScopes()->first();
});

Route::get('/debug-scholarship', function () {
    return App\Models\Scholarship::withoutGlobalScopes()->first();
});
