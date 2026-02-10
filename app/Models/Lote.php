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
