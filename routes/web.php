<?php

use App\Models\Farmaco;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\FarmacoController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\RecepcionDespachoController;
use App\Http\Controllers\HistoricoMovimientoController;
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

// Rutas adicionales para aprobación y rechazo de pedidos
Route::middleware('auth')->group(function () {
    Route::get('pedidos/{pedido}/aprobar', [PedidoController::class, 'aprobarForm'])->name('pedidos.aprobarForm');
    Route::post('pedidos/{pedido}/aprobar', [PedidoController::class, 'aprobar'])->name('pedidos.aprobar');
    Route::get('pedidos/{pedido}/rechazar', [PedidoController::class, 'rechazarForm'])->name('pedidos.rechazarForm');
    Route::post('pedidos/{pedido}/rechazar', [PedidoController::class, 'rechazar'])->name('pedidos.rechazar');
    Route::get('pedidos/{pedido}/despachar', [PedidoController::class, 'despacharForm'])->name('pedidos.despacharForm');
    Route::post('pedidos/{pedido}/despachar', [PedidoController::class, 'despachar'])->name('pedidos.despachar');
    Route::get('pedidos/{pedido}/modificar', [PedidoController::class, 'modificarForm'])->name('pedidos.modificarForm');
    Route::post('pedidos/{pedido}/modificar', [PedidoController::class, 'modificar'])->name('pedidos.modificar');
});

// Resource para Lotes
Route::resource('lotes', LoteController::class)->middleware('auth');

// Rutas adicionales para Lotes
Route::middleware('auth')->group(function () {
    Route::post('lotes/{lote}/marcar-vencido', [LoteController::class, 'marcarVencido'])->name('lotes.marcarVencido');
    Route::get('api/farmaco/{farmaco_id}/lotes-disponibles', [LoteController::class, 'lotesDisponibles'])->name('api.lotesDisponibles');
});

Route::resource('users', UserController::class)->middleware('auth');
Route::post('users/{id}/restore', [App\Http\Controllers\UserController::class, 'restore'])->name('users.restore');

// Rutas para Recepción de Despachos
Route::middleware('auth')->group(function () {
    Route::get('despachos/{despacho}/confirmar', [RecepcionDespachoController::class, 'confirmarForm'])->name('despachos.confirmarForm');
    Route::post('despachos/{despacho}/confirmar', [RecepcionDespachoController::class, 'confirmar'])->name('despachos.confirmar');
    Route::get('recepciones/historial-area', [RecepcionDespachoController::class, 'historialArea'])->name('recepciones.historialArea');
});

// Rutas para Historial de Movimientos
Route::middleware('auth')->group(function () {
    Route::get('historial/movimientos', [HistoricoMovimientoController::class, 'index'])->name('historial.movimientos');
    Route::get('historial/farmaco/{farmaco}', [HistoricoMovimientoController::class, 'porFarmaco'])->name('historial.farmaco');
    Route::get('historial/area/{area}', [HistoricoMovimientoController::class, 'porArea'])->name('historial.area');
    Route::get('historial/reporte-tipo', [HistoricoMovimientoController::class, 'reportePorTipo'])->name('historial.reportePorTipo');
    Route::get('historial/exportar', [HistoricoMovimientoController::class, 'exportar'])->name('historial.exportar');
});