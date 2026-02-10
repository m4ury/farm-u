<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historico_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmaco_id')->constrained('farmacos')->onDelete('cascade');
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->onDelete('set null');
            $table->foreignId('area_id')->nullable()->constrained('areas')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['entrada', 'salida', 'despacho', 'recepcion', 'devolucion', 'ajuste']);
            $table->integer('cantidad');
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
            
            $table->index('farmaco_id');
            $table->index('area_id');
            $table->index('tipo');
            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_movimientos');
    }
};
