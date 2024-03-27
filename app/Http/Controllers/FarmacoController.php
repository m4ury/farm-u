<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Farmaco;
use Illuminate\Http\Request;

class FarmacoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmacos = Farmaco::all();
        return view("farmacos.index", compact("farmacos"));
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
        $farmaco = new Farmaco($request->except('_token'));
        $farmaco->controlado = $request->controlado ?? null;

        $farmaco->save();

        return back()->withSuccess('Farmaco creado con exito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Farmaco $farmaco)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Farmaco $farmaco)
    {
        $areas = Area::orderBy('nombre_area', 'ASC')->pluck('nombre_area', 'id');
        //$patologias = Patologia::orderBy('nombre_patologia', 'ASC')->pluck('nombre_patologia', 'id');
        return view('farmacos.edit', compact('farmaco', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farmaco $farmaco)
    {
        //dd($request->all());
        $farmaco->update($request->all());
        $farmaco->controlado = $request->controlado ?? null;
        $farmaco->areas()->sync($request->area_id);
        $farmaco->save();
        return redirect('farmacos')->withSuccess('Farmaco actualizado con exito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farmaco $farmaco)
    {
        Farmaco::destroy($farmaco->id);
        return back()->withSuccess('Farmaco eliminado con exito!');
    }
}
