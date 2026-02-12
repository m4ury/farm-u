<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Salida extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha_salida',
        'cantidad_salida',
        'numero_dau',
        'farmaco_id',
        'user_id',
        'stock_actual',
        'receta_id'
    ];

    public function farmacos()
    {
        return $this->belongsToMany(Farmaco::class)->withTimestamps();
    }

    /**
     * Relación con Receta
     */
    public function receta()
    {
        return $this->belongsTo(Receta::class);
    }

    /**
     * Relación directa con Farmaco (para salidas vía receta)
     */
    public function farmaco()
    {
        return $this->belongsTo(Farmaco::class);
    }

    public function lotes()
    {
        return $this->belongsToMany(Lote::class, 'lote_salida')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
