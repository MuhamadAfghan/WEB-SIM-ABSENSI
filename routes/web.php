<?php

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

use Illuminate\Support\Facades\DB;
Route::post('/users/upload', [App\Http\Controllers\UserCrudController::class, 'uploadUserExcel'])
    ->name('users.upload');

Route::get('/', function () {
    try {
        DB::connection()->getPdo();
        echo "Connected successfully to database " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        echo "Could not connect to the database. Please check your configuration. Error: " . $e->getMessage();
    }
    return view('welcome');
});


// Authentication Routes
Route::get('/login', function () {
    return view('login');
})->name('login');

// Dashboard Routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');



// Employee Management Routes
Route::get('/employees', function () {
    return view('data-karyawan');
})->name('employees');

Route::get('/employee-details', function () {
    return view('detail-data-karyawan');
})->name('employee.details');

// Attendance Routes
Route::get('/attendance', function () {
    return view('data-absensi');
})->name('attendance');

// Account Management Routes
Route::get('/account-management', function () {
    return view('account-management');
})->name('account.management');
