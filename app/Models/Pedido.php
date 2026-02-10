<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_pedido',
        'user_id',
        'area_id',
        'solicitante',
        'observaciones',
        'estado',
        'user_aprobador_id',
        'fecha_aprobacion',
        'motivo_rechazo'
    ];

    protected $casts = [
        'fecha_pedido' => 'datetime:Y-m-d',
        'fecha_aprobacion' => 'datetime',
    ];

    /**
     * Estados: pendiente, aprobado, parcial, rechazado, completado
     */
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_APROBADO = 'aprobado';
    const ESTADO_PARCIAL = 'parcial';
    const ESTADO_RECHAZADO = 'rechazado';
    const ESTADO_COMPLETADO = 'completado';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function farmacos()
    {
        return $this->belongsToMany(Farmaco::class, 'farmaco_pedido')
                    ->withPivot('cantidad_pedida', 'cantidad_aprobada', 'cantidad_despachada')
                    ->withTimestamps();
    }

    /**
     * Relación con Usuario que aprobó
     */
    public function usuarioAprobador()
    {
        return $this->belongsTo(User::class, 'user_aprobador_id');
    }

    /**
     * Relación con Despachos (reposiciones realizadas)
     */
    public function despachos()
    {
        return $this->hasMany(Despacho::class);
    }

    /**
     * Verificar si el pedido está pendiente
     */
    public function estaPendiente()
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    /**
     * Verificar si el pedido está aprobado
     */
    public function estaAprobado()
    {
        return $this->estado === self::ESTADO_APROBADO;
    }

    /**
     * Verificar si el pedido está rechazado
     */
    public function estaRechazado()
    {
        return $this->estado === self::ESTADO_RECHAZADO;
    }

    /**
     * Obtener total de cantidad pedida de todos los fármacos
     */
    public function getTotalPedido()
    {
        return $this->farmacos()->sum('cantidad_pedida');
    }

    /**
     * Obtener total de cantidad aprobada
     */
    public function getTotalAprobado()
    {
        return $this->farmacos()->sum('cantidad_aprobada');
    }

    /**
     * Obtener total de cantidad despachada
     */
    public function getTotalDespachado()
    {
        return $this->farmacos()->sum('cantidad_despachada');
    }
}
