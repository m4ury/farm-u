<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return view("areas.index", compact("areas"));
    }

    public function store(Request $request)
    {
        $area = new Area($request->except('_token'));
       // $area->controlado = $request->controlado ?? null;

        $area->save();

        return back()->withSuccess('Area creada con exito!');
    }

    public function show(Area $area)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area)
    {

        $area->update($request->all());
        $area->save();
        return redirect('areas')->withSuccess('Solicitud actualizado con exito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        Area::destroy($area->id);
        return back()->withErrors('Area eliminada con exito!');
    }

    public function botiquinLIst(){
        $areas = Area::with('farmacos')->where('area.nombre_area' ,'botiquín urgencias')
        ->select('farmacos.descripcion', 'farmacos.stock_maximo', 'farmacos.stock_fisico')
        ->get();
    }
}
