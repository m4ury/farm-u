<?php

namespace App\Models;

use App\Models\Farmaco;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Area extends Model
{
    protected $fillable = ['descripcion_area', 'nombre_area'];

    use HasFactory;

    public function farmacos()
    {
        return $this->belongsToMany(Farmaco::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function movimientos()
    {
        return $this->hasMany(HistoricoMovimiento::class);
    }

    /**
     * Lotes disponibles en esta área (recibidos vía despacho)
     */
    public function lotes()
    {
        return $this->belongsToMany(Lote::class, 'lote_area')
            ->withPivot('cantidad_disponible')
            ->withTimestamps();
    }

    /**
     * Obtener stock total de un fármaco en esta área
     */
    public function getStockFarmaco($farmacoId)
    {
        return $this->lotes()
            ->where('farmaco_id', $farmacoId)
            ->sum('lote_area.cantidad_disponible');
    }

    /**
     * Obtener lotes disponibles de un fármaco en esta área (FIFO)
     */
    public function lotesDisponiblesFarmaco($farmacoId)
    {
        return $this->lotes()
            ->where('farmaco_id', $farmacoId)
            ->where('lote_area.cantidad_disponible', '>', 0)
            ->where('vencido', false)
            ->orderBy('fecha_vencimiento', 'asc');
    }
}
