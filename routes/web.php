<?php

use App\Models\Farmaco;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\FarmacoController;

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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', function() {
    return view('home');
})->name('home')->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('farmacos', FarmacoController::class);


/* Route::get('/areas.botiquin', function() {
    return Farmaco::with('areas')->get();
})->name('areas.botiquin'); */

Route::resource('areas', AreaController::class);
Route::get('areas.botiquin', [App\Http\Controllers\AreaController::class,'botiquinList'])->name('areas.botiquin');
Route::get('areas.carro', [App\Http\Controllers\AreaController::class,'carroList'])->name('areas.carro');
Route::get('areas.maletin', [App\Http\Controllers\AreaController::class,'maletinList'])->name('areas.maletin');



