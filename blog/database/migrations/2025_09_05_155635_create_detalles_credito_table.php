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
        Schema::create('detalle_creditos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credito_id')->constrained('creditos')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad');
            // Valores en centavos (sin decimales)
            $table->unsignedInteger('precio_unitario_centavos');
            $table->unsignedBigInteger('subtotal_centavos');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['credito_id']);
            $table->index(['producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_creditos');
    }
};
