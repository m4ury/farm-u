<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Despacho;
use App\Models\Farmaco;
use App\Models\Lote;
use App\Models\Pedido;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obtener todas las áreas con sus fármacos y lotes
        $areas = Area::with(['farmacos.lotes'])->get();

        // Mapeo de nombre_area a slug para URLs
        $areaSlugMapping = (new \App\Http\Controllers\AreaController)->getAreaSlugMapping();

        // === CONTADORES GENERALES ===
        $totalFarmacos = Farmaco::count();
        $totalLotesActivos = Lote::where('vencido', false)->where('cantidad_disponible', '>', 0)->count();

        // === PEDIDOS ===
        $pedidosPendientes = Pedido::where('estado', Pedido::ESTADO_PENDIENTE)->with(['area', 'user'])->latest('fecha_pedido')->get();
        $pedidosAprobados = Pedido::where('estado', Pedido::ESTADO_APROBADO)->count();
        $pedidosHoy = Pedido::whereDate('fecha_pedido', today())->count();
        $pedidosMes = Pedido::whereMonth('fecha_pedido', now()->month)->whereYear('fecha_pedido', now()->year)->count();

        // === DESPACHOS ===
        $despachosMes = Despacho::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $despachosPendientesRecepcion = Despacho::doesntHave('recepcion')->with(['pedido.area', 'lote.farmaco'])->latest()->limit(10)->get();
        $despachosRecientes = Despacho::with(['pedido.area', 'lote.farmaco', 'usuarioAprobador'])->latest()->limit(5)->get();

        // === LOTES ===
        // Lotes próximos a vencer (menos de 30 días)
        $proximosVencer = Lote::where('vencido', false)
            ->where('cantidad_disponible', '>', 0)
            ->where('fecha_vencimiento', '>', now())
            ->where('fecha_vencimiento', '<=', now()->addDays(30))
            ->with('farmaco')
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        // Lotes vencidos con stock
        $lotesVencidos = Lote::where('vencido', true)
            ->where('cantidad_disponible', '>', 0)
            ->with('farmaco')
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        // === FÁRMACOS ===
        // Bajo stock: fármacos cuyo stock calculado es < 20% del stock_maximo
        $bajoStock = Farmaco::with(['lotes', 'areas'])->get()->filter(function ($farmaco) {
            $stock = $farmaco->getStockFisicoCalculado();
            return $farmaco->stock_maximo > 0 && $stock <= ($farmaco->stock_maximo * 0.2);
        })->sortBy(function ($farmaco) {
            return $farmaco->getStockFisicoCalculado();
        });

        // Medicamentos controlados
        $controlados = Farmaco::where('controlado', true)->with(['areas', 'lotes'])->get();

        // Medicamentos con mayor salida - suma total de cantidad_salida
        $mayorSalida = Farmaco::withSum('salidas', 'cantidad_salida')
            ->orderBy('salidas_sum_cantidad_salida', 'desc')
            ->with('areas')
            ->limit(5)
            ->get();

        // Top 5 mayor stock disponible (calculado desde lotes)
        $mayorStock = Farmaco::with(['lotes', 'areas'])->get()->sortByDesc(function ($farmaco) {
            return $farmaco->getStockFisicoCalculado();
        })->take(5);

        // Stock total general (suma de todos los lotes disponibles)
        $stockTotal = Lote::where('vencido', false)->sum('cantidad_disponible');

        return view('home', compact(
            'areas', 'areaSlugMapping',
            'totalFarmacos', 'totalLotesActivos',
            'pedidosPendientes', 'pedidosAprobados', 'pedidosHoy', 'pedidosMes',
            'despachosMes', 'despachosPendientesRecepcion', 'despachosRecientes',
            'proximosVencer', 'lotesVencidos',
            'bajoStock', 'controlados', 'mayorSalida', 'mayorStock', 'stockTotal'
        ));
    }
}
