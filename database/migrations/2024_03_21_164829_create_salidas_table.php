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
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_salida')->nullable();
            $table->integer('cantidad_salida')->unsigned()->nullable()->default(0);
            $table->string('numero_dau', 100)->nullable();
            $table->foreignId('user_id')->constrained();
            //$table->foreignId('farmaco_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
