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
    Schema::create('pagos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('credito_id')->constrained('creditos')->onDelete('cascade');
        $table->date('fecha_pago');
        // Monto pagado en centavos (sin decimales)
        $table->unsignedBigInteger('monto_pagado_centavos');
        $table->string('metodo_pago')->nullable();
        // Índices para consultas por crédito y fecha
        $table->index(['credito_id', 'fecha_pago']);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
