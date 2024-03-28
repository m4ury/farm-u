<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Salida extends Model
{
    use HasFactory;
    protected $fillable = ['fecha_salida', 'cantidad_salida','numero_dau', 'farmaco_id', 'user_id'];

    public function farmacos(){
        return $this->belongsToMany(Farmaco::class)->withTimestamps();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
