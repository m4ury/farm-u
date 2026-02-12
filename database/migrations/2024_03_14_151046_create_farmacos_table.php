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
        Schema::create('farmacos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 100);
            $table->string('dosis',100);
            $table->string('forma_farmaceutica', 100)->nullable();
            $table->integer('stock_minimo')->unsigned()->nullable()->default(0);
            $table->boolean('controlado')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmacos');
    }
};
