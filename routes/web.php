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
    return view('index');
})->name('index');

Route::get('/parse', [\App\Http\Controllers\ParserController::class, 'parser']);
Route::get('/auth', [\App\Http\Controllers\ParserController::class, 'auth'])->name('auth');
Route::get('/seats/{hallId}', [\App\Http\Controllers\ParserController::class, 'seats']);
Route::get('/countedSeats/{hallId}', [\App\Http\Controllers\ParserController::class, 'countedSeats']);
//Route::get('/checkSale/{sessionId}', [\App\Http\Controllers\ParserController::class, 'checkSale']);
Route::get('/checkTarif/{sessionId}', [\App\Http\Controllers\ParserController::class, 'checkTarif']);
Route::get('/checkDuplicate/{sessionId}', [\App\Http\Controllers\ParserController::class, 'checkDuplicate']);
Route::get('testGuzzle', [\App\Http\Controllers\ParserController::class, 'testGuzzle']);

Route::post('/auth1', [\App\Http\Controllers\ParserUIController::class, 'auth1'])->name('auth1');
//Route::get('/auth1', [\App\Http\Controllers\ParserUIController::class, 'auth1'])->name('auth123');
Route::post('/checkSale', [\App\Http\Controllers\ParserUIController::class, 'checkSale'])->name('checkSale');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
