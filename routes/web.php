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

Route::get('/', function () {
    try {
        DB::connection()->getPdo();
        echo "Connected successfully to database " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        echo "Could not connect to the database. Please check your configuration. Error: " . $e->getMessage();
    }
    return view('welcome');
});
