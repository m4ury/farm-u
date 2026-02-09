<?php

use App\Models\Farmaco;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\FarmacoController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PedidoController;
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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

Route::resource('farmacos', FarmacoController::class)->middleware('auth');

// Ruta dinámica para áreas (DEBE estar ANTES del resource)
Route::get('areas/{areaType}', [AreaController::class, 'showArea'])->name('areas.show')->middleware('auth');

Route::resource('areas', AreaController::class)->middleware('auth');
Route::resource('salidas', SalidaController::class)->middleware('auth');

Route::resource('pedidos', PedidoController::class)->middleware('auth');
Route::post('pedidos/selection/store', [PedidoController::class, 'storeFromSelection'])->name('pedidos.storeFromSelection')->middleware('auth');

Route::resource('users', UserController::class)->middleware('auth');
Route::post('users/{id}/restore', [App\Http\Controllers\UserController::class, 'restore'])->name('users.restore');

