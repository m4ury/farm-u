<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Farmaco;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salidas = Salida::with('farmacos')->get();

        return view('salidas.index', compact('salidas'));
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
        $request->validate([
            'id' => 'required|exists:farmacos,id',
            'numero_dau' => 'required|string|max:100',
            'cantidad_salida' => 'required|integer|min:1',
            'lotes' => 'required|array|min:1',
            'lotes.*' => 'nullable|integer|min:0'
        ]);

        $farmaco = Farmaco::findOrFail($request->input('id'));
        $stockActual = $farmaco->getStockFisicoCalculado();

        if ($request->cantidad_salida > $stockActual) {
            return back()->withError('No es posible realizar, no hay stock suficiente');
        }

        $lotesSolicitados = collect($request->input('lotes', []))
            ->filter(fn ($cantidad) => (int) $cantidad > 0)
            ->map(fn ($cantidad) => (int) $cantidad);

        if ($lotesSolicitados->isEmpty()) {
            return back()->withError('Debe seleccionar al menos un lote');
        }

        $totalLotes = $lotesSolicitados->sum();
        if ($totalLotes !== (int) $request->cantidad_salida) {
            return back()->withError('La cantidad total por lote debe coincidir con la salida');
        }

        $lotes = $farmaco->lotes()
            ->whereIn('id', $lotesSolicitados->keys()->all())
            ->get();

        if ($lotes->count() !== $lotesSolicitados->count()) {
            return back()->withError('Algunos lotes seleccionados no pertenecen al farmaco');
        }

        foreach ($lotes as $lote) {
            $cantidad = $lotesSolicitados->get($lote->id, 0);

            if ($lote->vencido || $lote->isVencido()) {
                return back()->withError('No se puede usar un lote vencido');
            }

            if ($cantidad > $lote->cantidad_disponible) {
                return back()->withError('La cantidad solicitada excede el disponible en un lote');
            }
        }

        DB::beginTransaction();

        try {
            $salida = new Salida();
            $salida->fecha_salida = Carbon::now();
            $salida->cantidad_salida = (int) $request->cantidad_salida;
            $salida->numero_dau = $request->numero_dau;
            $salida->user_id = Auth::user()->id;
            $salida->stock_actual = $stockActual;
            $salida->save();

            $salida->farmacos()->sync([$farmaco->id]);

            $pivotData = [];
            foreach ($lotes as $lote) {
                $cantidad = $lotesSolicitados->get($lote->id, 0);
                $pivotData[$lote->id] = ['cantidad' => $cantidad];
                $lote->decrementarDisponible($cantidad);
            }

            $salida->lotes()->sync($pivotData);

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return back()->withSuccess('Realizado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Salida $salida)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Salida $salida)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salida $salida)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salida $salida)
    {
        //
    }
}
