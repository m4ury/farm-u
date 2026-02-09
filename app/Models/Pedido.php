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
        'estado'
    ];

    protected $casts = [
        'fecha_pedido' => 'datetime:Y-m-d',
    ];

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
                    ->withPivot('cantidad_pedida')
                    ->withTimestamps();
    }
}
