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
        // Detach related farmacos to avoid foreign key constraint errors
        $area->farmacos()->detach();
        $area->delete();
        return back()->withErrors('Area eliminada con exito!');
    }

    /**
     * Obtener configuración de áreas desde la BD dinámicamente
     */
    private function getAreaConfig(){
        $areas = Area::all();
        $config = [];
        
        foreach($areas as $area){
            // Generar slug automáticamente del nombre_area
            $slug = \Illuminate\Support\Str::slug($area->nombre_area, '-');
            // Capitalizar el nombre para el título
            $titulo = ucwords($area->nombre_area);
            
            $config[$slug] = [$area->nombre_area, $titulo];
        }
        
        return $config;
    }

    /**
     * Obtener mapeo de nombre_area a slug
     */
    public function getAreaSlugMapping(){
        $config = $this->getAreaConfig();
        $mapping = [];
        foreach($config as $slug => $data){
            $mapping[$data[0]] = $slug; // $data[0] es el nombre_area
        }
        return $mapping;
    }

    /**
     * Mostrar medicamentos de un área de forma dinámica
     * @param string $areaType - Tipo de área (botiquin, carro, maletin, etc)
     */
    public function showArea($areaType){
        $config = $this->getAreaConfig();
        
        if(!isset($config[$areaType])){
            abort(404, "Área no encontrada");
        }
        
        [$areaName, $titulo] = $config[$areaType];
        return $this->showAreaMedicamentos($areaName, 'areas.show', $titulo);
    }

    /**
     * Mostrar medicamentos de un área dinámicamente
     * @param string $areaName - Nombre del área a buscar
     * @param string $viewName - Nombre de la vista a renderizar
     * @param string $titulo - Título a mostrar en la vista
     */
    public function showAreaMedicamentos($areaName, $viewName, $titulo){
        $areas = Farmaco::join('area_farmaco','area_farmaco.farmaco_id','farmacos.id')
            ->join('areas','areas.id','area_farmaco.area_id')
            ->select('areas.nombre_area','farmacos.descripcion','farmacos.stock_maximo',
                     'farmacos.controlado','farmacos.fecha_vencimiento','areas.id', 
                     'farmacos.id', 'farmacos.forma_farmaceutica', 'farmacos.dosis', 
                     'farmacos.stock_fisico')
            ->where('nombre_area', $areaName)
            ->get();
        
        return view($viewName, compact('areas', 'titulo'));
    }
}

/* select `areas`.`nombre_area`, `farmacos`.`descripcion`, `farmacos`.`stock_maximo`, `farmacos`.`controlado`, `farmacos`.`fecha_vencimiento`, `areas`.`area_id` from `farmacos` inner join `area_farmaco` on `area_farmaco`.`farmaco_id` = `farmacos`.`id` inner join `areas` on `areas`.`id` = `area_farmaco`.`area_id` where `nombre_area` = ?
 */
