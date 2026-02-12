<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class Farmaco extends Model
{
    protected $fillable = ['descripcion', 'dosis', 'forma_farmaceutica', 'stock_minimo', 'controlado'];

    use HasFactory;

    /**
     * Accessor para stock_fisico - devuelve el valor calculado dinámicamente
     * en lugar del valor estático de la BD
     */
    protected function stockFisico(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getStockFisicoCalculado()
        );
    }

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
     * Calcular stock físico total (farmacia central + todas las áreas)
     */
    public function getStockFisicoCalculado()
    {
        $stockFarmacia = $this->getStockEnFarmacia();
        $stockAreas = $this->getStockEnAreas();
        return $stockFarmacia + $stockAreas;
    }

    /**
     * Stock en farmacia central (lotes.cantidad_disponible)
     */
    public function getStockEnFarmacia()
    {
        return $this->lotes()->sum('cantidad_disponible');
    }

    /**
     * Stock total en todas las áreas (lote_area.cantidad_disponible)
     */
    public function getStockEnAreas()
    {
        return \DB::table('lote_area')
            ->join('lotes', 'lotes.id', '=', 'lote_area.lote_id')
            ->where('lotes.farmaco_id', $this->id)
            ->sum('lote_area.cantidad_disponible');
    }

    /**
     * Stock de este fármaco en un área específica
     */
    public function getStockEnArea($areaId)
    {
        return \DB::table('lote_area')
            ->join('lotes', 'lotes.id', '=', 'lote_area.lote_id')
            ->where('lotes.farmaco_id', $this->id)
            ->where('lote_area.area_id', $areaId)
            ->sum('lote_area.cantidad_disponible');
    }

    /**
     * Lotes disponibles de este fármaco en un área específica (FIFO)
     */
    public function lotesEnArea($areaId)
    {
        return $this->lotes()
            ->join('lote_area', function ($join) use ($areaId) {
                $join->on('lotes.id', '=', 'lote_area.lote_id')
                     ->where('lote_area.area_id', '=', $areaId)
                     ->where('lote_area.cantidad_disponible', '>', 0);
            })
            ->where('vencido', false)
            ->orderBy('fecha_vencimiento', 'asc')
            ->select('lotes.*', 'lote_area.cantidad_disponible as stock_area')
            ->get();
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
