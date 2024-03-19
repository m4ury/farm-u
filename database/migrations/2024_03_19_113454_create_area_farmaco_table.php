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
        Schema::create('area_farmaco', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmaco_id')->constrained()->nullable();
            $table->foreignId('area_id')->constrained()->nullable();

            /* $table->unsignedBiginteger('farmaco_id');
            $table->unsignedBiginteger('area_id');

            $table->foreign('farmaco_id')->references('id')
                 ->on('farmacos')->onDelete('cascade');
            $table->foreign('area_id')->references('id')
                ->on('areas')->onDelete('cascade'); */

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_farmaco');
    }
};
