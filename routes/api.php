<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCrudController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserCrudController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
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

//auth user
Route::post('/login', [UserAuthController::class, 'login']); //Login user
// Route::post('/register', [UserAuthController::class, 'register']); //Simpan user baru
//auth admin
Route::post('/login/admin', [AdminAuthController::class, 'loginAdmin']); //Login admin
// Route::post('/register/admin', [AdminAuthController::class, 'registerAdmin']); //Simpan admin baru

//user
Route::post('/user', [UserCrudController::class, 'addUser']); //Baca semuan user
Route::get('/user', [UserCrudController::class, 'readAllUser']); //Baca semuan user
Route::get('/user/{id?}', [UserCrudController::class, 'showUserById']); //Baca satu user berdasarkan ID
Route::put('/user/{id?}', [UserCrudController::class, 'updateUser']); //update user berdasarkan ID
Route::delete('user/{id?}', [UserCrudController::class, 'deleteUser']); //Hapus user berdasarkan ID
//admin
Route::get('/admin', [AdminCrudController::class, 'readAllAdmin']); //Baca semua admin
Route::get('/admin/{id?}', [AdminCrudController::class, 'showAdminById']); //Baca satu admin berdasarkan ID
Route::put('/admin/{id?}', [AdminCrudController::class, 'updateAdmin']); //update admin berdasarkan ID
Route::delete('admin/{id?}', [AdminCrudController::class, 'deleteAdmin']); //Hapus admin berdasarkan ID

Route::prefix('absences')->group(function () {
    // Routes Untuk filter dan export
    Route::get('/filter', [AbsenceController::class, 'filter']);
    Route::get('/export', [AbsenceController::class, 'export']);
});