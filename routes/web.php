<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/parse', [\App\Http\Controllers\ParserController::class, 'index']);
Route::get('/auth', [\App\Http\Controllers\ParserController::class, 'auth']);
Route::get('/seats/{hallId}', [\App\Http\Controllers\ParserController::class, 'seats']);
Route::get('/countedSeats/{hallId}', [\App\Http\Controllers\ParserController::class, 'countedSeats']);
Route::get('/checkBuy/{sessionId}', [\App\Http\Controllers\ParserController::class, 'checkBuy']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
