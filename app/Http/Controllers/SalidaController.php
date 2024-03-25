<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Farmaco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salida = Salida::orderBy('fecha_salida', 'desc')->get();
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

    /* $farmaco = new Farmaco($request->except('_token'));
        $farmaco->controlado = $request->controlado ?? null;

        $farmaco->save(); */
    {
        //dd($request->all());
        $salida = new Salida($request->except('_token'));
        $salida->user_id = Auth::user()->id;
        $salida->farmaco_id = $request->input("id");
        $farmaco = Farmaco::findOrFail($salida->farmaco_id);
        if ($request->cantidad_salida > $farmaco->stock_fisico) {
            return redirect()->back()->withError("error","No es posible realizar, sin stock suficiente");
        } else {
            $nuevo_stock = $farmaco->stock_fisico - $request->cantidad_salida;
            $farmaco->update(["stock_fisico"=> $nuevo_stock]);
            $salida->save();
            return redirect()->back()->withSuccess("Realizado con exito");
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
