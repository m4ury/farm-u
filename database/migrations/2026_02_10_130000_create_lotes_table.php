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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmaco_id')->constrained()->onDelete('cascade');
            $table->string('num_serie', 100)->unique();
            $table->date('fecha_vencimiento');
            $table->integer('cantidad')->unsigned()->default(0);
            $table->integer('cantidad_disponible')->unsigned()->default(0); // cantidad - consumido
            $table->boolean('vencido')->default(false);
            $table->timestamps();
            
            $table->index('farmaco_id');
            $table->index('fecha_vencimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
