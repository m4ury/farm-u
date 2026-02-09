<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Farmaco;
use App\Models\Area;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::with('user', 'area', 'farmacos')->get();
        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $areas = Area::all();
        
        // Obtener fármacos con bajo stock
        $farmacos = Farmaco::whereColumn('stock_fisico', '<', 'stock_maximo')
                            ->with('areas')
                            ->get();
        
        // Procesar cada fármaco para calcular cantidad a pedir y asegurar tipos correctos
        $farmacos = $farmacos->map(function ($farmaco) {
            $farmaco->stock_maximo = (int) $farmaco->stock_maximo;
            $farmaco->stock_fisico = (int) $farmaco->stock_fisico;
            $farmaco->cantidad_a_pedir = $farmaco->stock_maximo - $farmaco->stock_fisico;
            $farmaco->area_predeterminada = $farmaco->areas->first();
            return $farmaco;
        });
        
        return view('pedidos.create', compact('areas', 'farmacos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validateFarmacos($request);

        $request->validate([
            'fecha_pedido' => 'required|date',
            'area_id' => 'required|exists:areas,id',
            'solicitante' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'farmacos' => 'required|array|min:1',
            'farmacos.*.farmaco_id' => 'required|exists:farmacos,id',
            'farmacos.*.cantidad' => 'required|integer|min:1',
        ]);

        $pedido = Pedido::create([
            'fecha_pedido' => $request->fecha_pedido,
            'user_id' => auth()->id(),
            'area_id' => $request->area_id,
            'solicitante' => $request->solicitante,
            'observaciones' => $request->observaciones,
            'estado' => 'solicitado',
        ]);

        // Agregar los fármacos al pedido
        $farmacoData = [];
        foreach ($request->farmacos as $farmaco) {
            $farmacoData[$farmaco['farmaco_id']] = [
                'cantidad_pedida' => $farmaco['cantidad'],
            ];
        }

        $pedido->farmacos()->attach($farmacoData);

        return redirect()->route('pedidos.show', $pedido)
                        ->with('success', 'Pedido creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        $pedido->load('user', 'area', 'farmacos');
        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        $areas = Area::all();
        $pedido->load('area', 'farmacos');
        return view('pedidos.edit', compact('pedido', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'fecha_pedido' => 'required|date',
            'area_id' => 'required|exists:areas,id',
            'solicitante' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:solicitado,rechazado,entregado',
        ]);

        $pedido->update($request->only([
            'fecha_pedido',
            'area_id',
            'solicitante',
            'observaciones',
            'estado'
        ]));

        return redirect()->route('pedidos.show', $pedido)
                        ->with('success', 'Pedido actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return redirect()->route('pedidos.index')
                        ->with('success', 'Pedido eliminado exitosamente.');
    }

    /**
     * Crear un pedido desde la selección de fármacos
     */
    public function storeFromSelection(Request $request)
    {
        $request->validate([
            'fecha_pedido' => 'required|date',
            'solicitante' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'farmacos' => 'required|array|min:1',
            'farmacos.*.id' => 'required|exists:farmacos,id',
            'farmacos.*.cantidad' => 'required|integer|min:1',
        ]);

        // Obtener el área del primer fármaco
        $primerFarmaco = Farmaco::find($request->farmacos[0]['id']);
        $areaId = $primerFarmaco->areas()->first()?->id;

        if (!$areaId) {
            return response()->json([
                'message' => 'No se pudo determinar el área para los fármacos seleccionados',
            ], 422);
        }

        $pedido = Pedido::create([
            'fecha_pedido' => $request->fecha_pedido,
            'user_id' => auth()->id(),
            'area_id' => $areaId,
            'solicitante' => $request->solicitante,
            'observaciones' => $request->observaciones,
            'estado' => 'solicitado',
        ]);

        // Agregar los fármacos al pedido
        $farmacoData = [];
        foreach ($request->farmacos as $farmaco) {
            $farmacoData[$farmaco['id']] = [
                'cantidad_pedida' => $farmaco['cantidad'],
            ];
        }

        $pedido->farmacos()->attach($farmacoData);

        return response()->json([
            'success' => true,
            'message' => 'Pedido creado exitosamente.',
            'pedido_id' => $pedido->id
        ]);
    }

    /**
     * Validar que las cantidades solicitadas no excedan lo permitido
     */
    private function validateFarmacos(Request $request)
    {
        if (!$request->has('farmacos')) {
            return;
        }

        $farmacos = Farmaco::all()->keyBy('id');

        foreach ($request->farmacos as $index => $farmaco) {
            if (isset($farmaco['farmaco_id']) && isset($farmaco['cantidad'])) {
                $farmacoModel = $farmacos->get($farmaco['farmaco_id']);
                if ($farmacoModel) {
                    $cantidadPermitida = $farmacoModel->stock_maximo - $farmacoModel->stock_fisico;
                    if ($farmaco['cantidad'] > $cantidadPermitida) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "farmacos.{$index}.cantidad" => "La cantidad solicitada ({$farmaco['cantidad']}) no puede exceder el stock a reponer ({$cantidadPermitida}) para {$farmacoModel->descripcion}."
                        ]);
                    }
                }
            }
        }
    }
}

