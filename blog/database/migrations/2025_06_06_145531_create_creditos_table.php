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
            $table->date('fecha_vencimiento'); // fecha de vencimiento del crédito
            $table->decimal('monto_total', 10, 2); // monto total del crédito
            $table->decimal('saldo_pendiente', 10, 2); // saldo que falta por pagar
            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente'); // estado del crédito
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
