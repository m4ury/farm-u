<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla pivot para trackear stock de lotes por área.
     * Cuando farmacia despacha y el área recepciona, las unidades
     * quedan registradas aquí como disponibles para dispensación (salidas).
     */
    public function up(): void
    {
        Schema::create('lote_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->integer('cantidad_disponible')->default(0);
            $table->timestamps();

            $table->unique(['lote_id', 'area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_area');
    }
};
