<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_dau',
        'fecha_receta',
        'area_id',
        'user_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_receta' => 'date',
    ];

    /**
     * Relación con Salidas (una receta tiene muchas salidas, una por fármaco)
     */
    public function salidas()
    {
        return $this->hasMany(Salida::class);
    }

    /**
     * Relación con User (quien creó la receta)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Area (área desde donde se despacha)
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Obtener los fármacos de esta receta a través de las salidas
     */
    public function farmacos()
    {
        return Farmaco::whereIn('id', $this->salidas()->pluck('farmaco_id'));
    }

    /**
     * Cantidad total de unidades despachadas en esta receta
     */
    public function totalUnidades()
    {
        return $this->salidas()->sum('cantidad_salida');
    }
}
