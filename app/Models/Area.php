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
}
