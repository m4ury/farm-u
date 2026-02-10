<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecepcionDespacho extends Model
{
    use HasFactory;

    protected $table = 'recepciones_despachos';

    protected $fillable = [
        'despacho_id',
        'user_id',
        'cantidad_recibida',
        'observaciones',
        'fecha_recepcion'
    ];

    protected $casts = [
        'fecha_recepcion' => 'datetime',
    ];

    /**
     * Relación con Despacho
     */
    public function despacho()
    {
        return $this->belongsTo(Despacho::class);
    }

    /**
     * Relación con User (quien recibió)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Acceso a farmaco a través de despacho -> lote
     */
    public function getFarmaco()
    {
        return $this->despacho->farmaco();
    }
}
