<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CuentaPorCobrar extends Model
{
    use HasFactory;

    protected $table = 'cuentas_por_cobrar';

    protected $fillable = [
        'cliente_id',
        'monto_adeudado',
        'saldo_pendiente',
        'fecha_vencimiento',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    // CuentaPorCobrar.php
public function credito()
{
    return $this->belongsTo(Credito::class);
}

}
