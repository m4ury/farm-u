<?php

namespace App\Http\Controllers;

use App\Models\HistoricoMovimiento;
use App\Models\Farmaco;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoricoMovimientoController extends Controller
{
    /**
     * Mostrar historial completo de movimientos (admin/farmacia)
     */
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $query = HistoricoMovimiento::with(['farmaco', 'lote', 'area', 'usuario']);

        // Filtrar por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtrar por fármaco
        if ($request->filled('farmaco_id')) {
            $query->where('farmaco_id', $request->farmaco_id);
        }

        // Filtrar por área
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Filtrar por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $movimientos = $query->orderBy('fecha', 'desc')->paginate(20);

        $farmacos = Farmaco::orderBy('descripcion')->get();
        $areas = Area::orderBy('nombre_area')->get();

        return view('historial.movimientos', compact('movimientos', 'farmacos', 'areas'));
    }

    /**
     * Historial de movimientos por fármaco
     */
    public function porFarmaco(Farmaco $farmaco)
    {
        $movimientos = $farmaco->movimientos()
            ->with(['lote', 'area', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->paginate(25);

        return view('historial.farmaco', compact('farmaco', 'movimientos'));
    }

    /**
     * Historial de movimientos por área
     */
    public function porArea(Area $area)
    {
        // Verificar permisos
        if (Auth::user()->area_id != $area->id && !Auth::user()->isAdmin()) {
            abort(403, 'No autorizado');
        }

        $movimientos = $area->movimientos()
            ->with(['farmaco', 'lote', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->paginate(25);

        return view('historial.area', compact('area', 'movimientos'));
    }

    /**
     * Reporte de movimientos por tipo
     */
    public function reportePorTipo(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $tipo = $request->tipo ?? 'despacho';
        $fecha_desde = $request->fecha_desde ?? now()->subMonth()->toDateString();
        $fecha_hasta = $request->fecha_hasta ?? now()->toDateString();

        $movimientos = HistoricoMovimiento::where('tipo', $tipo)
            ->whereDate('fecha', '>=', $fecha_desde)
            ->whereDate('fecha', '<=', $fecha_hasta)
            ->with(['farmaco', 'lote', 'area', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        // Agrupar por fármaco para estadísticas
        $estadisticas = $movimientos->groupBy('farmaco_id')->map(function ($items) {
            return [
                'farmaco' => $items->first()->farmaco,
                'total_cantidad' => $items->sum('cantidad'),
                'total_movimientos' => $items->count(),
            ];
        })->sortByDesc('total_cantidad');

        return view('historial.reporte-tipo', compact('movimientos', 'estadisticas', 'tipo', 'fecha_desde', 'fecha_hasta'));
    }

    /**
     * Exportar historial a CSV
     */
    public function exportar(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isFarmacia()) {
            abort(403, 'No autorizado');
        }

        $query = HistoricoMovimiento::query();

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('farmaco_id')) {
            $query->where('farmaco_id', $request->farmaco_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $movimientos = $query->with(['farmaco', 'lote', 'area', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        $filename = 'historial_movimientos_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->stream(
            function () use ($movimientos) {
                $handle = fopen('php://output', 'w');
                
                // Headers
                fputcsv($handle, ['Fecha', 'Tipo', 'Fármaco', 'Lote', 'Área', 'Cantidad', 'Usuario', 'Descripción']);

                // Data
                foreach ($movimientos as $m) {
                    fputcsv($handle, [
                        $m->fecha->format('d/m/Y H:i'),
                        ucfirst($m->tipo),
                        $m->farmaco->descripcion ?? 'N/A',
                        $m->lote->num_serie ?? 'N/A',
                        $m->area->nombre_area ?? 'N/A',
                        $m->cantidad,
                        $m->usuario->name ?? 'N/A',
                        $m->descripcion
                    ]);
                }

                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\""
            ]
        );
    }
}
