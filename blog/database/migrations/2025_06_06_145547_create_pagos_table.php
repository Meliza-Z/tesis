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
        $table->decimal('monto_pago', 10, 2);
        $table->string('metodo_pago')->nullable();
        $table->enum('estado_pago', ['pendiente', 'al_dia', 'pagado'])->default('pendiente');
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
