<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminCrudController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserCrudController;
use App\Http\Controllers\StatistikController;
use Illuminate\Support\Facades\Route;

// Public auth
Route::post('/login', [UserAuthController::class, 'login']);         // user login (email+password)
Route::post('/login/admin', [AdminAuthController::class, 'loginAdmin']); // admin login (username+password)

// User-protected endpoints
Route::middleware(['auth:sanctum', 'auth.user'])->group(function () {
    // User profile
    Route::get('/user/current-activity', [UserCrudController::class, 'getMyCurrentActivity']);
    Route::get('/user/statistik', [UserCrudController::class, 'getMyStatistik']);
});

// Admin-protected endpoints
Route::middleware(['auth:sanctum', 'auth.admin'])->group(function () {
    // User management
    Route::post('/user', [UserCrudController::class, 'addUser']);
    Route::get('/user', [UserCrudController::class, 'readAllUser']);
    Route::get('/user/{id?}', [UserCrudController::class, 'showUserById']);
    Route::get('/user/{id?}/absences', [UserCrudController::class, 'showDetailWithAttendance']);
    Route::put('/user/{id?}', [UserCrudController::class, 'updateUser']);
    Route::delete('/user/{id?}', [UserCrudController::class, 'deleteUser']);
    Route::post('/user/import', [UserCrudController::class, 'import']);

    // Settings and statistics (admin-managed)
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'store']);
    Route::get('/statistik-tahunan', [StatistikController::class, 'statistikTahunan']);

    Route::put('/absences/{user:id}/approve', [AbsenceController::class, 'approveAbsence']);
});
