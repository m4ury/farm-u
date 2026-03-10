<?php

namespace App\Console\Commands;

use App\Models\AreaFarmaco;
use App\Models\HistoricoMovimiento;
use App\Models\Lote;
use Illuminate\Console\Command;

class InitializeStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:initialize-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa el stock de cada fármaco en cada área asignada al stock_minimo correspondiente.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando inicialización de stock...');

        $areaFarmacos = AreaFarmaco::with(['farmaco', 'area'])->get();

        foreach ($areaFarmacos as $af) {
            $stockMinimo = $af->stock_minimo;

            if ($stockMinimo <= 0) {
                $this->warn("Saltando {$af->farmaco->descripcion} en {$af->area->nombre_area}: stock_minimo es 0 o negativo.");
                continue;
            }

            // Verificar si el fármaco tiene lotes
            $lote = $af->farmaco->lotes()->first();

            if (!$lote) {
                $this->warn("Saltando {$af->farmaco->descripcion} en {$af->area->nombre_area}: no hay lotes disponibles.");
                continue;
            }

            // Crear o actualizar lote_area con cantidad_disponible = stock_minimo
            $lote->areas()->syncWithoutDetaching([$af->area_id => ['cantidad_disponible' => $stockMinimo]]);

            $this->info("Inicializado stock para {$af->farmaco->descripcion} en {$af->area->nombre_area}: {$stockMinimo} unidades.");
        }

        $this->info('Inicialización de stock completada.');
    }
}
