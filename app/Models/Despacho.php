<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despacho extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'lote_id',
        'area_id',
        'cantidad',
        'user_aprobador_id',
        'fecha_aprobacion',
        'observaciones'
    ];

    protected $casts = [
        'fecha_aprobacion' => 'datetime',
    ];

    /**
     * Relación con Pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Relación con Lote
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    /**
     * Relación con Area (destino)
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación con User (quien aprobó)
     */
    public function usuarioAprobador()
    {
        return $this->belongsTo(User::class, 'user_aprobador_id');
    }

    /**
     * Relación con Farmaco a través de Lote
     */
    public function farmaco()
    {
        return $this->hasOneThrough(Farmaco::class, Lote::class, 'id', 'id', 'lote_id', 'farmaco_id');
    }
}
