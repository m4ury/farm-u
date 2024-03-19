<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaFarmacoController extends Controller
{
    public function store(Request $request)
    {
        $area_farmaco = Area::updateOrCreate($request->except('_token'));
        $area_farmaco->farmaco_id = $request->farmaco_id;
        $area_farmaco->area_id = $request->area_id;

        return redirect('areas')->withSuccess('Farmaco añadido con exito!');
    }

    public function destroy(Request $request)
    {
        //dd($request->all());
        $area = Area::findOrFail($request->area_id);
        $area->farmacos()->detach($request->farmaco_id);

        return redirect('areas')->withSuccess('farmaco eliminado con exito!');
    }
}
