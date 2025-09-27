<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('reportes', function (Blueprint $table) {
        $table->id();
        $table->date('fecha_reporte'); // fecha en que se generó el reporte
        $table->string('tipo_reporte'); // puede ser: ventas_credito, pagos, cuentas_vencidas, estado_general, etc.
        $table->integer('cantidad_registros'); // número total de registros incluidos en el reporte
        // Monto total en centavos (sin decimales)
        $table->unsignedBigInteger('monto_total_centavos')->default(0);
        $table->text('descripcion')->nullable(); // descripción general del reporte
        $table->json('detalles')->nullable(); // datos adicionales en formato JSON (opcional)
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
