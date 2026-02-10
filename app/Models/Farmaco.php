<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmaco extends Model
{
    protected $fillable = ['descripcion', 'dosis', 'forma_farmaceutica', 'stock_maximo', 'stock_fisico', 'controlado'];

    use HasFactory;

    public function areas(){
        return $this->belongsToMany(Area::class);
    }

    public function salidas(){
        return $this->belongsToMany(Salida::class);
    }

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'farmaco_pedido')
                    ->withPivot('cantidad_pedida', 'cantidad_aprobada', 'cantidad_despachada')
                    ->withTimestamps();
    }

    /**
     * Relación con Lotes - múltiples lotes por farmaco
     */
    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

    /**
     * Calcular stock físico dinámicamente desde lotes disponibles
     */
    public function getStockFisicoCalculado()
    {
        return $this->lotes()->sum('cantidad_disponible');
    }

    /**
     * Obtener lotes disponibles (no vencidos) ordenados por fecha de vencimiento (FIFO)
     */
    public function lotesDisponibles()
    {
        return $this->lotes()
                    ->where('vencido', false)
                    ->where('cantidad_disponible', '>', 0)
                    ->orderBy('fecha_vencimiento', 'asc');
    }

    /**
     * Obtener lotes vencidos
     */
    public function lotesVencidos()
    {
        return $this->lotes()->where('vencido', true);
    }

    /**
     * Verificar si hay stock disponible
     */
    public function tieneStockDisponible($cantidad = 1)
    {
        return $this->getStockFisicoCalculado() >= $cantidad;
    }

    /**
     * Relación con Historial de Movimientos
     */
    public function movimientos()
    {
        return $this->hasMany(HistoricoMovimiento::class);
    }
}
