<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Farmaco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoteController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listar lotes con filtros
     */
    public function index(Request $request)
    {
        $query = Lote::with('farmaco');

        // Filtrar por farmaco
        if ($request->filled('farmaco_id')) {
            $query->where('farmaco_id', $request->farmaco_id);
        }

        // Filtrar vencidos
        if ($request->filled('vencidos')) {
            if ($request->vencidos == 'si') {
                $query->where('vencido', true);
            } elseif ($request->vencidos == 'no') {
                $query->where('vencido', false);
            }
        }

        // Ordenar por fecha de vencimiento
        $lotes = $query->orderBy('fecha_vencimiento', 'asc')->paginate(20);

        // Marcar los vencidos
        $lotes->getCollection()->transform(function ($lote) {
            $lote->vencido = $lote->isVencido();
            return $lote;
        });

        return view('lotes.index', compact('lotes'));
    }

    /**
     * Mostrar formulario para crear nuevo lote
     */
    public function create()
    {
        // Verificar que solo farmacia/admin pueda crear lotes
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $farmacos = Farmaco::all();
        return view('lotes.create', compact('farmacos'));
    }

    /**
     * Guardar nuevo lote
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'farmaco_id' => 'required|exists:farmacos,id',
            'num_serie' => 'required|string|unique:lotes,num_serie',
            'fecha_vencimiento' => 'required|date|after:today',
            'cantidad' => 'required|integer|min:1',
        ]);

        $lote = Lote::create([
            'farmaco_id' => $request->farmaco_id,
            'num_serie' => $request->num_serie,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'cantidad' => $request->cantidad,
            'cantidad_disponible' => $request->cantidad,
            'vencido' => false
        ]);

        return redirect()->route('lotes.show', $lote)
                        ->with('success', 'Lote creado exitosamente.');
    }

    /**
     * Mostrar detalle de un lote
     */
    public function show(Lote $lote)
    {
        $lote->load(['farmaco', 'despachos']);
        $lote->vencido = $lote->isVencido();
        return view('lotes.show', compact('lote'));
    }

    /**
     * Formulario para editar lote
     */
    public function edit(Lote $lote)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $farmacos = Farmaco::all();
        return view('lotes.edit', compact('lote', 'farmacos'));
    }

    /**
     * Actualizar lote
     */
    public function update(Request $request, Lote $lote)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'farmaco_id' => 'required|exists:farmacos,id',
            'num_serie' => "required|string|unique:lotes,num_serie,{$lote->id}",
            'fecha_vencimiento' => 'required|date',
            'cantidad' => 'required|integer|min:1',
        ]);

        // Calcular nueva cantidad disponible
        $nueva_cantidad = $request->cantidad;
        $diferencia = $nueva_cantidad - $lote->cantidad;
        $nueva_disponible = $lote->cantidad_disponible + $diferencia;
        $nueva_disponible = max(0, min($nueva_cantidad, $nueva_disponible));

        $lote->update([
            'farmaco_id' => $request->farmaco_id,
            'num_serie' => $request->num_serie,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'cantidad' => $nueva_cantidad,
            'cantidad_disponible' => $nueva_disponible,
            'vencido' => $lote->isVencido()
        ]);

        return redirect()->route('lotes.show', $lote)
                        ->with('success', 'Lote actualizado exitosamente.');
    }

    /**
     * Eliminar lote (solo si no tiene despachos)
     */
    public function destroy(Lote $lote)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        if ($lote->despachos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un lote que tiene despachos registrados');
        }

        $lote->delete();
        return redirect()->route('lotes.index')
                        ->with('success', 'Lote eliminado exitosamente.');
    }

    /**
     * Marcar lote como vencido
     */
    public function marcarVencido(Lote $lote)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $lote->update(['vencido' => true]);

        return back()->with('success', 'Lote marcado como vencido');
    }

    /**
     * Obtener lotes disponibles de un farmaco via API
     */
    public function lotesDisponibles($farmaco_id)
    {
        $farmaco = Farmaco::findOrFail($farmaco_id);
        $lotes = $farmaco->lotesDisponibles()->select(['id', 'num_serie', 'fecha_vencimiento', 'cantidad_disponible'])->get();
        
        return response()->json($lotes);
    }
}
