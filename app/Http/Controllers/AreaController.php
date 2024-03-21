<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Farmaco;
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

    public function botiquinList(){

        $areas = Farmaco::join('area_farmaco','area_farmaco.farmaco_id','farmacos.id')
        ->join('areas','areas.id','area_farmaco.area_id')
        ->select('areas.nombre_area','farmacos.descripcion','farmacos.stock_maximo','farmacos.controlado','farmacos.fecha_vencimiento','areas.id', 'farmacos.id', 'farmacos.forma_farmaceutica', 'farmacos.dosis', 'farmacos.stock_fisico')
        ->where('nombre_area' ,'botiquín urgencias')
        ->get();
//dd($areas);
        return view('areas.botiquin', compact('areas'));
    }

    public function carroList(){
        $areas = Farmaco::join('area_farmaco','area_farmaco.farmaco_id','farmacos.id')
        ->join('areas','areas.id','area_farmaco.area_id')
        ->select('areas.nombre_area','farmacos.descripcion','farmacos.stock_maximo','farmacos.controlado','farmacos.fecha_vencimiento','areas.id', 'farmacos.id', 'farmacos.forma_farmaceutica', 'farmacos.dosis', 'farmacos.stock_fisico')
        ->where('nombre_area' ,'carro de paro urgencias')
        ->get();
//dd($areas);
        return view('areas.carro', compact('areas'));
    }

    public function maletinList(){
        $areas = Farmaco::join('area_farmaco','area_farmaco.farmaco_id','farmacos.id')
        ->join('areas','areas.id','area_farmaco.area_id')
        ->select('areas.nombre_area','farmacos.descripcion','farmacos.stock_maximo','farmacos.controlado','farmacos.fecha_vencimiento','areas.id', 'farmacos.id', 'farmacos.forma_farmaceutica', 'farmacos.dosis', 'farmacos.stock_fisico')
        ->where('nombre_area' ,'maletín urgencias')
        ->get();
//dd($areas);
        return view('areas.maletin', compact('areas'));
    }
}

/* select `areas`.`nombre_area`, `farmacos`.`descripcion`, `farmacos`.`stock_maximo`, `farmacos`.`controlado`, `farmacos`.`fecha_vencimiento`, `areas`.`area_id` from `farmacos` inner join `area_farmaco` on `area_farmaco`.`farmaco_id` = `farmacos`.`id` inner join `areas` on `areas`.`id` = `area_farmaco`.`area_id` where `nombre_area` = ?
 */
