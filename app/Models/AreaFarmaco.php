<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
//use Illuminate\Database\Eloquent\Model;

class AreaFarmaco extends Pivot
{
    use HasFactory;
    public $table   = 'area_farmaco';

    protected $fillable = ['farmaco_id', 'area_id', 'stock_minimo'];
}
