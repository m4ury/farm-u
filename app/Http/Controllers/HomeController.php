<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Salida;
use App\Models\Farmaco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        //$areas = DB::table('areas')->pluck('nombre_area','id')->first();
        //$areas = new Area();
        $botiquin = Farmaco::join('area_farmaco','area_farmaco.farmaco_id','farmacos.id')
        ->join('areas','areas.id','area_farmaco.area_id')
        ->select('areas.nombre_area','farmacos.descripcion','farmacos.stock_maximo','farmacos.controlado','farmacos.fecha_vencimiento','areas.id', 'farmacos.id', 'farmacos.forma_farmaceutica', 'farmacos.dosis', 'farmacos.stock_fisico')
        ->where('nombre_area' ,'botiquín urgencias')
        ->get();

        $carro = Farmaco::join('area_farmaco','area_farmaco.farmaco_id','farmacos.id')
        ->join('areas','areas.id','area_farmaco.area_id')
        ->select('areas.nombre_area','farmacos.descripcion','farmacos.stock_maximo','farmacos.controlado','farmacos.fecha_vencimiento','areas.id', 'farmacos.id', 'farmacos.forma_farmaceutica', 'farmacos.dosis', 'farmacos.stock_fisico')
        ->where('nombre_area' ,'carro de paro urgencias')
        ->get();

        $maletin = Farmaco::join('area_farmaco','area_farmaco.farmaco_id','farmacos.id')
        ->join('areas','areas.id','area_farmaco.area_id')
        ->select('areas.nombre_area','farmacos.descripcion','farmacos.stock_maximo','farmacos.controlado','farmacos.fecha_vencimiento','areas.id', 'farmacos.id', 'farmacos.forma_farmaceutica', 'farmacos.dosis', 'farmacos.stock_fisico')
        ->where('nombre_area' ,'maletín urgencias')
        ->get();

        $bajo = Farmaco::with('areas')->where('stock_maximo', '>', 'stock_fisico')->get();

        /* $bajo = $salida->join('farmaco_salida', 'farmaco_salida.salida_id', 'salidas.id')
                            ->join('farmacos', 'farmacos.id', 'farmaco_salida.farmaco_id')
                            ->join('area_farmaco','area_farmaco.farmaco_id','farmacos.id')
                            ->join('areas','areas.id','area_farmaco.area_id')
                            ->join('users','users.id','salidas.user_id')
                            ->select('salidas.cantidad_salida', 'salidas.fecha_salida', 'users.name', 'users.apellido_p', 'farmacos.descripcion', 'farmacos.forma_farmaceutica', 'farmacos.dosis', 'farmacos.stock_maximo', 'farmacos.stock_fisico', 'farmacos.fecha_vencimiento')
                            ->where('farmacos.stock_fisico', '<','farmacos.stock_maximo')
                            ->get(); */

        return view('home', compact('botiquin', 'carro', 'maletin', 'bajo'));
    }
}


