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
Route::get('/checkSale/{sessionId}', [\App\Http\Controllers\ParserController::class, 'checkSale']);
Route::get('/checkByTarif/{sessionId}', [\App\Http\Controllers\ParserController::class, 'checkByTarif']);
Route::get('/checkTarif/{sessionId}', [\App\Http\Controllers\ParserController::class, 'checkTarif']);
Route::get('/checkDuplicate/{sessionId}', [\App\Http\Controllers\ParserController::class, 'checkDuplicate']);
Route::get('/scheme', [\App\Http\Controllers\ParserController::class, 'scheme']);
Route::get('/scheme/{count}', [\App\Http\Controllers\ParserController::class, 'scheme']);
Route::get('testGuzzle', [\App\Http\Controllers\ParserController::class, 'testGuzzle']);

Route::post('/auth1', [\App\Http\Controllers\ParserUIController::class, 'auth1'])->name('auth1');
//Route::get('/auth1', [\App\Http\Controllers\ParserUIController::class, 'auth1'])->name('auth123');
Route::post('/checkBuy', [\App\Http\Controllers\ParserUIController::class, 'checkSale'])->name('checkSale');
Route::get('/report/{id}', [\App\Http\Controllers\ReportController::class, 'index'])->name('report.view');
Route::get('/report', [\App\Http\Controllers\ReportController::class, 'index'])->name('report.index');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
