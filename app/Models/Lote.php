<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lote extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmaco_id',
        'num_serie',
        'fecha_vencimiento',
        'cantidad',
        'cantidad_disponible',
        'vencido'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
    ];

    /**
     * Relación con Farmaco
     */
    public function farmaco()
    {
        return $this->belongsTo(Farmaco::class);
    }

    /**
     * Relación con Despachos
     */
    public function despachos()
    {
        return $this->hasMany(Despacho::class);
    }

    /**
     * Relación con Salidas
     */
    public function salidas()
    {
        return $this->belongsToMany(Salida::class, 'lote_salida')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    /**
     * Relación con Áreas - stock disponible por área (post-recepción)
     */
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'lote_area')
            ->withPivot('cantidad_disponible')
            ->withTimestamps();
    }

    /**
     * Obtener cantidad disponible de este lote en un área específica
     */
    public function cantidadEnArea($areaId)
    {
        $pivot = $this->areas()->where('area_id', $areaId)->first();
        return $pivot ? $pivot->pivot->cantidad_disponible : 0;
    }

    /**
     * Verificar si el lote está vencido
     */
    public function isVencido()
    {
        return Carbon::now()->isAfter($this->fecha_vencimiento);
    }

    /**
     * Actualizar cantidad disponible después de un despacho
     */
    public function decrementarDisponible($cantidad)
    {
        $this->cantidad_disponible -= $cantidad;
        $this->cantidad_disponible = max(0, $this->cantidad_disponible);
        $this->save();
    }

    /**
     * Aumentar cantidad disponible (devoluciones, etc)
     */
    public function incrementarDisponible($cantidad)
    {
        $this->cantidad_disponible += $cantidad;
        $this->cantidad_disponible = min($this->cantidad, $this->cantidad_disponible);
        $this->save();
    }
}
