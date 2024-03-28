<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Farmaco;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salidas = Salida::with('farmacos')->get();

        return view('salidas.index', compact('salidas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());

        $farmaco = Farmaco::findOrFail($request->input("id"));
        if ($request->cantidad_salida > $farmaco->stock_fisico) {
            return back()->withError("No es posible realizar, no hay stock suficiente");
        } else {
            $salida = Salida::create($request->except('_token'));
            $salida->user_id = Auth::user()->id;
            $salida->fecha_salida = Carbon::now();
            $salida->save();

            $nuevo_stock = $farmaco->stock_fisico - $request->cantidad_salida;
            $salida->stock_actual = $nuevo_stock;
            $salida->save();
            $farmaco->update(["stock_fisico" => $nuevo_stock]);

            $salida->farmacos()->sync($request->input("id"));

            return back()->withSuccess("Realizado con exito");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Salida $salida)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Salida $salida)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salida $salida)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salida $salida)
    {
        //
    }
}
