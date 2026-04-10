<?php

use App\Http\Controllers\ApplicationsController;
use App\Http\Controllers\ApplyScholarshipsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
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
    Route::put('/changepassword', [AuthController::class, 'changePassword']);
    Route::get('/scholarships', [ScholarshipController::class, 'getScholarships']);
    Route::get('/scholarships/{id}', [ScholarshipController::class, 'getScholarship']);
});

Route::middleware(['auth:sanctum', 'role:1'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/user', [UserController::class, 'getUsers']);
    Route::get('/user/{id}', [UserController::class, 'getUser']);
    Route::get('/admin/user/Usersearch', [UserController::class, 'getUserSearch']);

    Route::prefix('admin')->group(function () {
        Route::get('/uploadedScholarships', [ScholarshipController::class, 
            'getAllUploadedScholarships']);

        Route::get('/user/applicants', [ApplicationsController::class, 'getAllApplicant']);
        Route::get('/user/applicant/{id}', [ApplicationsController::class, 'getApplicant']);
        Route::get('/user/applicants/{scholarship_id}', [ApplicationsController::class,
            'getApplicantOnScholarship']);

        Route::post('/scholarships', [ScholarshipController::class, 'store']);
        Route::put('/scholarships/{id}', [ScholarshipController::class, 'update']);
        Route::delete('/scholarships/{id}', [ScholarshipController::class, 'destroy']);

        Route::post('/users', [UserController::class, 'userStore']);
        Route::put('/users/{id}', [UserController::class, 'userUpdate']);
        Route::delete('/users/{id}', [UserController::class, 'userDestroy']);
        
        Route::get('/logs', [LogController::class, 'index']);
        Route::get('/logs/search?user_id={user_id}', [LogController::class, 'getLogSearch']);

        Route::put('/user/deactivate/{id}', [UserController::class, 'deactivateUser']);

        Route::get('/user/searchByCategory', [UserController::class, 'searchByCategory']);
        Route::get('/userInquiry/{user_id}', [UserController::class, 'inquireUser']);    
    });

    Route::put('/admin/approveReject/{application_id}', [ApplicationsController::class,
             'approveReject']);
});

Route::middleware('auth:sanctum', 'role:3')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/ActiveScholarships', [ScholarshipController::class, 'getScholarships']);
    Route::get('/scholarships/{id}', [ScholarshipController::class, 'getScholarship']);

    Route::prefix('student')->group(function () {
        Route::middleware('auth:sanctum')->get('/applications', [ApplyScholarshipsController::class, 
            'getAllApplications']);
        Route::middleware('auth:sanctum')->get('/application/{id}', [ApplyScholarshipsController::class, 
            'getApplication']);
        Route::middleware('auth:sanctum')->post('/storeapplication/{scholarship_id}', [ApplyScholarshipsController::class, 
            'storeApplication']);
        Route::middleware('auth:sanctum')->put('/updateapplication/{application_id}', [ApplyScholarshipsController::class, 
            'updateApplication']);
        Route::middleware('auth:sanctum')->delete('/destroyapplication/{id}', [ApplyScholarshipsController::class, 
            'destroyApplication']);

        Route::post('/profile/store', [ProfileController::class, 'store'])->name('profile.store');
        Route::middleware('auth:sanctum')->match(['post', 'patch'], '/profile/update', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::get('/profile/show', [ProfileController::class, 'getProfile'])->name('profile.show');

        Route::get('/approvedApplications', [ApplicationsController::class, 'getApprovedApplications']);
        Route::get('/rejectedApplications', [ApplicationsController::class, 'getRejectedApplications']);
        Route::get('/pendingApplications', [ApplicationsController::class, 'getPendingApplications']);
    });
});

Route::middleware('auth:sanctum', 'role:2')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/scholarships', [ScholarshipController::class, 'getScholarships']);

    Route::prefix('provider')->group(function () {
        Route::middleware('auth:sanctum')->get('/scholarship/{id}', [ScholarshipController::class, 
            'providerGetScholarship']);
        Route::middleware('auth:sanctum')->get('/scholarships', [ScholarshipController::class, 
            'getAllUploadedScholarships']);
        Route::middleware('auth:sanctum')->get('/application/{id}', [ApplyScholarshipsController::class, 
            'getApplication']);
        Route::middleware('auth:sanctum')->put('/updateapplication/{application_id}', [ApplyScholarshipsController::class, 
            'updateApplication']);

        Route::middleware('auth:sanctum')->post('/scholarships', [ScholarshipController::class, 
            'store']);
        Route::middleware('auth:sanctum')->put('/scholarships/{id}', [ScholarshipController::class, 
            'update']);
        Route::middleware('auth:sanctum')->delete('/scholarships/{id}', [ScholarshipController::class, 
            'destroy']);

        Route::get('/userInquiry/{user_id}', [UserController::class, 'inquireUser']);    
        Route::get('/user/applicants', [ApplicationsController::class, 'getAllApplicant']);
        Route::get('/user/applicant/{id}', [ApplicationsController::class, 'getApplicant']);
        Route::get('/user/applicants/{scholarship_id}', [ApplicationsController::class,
            'getApplicantOnOwnScholarship']);
    });

    Route::middleware('auth:sanctum')->put('/provider/approveReject/{application_id}', [ApplicationsController::class,
             'approveReject']);
});

Route::get('/debug-user', function () {
    return App\Models\User::withoutGlobalScopes()->first();
});

Route::get('/debug-scholarship', function () {
    return App\Models\Scholarship::withoutGlobalScopes()->first();
});

Route::middleware('auth:sanctum')->get('/debug-user', function() {
    return response()->json(['user' => Auth::user()]);
});
