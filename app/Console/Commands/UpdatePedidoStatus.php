<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use Illuminate\Console\Command;

class UpdatePedidoStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedidos:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar pedidos de estado "solicitado" a "pendiente"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = Pedido::where('estado', 'solicitado')
                         ->update(['estado' => 'pendiente']);

        $this->info("✓ Se actualizaron $updated pedidos de 'solicitado' a 'pendiente'");
    }
}
