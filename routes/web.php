<?php

use App\Models\Farmaco;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\FarmacoController;
use App\Http\Controllers\SalidaController;
use App\Models\Salida;

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

// Ruta dinámica para áreas (DEBE estar ANTES del resource)
Route::get('areas/{areaType}', [AreaController::class, 'showArea'])->name('areas.show');

Route::resource('areas', AreaController::class);
Route::resource('salidas', SalidaController::class);



