<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', []);
});

Route::get('/booking', [BookingController::class, 'index']);
Route::get('/booking/{from}/{to}', [BookingController::class, 'search']);
Route::get('/booking-confirm/{id}', [BookingController::class, 'confirmPage']);
Route::get('/booking-confirmed/{id}', [BookingController::class, 'confirmedPage']);


require __DIR__.'/auth.php';
