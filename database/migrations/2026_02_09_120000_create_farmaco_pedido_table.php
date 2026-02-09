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
        Schema::create('farmaco_pedido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmaco_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pedido_id')->constrained()->cascadeOnDelete();
            $table->integer('cantidad_pedida');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmaco_pedido');
    }
};
