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
        Schema::table('pedidos', function (Blueprint $table) {
            // Modificar el enum estado para incluir nuevos estados
            // Se comenta por ahora ya que cambiar enum es complejo en MySQL
            // Estados ahora: pendiente, aprobado, parcial, rechazado, completado
            
            if (!Schema::hasColumn('pedidos', 'user_aprobador_id')) {
                $table->foreignId('user_aprobador_id')->nullable()->constrained('users')->onDelete('set null')->after('user_id');
            }
            
            if (!Schema::hasColumn('pedidos', 'fecha_aprobacion')) {
                $table->timestamp('fecha_aprobacion')->nullable()->after('fecha_pedido');
            }
            
            if (!Schema::hasColumn('pedidos', 'motivo_rechazo')) {
                $table->text('motivo_rechazo')->nullable()->after('observaciones');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'user_aprobador_id')) {
                $table->dropForeign(['user_aprobador_id']);
                $table->dropColumn('user_aprobador_id');
            }
            if (Schema::hasColumn('pedidos', 'fecha_aprobacion')) {
                $table->dropColumn('fecha_aprobacion');
            }
            if (Schema::hasColumn('pedidos', 'motivo_rechazo')) {
                $table->dropColumn('motivo_rechazo');
            }
        });
    }
};
