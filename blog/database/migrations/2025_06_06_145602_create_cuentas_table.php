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
    Schema::create('cuentas_por_cobrar', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
        // Montos en centavos (sin decimales)
        $table->unsignedBigInteger('monto_adeudado_centavos'); 
        $table->unsignedBigInteger('saldo_pendiente_centavos'); 
        $table->date('fecha_vencimiento'); 
        $table->enum('estado', ['al_dia', 'mora'])->default('al_dia'); 
        $table->dateTime('proximo_recordatorio_at')->nullable();
        $table->index(['cliente_id']);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_por_cobrar');
    }
};
