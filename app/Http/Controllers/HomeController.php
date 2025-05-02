<?php

namespace App\Http\Controllers;

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
        $botiquin = Farmaco::whereHas('areas', function ($query) {
            $query->where('nombre_area', 'botiquín urgencias');
        })->get();
        $carro = Farmaco::whereHas('areas', function ($query) {
            $query->where('nombre_area', 'carro de paro urgencias');
        })->get();
        $maletin = Farmaco::whereHas('areas', function ($query) {
            $query->where('nombre_area', 'maletín urgencias');
        })->get();

        $bajo = Farmaco::whereColumn('stock_fisico', '<', 'stock_maximo')->with('areas')->get();

        return view('home', compact('botiquin', 'carro', 'maletin', 'bajo'));
    }
}
