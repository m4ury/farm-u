<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Farmaco;
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
        // Obtener todas las áreas con sus fármacos
        $areas = Area::with('farmacos')->get();

        // Mapeo de nombre_area a slug para URLs
        $areaSlugMapping = (new \App\Http\Controllers\AreaController)->getAreaSlugMapping();

        // Fármacos con bajo stock
       // $bajo = Farmaco::whereColumn('stock_fisico', '<', 'stock_maximo')->with('areas')->get();
        
        // Medicamentos próximos a vencer (menos de 20 días)
        /* $proximosVencer = Farmaco::where('fecha_vencimiento', '>', now())
            ->where('fecha_vencimiento', '<=', now()->addDays(20))
            ->with('areas')
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();
        
        // Medicamentos vencidos
        $vencidos = Farmaco::where('fecha_vencimiento', '<', now())->with('areas')->get(); */
        
        // Medicamentos controlados
        $controlados = Farmaco::where('controlado', true)->with('areas')->get();
        
        // Medicamentos con mayor stock disponible
        /* $mayorStock = Farmaco::orderBy('stock_fisico', 'desc')
            ->with('areas')
            ->limit(5)
            ->get(); */
        
        // Medicamentos con mayor salida - suma total de cantidad_salida
        $mayorSalida = Farmaco::withSum('salidas', 'cantidad_salida')
            ->orderBy('salidas_sum_cantidad_salida', 'desc')
            ->with('areas')
            ->limit(5)
            ->get();

        return view('home', compact('areas', 'areaSlugMapping', 'controlados', 'mayorSalida'));
    }
}
