<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Farmaco;
use App\Models\Area;
use App\Models\Lote;
use App\Models\Despacho;
use App\Models\HistoricoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public function index(Request $request)
    {
        $query = Pedido::with(['user', 'area', 'farmacos', 'usuarioAprobador']);

        // Filtrar por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtrar por área
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Ordenar por fecha descendente
        $pedidos = $query->orderBy('fecha_pedido', 'desc')->paginate(15);

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
            'estado' => 'pendiente',
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
        $pedido->load(['user', 'area', 'farmacos', 'usuarioAprobador', 'despachos']);
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
            'estado' => 'pendiente',
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

    /**
     * Formulario para aprobar pedido (total o parcial)
     */
    public function aprobarForm(Pedido $pedido)
    {
        // Verificar que solo farmacia/admin pueda aprobar
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $pedido->load(['farmacos']);

        // Obtener información de stock disponible para cada farmaco
        $farmacos_info = [];
        foreach ($pedido->farmacos as $farmaco) {
            $cantidad_pedida = $farmaco->pivot->cantidad_pedida;
            $stock_disponible = $farmaco->getStockFisicoCalculado();
            $lotes_disponibles = $farmaco->lotesDisponibles()->get();

            $farmacos_info[$farmaco->id] = [
                'farmaco' => $farmaco,
                'cantidad_pedida' => $cantidad_pedida,
                'stock_disponible' => $stock_disponible,
                'cantidad_aprobada' => $farmaco->pivot->cantidad_aprobada,
                'lotes' => $lotes_disponibles
            ];
        }

        return view('pedidos.aprobar', compact('pedido', 'farmacos_info'));
    }

    /**
     * Procesar aprobación de pedido
     */
    public function aprobar(Request $request, Pedido $pedido)
    {
        // Verificar autorización
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        // Validar que sea pedido pendiente
        if (!$pedido->estaPendiente()) {
            return back()->with('error', 'El pedido no está en estado pendiente');
        }

        $request->validate([
            'aprobaciones' => 'required|array',
            'aprobaciones.*.farmaco_id' => 'required|exists:farmacos,id',
            'aprobaciones.*.cantidad_aprobada' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $total_aprobado = 0;

            // Procesar cada farmaco
            foreach ($request->aprobaciones as $aprobacion) {
                $farmaco_id = $aprobacion['farmaco_id'];
                $cantidad_aprobada = $aprobacion['cantidad_aprobada'];

                if ($cantidad_aprobada > 0) {
                    // Actualizar cantidad aprobada en pivot
                    $pedido->farmacos()->updateExistingPivot($farmaco_id, [
                        'cantidad_aprobada' => $cantidad_aprobada
                    ]);

                    $total_aprobado += $cantidad_aprobada;
                }
            }

            // Determinar estado: aprobado o parcial
            $total_pedido = $pedido->getTotalPedido();
            $estado = ($total_aprobado < $total_pedido) ? Pedido::ESTADO_PARCIAL : Pedido::ESTADO_APROBADO;

            // Actualizar pedido
            $pedido->update([
                'estado' => $estado,
                'user_aprobador_id' => Auth::id(),
                'fecha_aprobacion' => now()
            ]);

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido aprobado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Formulario para rechazar pedido
     */
    public function rechazarForm(Pedido $pedido)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        return view('pedidos.rechazar', compact('pedido'));
    }

    /**
     * Procesar rechazo de pedido
     */
    public function rechazar(Request $request, Pedido $pedido)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        if (!$pedido->estaPendiente()) {
            return back()->with('error', 'El pedido no está en estado pendiente');
        }

        $request->validate([
            'motivo_rechazo' => 'required|string|min:10'
        ]);

        $pedido->update([
            'estado' => Pedido::ESTADO_RECHAZADO,
            'user_aprobador_id' => Auth::id(),
            'fecha_aprobacion' => now(),
            'motivo_rechazo' => $request->motivo_rechazo
        ]);

        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido rechazado correctamente');
    }

    /**
     * Formulario para despachar farmácos desde lotes
     */
    public function despacharForm(Pedido $pedido)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        // Debe estar aprobado o parcial
        if (!in_array($pedido->estado, [Pedido::ESTADO_APROBADO, Pedido::ESTADO_PARCIAL])) {
            abort(403, 'El pedido debe estar aprobado para despachar');
        }

        $pedido->load(['farmacos', 'area']);

        // Obtener lotes disponibles para cada farmaco
        $farmacos_despacho = collect();
        foreach ($pedido->farmacos as $farmaco) {
            $cantidad_aprobada = $farmaco->pivot->cantidad_aprobada ?? 0;
            $cantidad_despachada = $farmaco->pivot->cantidad_despachada ?? 0;
            $cantidad_pendiente = $cantidad_aprobada - $cantidad_despachada;

            if ($cantidad_pendiente > 0) {
                $lotes = $farmaco->lotesDisponibles()->get();

                $farmacos_despacho->put($farmaco->id, [
                    'farmaco' => $farmaco,
                    'cantidad_aprobada' => $cantidad_aprobada,
                    'cantidad_despachada' => $cantidad_despachada,
                    'cantidad_pendiente' => $cantidad_pendiente,
                    'lotes' => $lotes
                ]);
            }
        }

        return view('pedidos.despachar', compact('pedido', 'farmacos_despacho'));
    }

    /**
     * Procesar despacho de farmácos desde lotes
     */
    public function despachar(Request $request, Pedido $pedido)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'despachos' => 'required|array',
            'despachos.*.farmaco_id' => 'required|exists:farmacos,id',
            'despachos.*.lote_id' => 'required|exists:lotes,id',
            'despachos.*.cantidad_despacho' => 'required|integer|min:1',
            'observaciones' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->despachos as $despacho_data) {
                $farmaco_id = $despacho_data['farmaco_id'];
                $lote_id = $despacho_data['lote_id'];
                $cantidad = $despacho_data['cantidad_despacho'];

                // Obtener lote
                $lote = Lote::findOrFail($lote_id);

                // Verificar disponibilidad
                if ($lote->cantidad_disponible < $cantidad) {
                    throw new \Exception("Cantidad insuficiente en lote {$lote->num_serie}");
                }

                // Crear despacho
                $despacho = Despacho::create([
                    'pedido_id' => $pedido->id,
                    'lote_id' => $lote_id,
                    'area_id' => $pedido->area_id,
                    'cantidad' => $cantidad,
                    'user_aprobador_id' => Auth::id(),
                    'fecha_aprobacion' => now(),
                    'observaciones' => $request->observaciones
                ]);

                // Decrementar cantidad disponible en lote
                $lote->decrementarDisponible($cantidad);

                // Registrar movimiento en historial (DESPACHO)
                HistoricoMovimiento::create([
                    'farmaco_id' => $farmaco_id,
                    'lote_id' => $lote_id,
                    'area_id' => $pedido->area_id,
                    'user_id' => Auth::id(),
                    'tipo' => 'despacho',
                    'cantidad' => $cantidad,
                    'descripcion' => "Despacho de {$cantidad} unidades a {$pedido->area->nombre_area} (Pedido #{$pedido->id})",
                    'fecha' => now()
                ]);

                // Actualizar cantidad despachada en pivot
                $cantidad_actual = $pedido->farmacos()->where('farmaco_id', $farmaco_id)->first()->pivot->cantidad_despachada ?? 0;
                $pedido->farmacos()->updateExistingPivot($farmaco_id, [
                    'cantidad_despachada' => $cantidad_actual + $cantidad
                ]);
            }

            // Actualizar estado del pedido a completado si todo está despachado
            $total_aprobado = $pedido->getTotalAprobado();
            $total_despachado = $pedido->getTotalDespachado();

            if ($total_despachado >= $total_aprobado) {
                $pedido->update(['estado' => Pedido::ESTADO_COMPLETADO]);
            }

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)->with('success', 'Despacho realizado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al despachar: ' . $e->getMessage());
        }
    }

    /**
     * Modificar cantidades aprobadas de un pedido
     */
    public function modificarForm(Pedido $pedido)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        if (!in_array($pedido->estado, [Pedido::ESTADO_PENDIENTE, Pedido::ESTADO_APROBADO, Pedido::ESTADO_PARCIAL])) {
            abort(403, 'No se puede modificar este pedido');
        }

        $pedido->load(['farmacos']);

        $farmacos_info = [];
        foreach ($pedido->farmacos as $farmaco) {
            $stock_disponible = $farmaco->getStockFisicoCalculado();
            $farmacos_info[$farmaco->id] = [
                'farmaco' => $farmaco,
                'cantidad_pedida' => $farmaco->pivot->cantidad_pedida,
                'cantidad_aprobada' => $farmaco->pivot->cantidad_aprobada,
                'stock_disponible' => $stock_disponible
            ];
        }

        return view('pedidos.modificar', compact('pedido', 'farmacos_info'));
    }

    /**
     * Procesar modificación de pedido
     */
    public function modificar(Request $request, Pedido $pedido)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'modificaciones' => 'required|array',
            'modificaciones.*.farmaco_id' => 'required|exists:farmacos,id',
            'modificaciones.*.cantidad_nueva' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->modificaciones as $mod) {
                $pedido->farmacos()->updateExistingPivot($mod['farmaco_id'], [
                    'cantidad_pedida' => $mod['cantidad_nueva'],
                    'cantidad_aprobada' => $mod['cantidad_nueva']
                ]);
            }

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido modificado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al modificar pedido: ' . $e->getMessage());
        }
    }
}

