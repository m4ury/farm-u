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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_pedido');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('area_id')->constrained();
            $table->string('receptor', 100)->nullable();
            $table->string('solicitante', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['solicitado', 'rechazado', 'entregado'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
