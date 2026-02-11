<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Farmaco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        // Si es una petición AJAX, retornar JSON
        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            $farmaco->load('areas'); // Cargar las áreas asociadas
            $areas = Area::orderBy('nombre_area', 'ASC')->get();
            return response()->json([
                'farmaco' => $farmaco,
                'areas' => $areas
            ]);
        }

        $areas = Area::orderBy('nombre_area', 'ASC')->pluck('nombre_area', 'id');
        //$patologias = Patologia::orderBy('nombre_patologia', 'ASC')->pluck('nombre_patologia', 'id');
        return view('farmacos.edit', compact('farmaco', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farmaco $farmaco)
    {
        // Validar datos
        $request->validate([
            'descripcion' => 'sometimes|string|max:255',
            'dosis' => 'sometimes|string|max:100',
            'forma_farmaceutica' => 'sometimes|string|max:100',
            'stock_maximo' => 'sometimes|integer|min:0',
            'controlado' => 'sometimes|boolean',
            'area_id' => 'sometimes|nullable|exists:areas,id'
        ]);

        // Actualizar farmaco (excluye area_id para manejarlo por separado)
        $farmaco->update($request->except('area_id'));
        $farmaco->controlado = $request->input('controlado', 0);

        // Sincronizar áreas
        $areaId = $request->area_id;
        if ($areaId) {
            $farmaco->areas()->sync([$areaId]);
        } else {
            $farmaco->areas()->detach();
        }

        $farmaco->save();

        Log::info('UPDATE FARMACO: ' . $farmaco->descripcion . ' USER: ' . auth()->user()->rut . ' - HORA/FECHA: ' . now());

        // Si es AJAX, retornar JSON
        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Fármaco actualizado correctamente',
                'farmaco' => $farmaco
            ]);
        }

        return redirect('farmacos')->withSuccess('Farmaco actualizado con exito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farmaco $farmaco)
    {
        // Detach related areas to avoid foreign key constraint errors
        Log::info('DELETE FARMACO:  ' . $farmaco . 'USER: ' . auth()->user()->rut. ' - ' . 'HORA/FECHA: ' . now());
        $farmaco->areas()->detach();
        $farmaco->delete();
        return back()->withSuccess('Farmaco eliminado con exito!');
    }
}
