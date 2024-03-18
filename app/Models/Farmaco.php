<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmaco extends Model
{
    protected $fillable = ['descripcion', 'dosis', 'forma_farmaceutica', 'stock_maximo', 'stock_fisico', 'controlado', 'fecha_vencimiento'];

    use HasFactory;
}
