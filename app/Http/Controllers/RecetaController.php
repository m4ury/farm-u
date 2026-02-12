<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use App\Models\Salida;
use App\Models\Farmaco;
use App\Models\Area;
use App\Models\Lote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listado de todas las recetas/DAU
     */
    public function index()
    {
        $recetas = Receta::with(['user', 'area', 'salidas.farmaco'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('recetas.index', compact('recetas'));
    }

    /**
     * Formulario para crear una nueva receta/DAU
     */
    public function create()
    {
        $farmacos = Farmaco::orderBy('descripcion')->get();
        $areas = Area::orderBy('nombre_area')->get();

        return view('recetas.create', compact('farmacos', 'areas'));
    }

    /**
     * Almacenar la receta con todos sus fármacos
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_dau' => 'required|string|max:100|unique:recetas,numero_dau',
            'fecha_receta' => 'required|date',
            'area_id' => 'nullable|exists:areas,id',
            'observaciones' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.farmaco_id' => 'required|exists:farmacos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.lotes' => 'required|array|min:1',
        ]);

        $areaId = $request->input('area_id') ?: null;

        DB::beginTransaction();

        try {
            // Crear la Receta
            $receta = Receta::create([
                'numero_dau' => $request->numero_dau,
                'fecha_receta' => $request->fecha_receta,
                'area_id' => $areaId,
                'user_id' => Auth::id(),
                'observaciones' => $request->observaciones,
            ]);

            $items = $request->input('items');

            foreach ($items as $item) {
                $farmaco = Farmaco::findOrFail($item['farmaco_id']);
                $cantidadSalida = (int) $item['cantidad'];

                // Verificar stock según contexto
                if ($areaId) {
                    $stockActual = $farmaco->getStockEnArea($areaId);
                } else {
                    $stockActual = $farmaco->getStockEnFarmacia();
                }

                if ($cantidadSalida > $stockActual) {
                    throw new \Exception("Stock insuficiente para {$farmaco->descripcion}. Disponible: {$stockActual}, solicitado: {$cantidadSalida}");
                }

                // Filtrar lotes con cantidad > 0
                $lotesSolicitados = collect($item['lotes'])
                    ->filter(fn($cantidad) => (int) $cantidad > 0)
                    ->map(fn($cantidad) => (int) $cantidad);

                if ($lotesSolicitados->isEmpty()) {
                    throw new \Exception("Debe asignar lotes para {$farmaco->descripcion}");
                }

                $totalLotes = $lotesSolicitados->sum();
                if ($totalLotes !== $cantidadSalida) {
                    throw new \Exception("La suma de lotes ({$totalLotes}) no coincide con la cantidad ({$cantidadSalida}) para {$farmaco->descripcion}");
                }

                // Validar lotes según contexto
                if ($areaId) {
                    $loteIds = $lotesSolicitados->keys()->all();
                    $lotesArea = DB::table('lote_area')
                        ->join('lotes', 'lotes.id', '=', 'lote_area.lote_id')
                        ->where('lote_area.area_id', $areaId)
                        ->where('lotes.farmaco_id', $farmaco->id)
                        ->whereIn('lote_area.lote_id', $loteIds)
                        ->select('lotes.*', 'lote_area.cantidad_disponible as stock_area')
                        ->get()
                        ->keyBy('id');

                    if ($lotesArea->count() !== $lotesSolicitados->count()) {
                        throw new \Exception("Lotes inválidos para {$farmaco->descripcion} en esta área");
                    }

                    foreach ($lotesArea as $lote) {
                        $cantidad = $lotesSolicitados->get($lote->id, 0);
                        if ($lote->vencido) {
                            throw new \Exception("No se puede usar un lote vencido ({$lote->num_serie})");
                        }
                        if ($cantidad > $lote->stock_area) {
                            throw new \Exception("Cantidad excede disponible en lote {$lote->num_serie} del área");
                        }
                    }

                    $lotes = $farmaco->lotes()->whereIn('id', $loteIds)->get();
                } else {
                    $lotes = $farmaco->lotes()
                        ->whereIn('id', $lotesSolicitados->keys()->all())
                        ->get();

                    if ($lotes->count() !== $lotesSolicitados->count()) {
                        throw new \Exception("Lotes inválidos para {$farmaco->descripcion}");
                    }

                    foreach ($lotes as $lote) {
                        $cantidad = $lotesSolicitados->get($lote->id, 0);
                        if ($lote->vencido || $lote->isVencido()) {
                            throw new \Exception("Lote vencido: {$lote->num_serie}");
                        }
                        if ($cantidad > $lote->cantidad_disponible) {
                            throw new \Exception("Cantidad excede disponible en lote {$lote->num_serie}");
                        }
                    }
                }

                // Crear la Salida vinculada a la Receta
                $salida = new Salida();
                $salida->receta_id = $receta->id;
                $salida->fecha_salida = Carbon::now();
                $salida->cantidad_salida = $cantidadSalida;
                $salida->numero_dau = $receta->numero_dau;
                $salida->farmaco_id = $farmaco->id;
                $salida->user_id = Auth::id();
                $salida->stock_actual = $stockActual;
                $salida->save();

                // Vincular farmaco (pivot legacy)
                $salida->farmacos()->sync([$farmaco->id]);

                // Procesar lotes
                $pivotData = [];
                foreach ($lotes as $lote) {
                    $cantidad = $lotesSolicitados->get($lote->id, 0);
                    $pivotData[$lote->id] = ['cantidad' => $cantidad];

                    if ($areaId) {
                        DB::table('lote_area')
                            ->where('lote_id', $lote->id)
                            ->where('area_id', $areaId)
                            ->decrement('cantidad_disponible', $cantidad);
                    } else {
                        $lote->decrementarDisponible($cantidad);
                    }
                }

                $salida->lotes()->sync($pivotData);
            }

            DB::commit();

            return redirect()->route('recetas.show', $receta)
                ->with('success', 'Receta/DAU creada exitosamente con ' . count($items) . ' fármaco(s)');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalle de una receta
     */
    public function show(Receta $receta)
    {
        $receta->load(['user', 'area', 'salidas.farmaco', 'salidas.lotes']);

        return view('recetas.show', compact('receta'));
    }

    /**
     * API: Obtener lotes disponibles de un fármaco (opcionalmente filtrados por área)
     */
    public function lotesDisponiblesApi(Request $request, $farmaco_id)
    {
        $farmaco = Farmaco::findOrFail($farmaco_id);
        $areaId = $request->query('area_id');

        if ($areaId) {
            $area = Area::findOrFail($areaId);
            $lotes = $area->lotesDisponiblesFarmaco($farmaco->id)
                ->select('lotes.id', 'lotes.num_serie', 'lotes.fecha_vencimiento')
                ->get()
                ->map(function ($lote) {
                    return [
                        'id' => $lote->id,
                        'num_serie' => $lote->num_serie,
                        'fecha_vencimiento' => $lote->fecha_vencimiento?->format('d-m-Y'),
                        'cantidad_disponible' => $lote->pivot->cantidad_disponible,
                    ];
                });
        } else {
            $lotes = $farmaco->lotesDisponibles()
                ->select('id', 'num_serie', 'fecha_vencimiento', 'cantidad_disponible')
                ->get()
                ->map(function ($lote) {
                    return [
                        'id' => $lote->id,
                        'num_serie' => $lote->num_serie,
                        'fecha_vencimiento' => $lote->fecha_vencimiento?->format('d-m-Y'),
                        'cantidad_disponible' => $lote->cantidad_disponible,
                    ];
                });
        }

        return response()->json($lotes);
    }

    /**
     * API: Buscar fármacos por descripción
     */
    public function buscarFarmacos(Request $request)
    {
        $query = $request->query('q', '');
        $areaId = $request->query('area_id');

        $farmacosQuery = Farmaco::where('descripcion', 'like', "%{$query}%")
            ->orderBy('descripcion');

        if ($areaId) {
            // Solo fármacos que tienen stock en esta área
            $farmacosQuery->whereHas('lotes', function ($q) use ($areaId) {
                $q->whereExists(function ($sub) use ($areaId) {
                    $sub->select(DB::raw(1))
                        ->from('lote_area')
                        ->whereColumn('lote_area.lote_id', 'lotes.id')
                        ->where('lote_area.area_id', $areaId)
                        ->where('lote_area.cantidad_disponible', '>', 0);
                });
            });
        }

        $farmacos = $farmacosQuery->limit(20)->get()->map(function ($f) use ($areaId) {
            $stock = $areaId ? $f->getStockEnArea($areaId) : $f->getStockEnFarmacia();
            return [
                'id' => $f->id,
                'descripcion' => $f->descripcion,
                'dosis' => $f->dosis,
                'forma_farmaceutica' => $f->forma_farmaceutica,
                'stock' => $stock,
            ];
        });

        return response()->json($farmacos);
    }
}
