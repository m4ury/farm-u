<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoMovimiento extends Model
{
    use HasFactory;

    protected $table = 'historico_movimientos';

    protected $fillable = [
        'farmaco_id',
        'lote_id',
        'area_id',
        'user_id',
        'tipo',
        'cantidad',
        'descripcion',
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    /**
     * Relación con Farmaco
     */
    public function farmaco()
    {
        return $this->belongsTo(Farmaco::class);
    }

    /**
     * Relación con Lote
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    /**
     * Relación con Area
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación con User
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtener movimientos de un área
     */
    public static function porArea($areaId)
    {
        return self::where('area_id', $areaId)->orderBy('fecha', 'desc');
    }

    /**
     * Obtener movimientos de un fármaco
     */
    public static function porFarmaco($farmacoId)
    {
        return self::where('farmaco_id', $farmacoId)->orderBy('fecha', 'desc');
    }

    /**
     * Obtener movimientos por tipo
     */
    public static function porTipo($tipo)
    {
        return self::where('tipo', $tipo)->orderBy('fecha', 'desc');
    }
}
