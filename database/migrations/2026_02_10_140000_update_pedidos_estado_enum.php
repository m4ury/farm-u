<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solución: crear columna temporal, mapear datos y reemplazar
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('estado_nuevo')->nullable()->after('estado');
        });

        // Mapear valores antiguos a nuevos
        DB::statement("UPDATE pedidos SET estado_nuevo = 'pendiente' WHERE estado = 'solicitado'");
        DB::statement("UPDATE pedidos SET estado_nuevo = 'rechazado' WHERE estado = 'rechazado'");
        DB::statement("UPDATE pedidos SET estado_nuevo = 'completado' WHERE estado = 'entregado'");
        DB::statement("UPDATE pedidos SET estado_nuevo = IFNULL(estado_nuevo, 'pendiente') WHERE estado_nuevo IS NULL");

        // Eliminar columna antigua y renombrar la nueva
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->renameColumn('estado_nuevo', 'estado');
        });

        // Asegurar que la columna tenga un valor por defecto
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('estado')->default('pendiente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('estado_old')->nullable();
        });

        DB::statement("UPDATE pedidos SET estado_old = 'solicitado' WHERE estado = 'pendiente'");
        DB::statement("UPDATE pedidos SET estado_old = 'rechazado' WHERE estado = 'rechazado'");
        DB::statement("UPDATE pedidos SET estado_old = 'entregado' WHERE estado = 'completado'");

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->renameColumn('estado_old', 'estado');
        });
    }
};
