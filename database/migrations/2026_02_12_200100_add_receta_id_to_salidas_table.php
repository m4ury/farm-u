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
        Schema::table('salidas', function (Blueprint $table) {
            $table->foreignId('receta_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->unsignedBigInteger('farmaco_id')->nullable()->after('numero_dau');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas', function (Blueprint $table) {
            $table->dropForeign(['receta_id']);
            $table->dropColumn('receta_id');
            $table->dropColumn('farmaco_id');
        });
    }
};
