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
        $table->string('tipo_reporte'); // puede ser: ventas_credito, pagos, cuentas_vencidas, estado_general
        $table->integer('cantidad_registros'); // número total de registros incluidos en el reporte
        $table->decimal('monto_total', 12, 2)->default(0); // suma total (ventas o pagos, según reporte)
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
        Schema::dropIfExists('reporte');
    }
};
