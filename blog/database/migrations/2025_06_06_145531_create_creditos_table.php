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
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('codigo')->unique(); // código único del crédito
            $table->date('fecha_credito'); // fecha de creación del crédito
            $table->unsignedSmallInteger('plazo_dias')->default(15); // plazo por defecto en días
            $table->date('fecha_vencimiento'); // fecha de vencimiento inicial
            $table->date('fecha_vencimiento_ext')->nullable(); // extensión de vencimiento
            // Totales y saldos se derivan de detalle_creditos y pagos (no se persisten aquí)
            $table->enum('estado', ['pendiente', 'activo', 'vencido', 'pagado'])->default('pendiente'); // estado del crédito
            // Índices útiles para consultas
            $table->index(['cliente_id', 'estado', 'fecha_vencimiento']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditos');
    }
};
