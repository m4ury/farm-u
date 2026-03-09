<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Añadir stock_minimo a la tabla pivot area_farmaco
        Schema::table('area_farmaco', function (Blueprint $table) {
            $table->integer('stock_minimo')->unsigned()->nullable()->default(0)->after('area_id');
        });

        // Eliminar stock_minimo de la tabla farmacos
        Schema::table('farmacos', function (Blueprint $table) {
            $table->dropColumn('stock_minimo');
        });
    }

    public function down(): void
    {
        // Restaurar stock_minimo en farmacos
        Schema::table('farmacos', function (Blueprint $table) {
            $table->integer('stock_minimo')->unsigned()->nullable()->default(0);
        });

        // Eliminar stock_minimo de area_farmaco
        Schema::table('area_farmaco', function (Blueprint $table) {
            $table->dropColumn('stock_minimo');
        });
    }
};
