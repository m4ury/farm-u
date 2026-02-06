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
        $bajo = Farmaco::whereColumn('stock_fisico', '<', 'stock_maximo')->with('areas')->get();

        return view('home', compact('areas', 'areaSlugMapping', 'bajo'));
    }
}
