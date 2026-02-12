<?php

namespace App\Http\Controllers;

use App\Models\RecepcionDespacho;
use App\Models\Despacho;
use App\Models\HistoricoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecepcionDespachoController extends Controller
{
    /**
     * Mostrar formulario para confirmar recepción de despacho
     */
    public function confirmarForm(Despacho $despacho)
    {
        // Verificar que sea usuario del área que recibe
        if ($despacho->area_id != Auth::user()->area_id && !Auth::user()->isAdmin()) {
            abort(403, 'No autorizado');
        }

        // Verificar que no haya sido ya recibido
        if ($despacho->estaRecibido()) {
            return redirect()->back()->with('warning', 'Este despacho ya fue recibido.');
        }

        return view('recepciones.confirmar', compact('despacho'));
    }

    /**
     * Registrar recepción de despacho
     */
    public function confirmar(Request $request, Despacho $despacho)
    {
        // Verificar que sea usuario del área que recibe
        if ($despacho->area_id != Auth::user()->area_id && !Auth::user()->isAdmin()) {
            abort(403, 'No autorizado');
        }

        // Verificar que no haya sido ya recibido
        if ($despacho->estaRecibido()) {
            return redirect()->back()->with('warning', 'Este despacho ya fue recibido.');
        }

        $request->validate([
            'cantidad_recibida' => 'required|integer|min:1|max:' . $despacho->cantidad,
            'observaciones' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            // Crear registro de recepción
            $recepcion = RecepcionDespacho::create([
                'despacho_id' => $despacho->id,
                'user_id' => Auth::id(),
                'cantidad_recibida' => $request->cantidad_recibida,
                'observaciones' => $request->observaciones,
                'fecha_recepcion' => now()
            ]);

            // Registrar en historial como RECEPCIÓN
            HistoricoMovimiento::create([
                'farmaco_id' => $despacho->lote->farmaco_id,
                'lote_id' => $despacho->lote_id,
                'area_id' => $despacho->area_id,
                'user_id' => Auth::id(),
                'tipo' => 'recepcion',
                'cantidad' => $request->cantidad_recibida,
                'descripcion' => "Recepción confirmada de {$request->cantidad_recibida} unidades en {$despacho->area->nombre_area}",
                'fecha' => now()
            ]);

            // Si la cantidad recibida es menor que la despachada, registrar ajuste
            if ($request->cantidad_recibida < $despacho->cantidad) {
                $diferencia = $despacho->cantidad - $request->cantidad_recibida;

                // Restaurar cantidad al lote (la diferencia no recibida)
                $despacho->lote->incrementarDisponible($diferencia);

                // Registrar ajuste
                HistoricoMovimiento::create([
                    'farmaco_id' => $despacho->lote->farmaco_id,
                    'lote_id' => $despacho->lote_id,
                    'area_id' => null,
                    'user_id' => Auth::id(),
                    'tipo' => 'ajuste',
                    'cantidad' => $diferencia,
                    'descripcion' => "Ajuste: {$diferencia} unidades no recibidas en despacho {$despacho->id}",
                    'fecha' => now()
                ]);
            }

            // Registrar stock en el área (lote_area)
            $existing = DB::table('lote_area')
                ->where('lote_id', $despacho->lote_id)
                ->where('area_id', $despacho->area_id)
                ->first();

            if ($existing) {
                DB::table('lote_area')
                    ->where('id', $existing->id)
                    ->increment('cantidad_disponible', $request->cantidad_recibida);
            } else {
                DB::table('lote_area')->insert([
                    'lote_id' => $despacho->lote_id,
                    'area_id' => $despacho->area_id,
                    'cantidad_disponible' => $request->cantidad_recibida,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('farmacos.show', $despacho->lote->farmaco)
                            ->with('success', "Recepción confirmada: {$request->cantidad_recibida} unidades recibidas.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al confirmar recepción: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar historial de despachos recibidos por área
     */
    public function historialArea()
    {
        $query = RecepcionDespacho::with([
            'despacho.lote.farmaco',
            'despacho.area',
            'despacho.pedido',
            'usuario'
        ]);

        // Si no es admin, filtrar por su área
        if (!Auth::user()->isAdmin()) {
            $areaId = Auth::user()->area_id;
            $query->whereHas('despacho', function ($q) use ($areaId) {
                $q->where('area_id', $areaId);
            });
        }

        $recepciones = $query->orderBy('fecha_recepcion', 'desc')->paginate(15);

        return view('recepciones.historial-area', compact('recepciones'));
    }
}
