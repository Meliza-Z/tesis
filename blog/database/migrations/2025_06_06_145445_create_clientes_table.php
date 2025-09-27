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
    Schema::create('clientes', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('cedula')->unique()->nullable();
        $table->string('direccion')->nullable();
        $table->string('telefono')->nullable();
        $table->string('email')->nullable();
        // Límite de crédito en centavos (sin decimales)
        $table->unsignedBigInteger('limite_credito_centavos')->nullable();
        $table->timestamps();
        
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
