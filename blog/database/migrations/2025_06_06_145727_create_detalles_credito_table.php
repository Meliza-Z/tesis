// Nueva migración: add_credit_fields_to_detalle_credito_table
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('detalle_credito', function (Blueprint $table) {
            // Eliminar la referencia a creditos si aún existe
            $table->dropForeign(['credito_id']);
            $table->dropColumn('credito_id');
            
            // Agregar nuevos campos
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('numero_credito')->after('id'); // Ej: CRED-001, CRED-002
            $table->date('fecha_vencimiento');
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'pagado_parcial', 'pagado', 'vencido'])->default('pendiente');
            $table->text('observaciones')->nullable();
        });
    }

    public function down()
    {
        Schema::table('detalle_credito', function (Blueprint $table) {
            $table->dropColumn(['cliente_id', 'numero_credito', 'fecha_vencimiento', 'monto_pagado', 'estado', 'observaciones']);
        });
    }
    // app/Models/DetalleCredito.php
public function cliente()
{
    return $this->belongsTo(Cliente::class);
}

};
