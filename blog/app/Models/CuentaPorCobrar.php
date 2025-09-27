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
        'monto_adeudado_centavos',
        'saldo_pendiente_centavos',
        'fecha_vencimiento',
        'estado',
    ];

    protected $casts = [
        'monto_adeudado_centavos' => 'integer',
        'saldo_pendiente_centavos' => 'integer',
        'fecha_vencimiento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    // Compatibilidad con API previa (decimales)
    public function getMontoAdeudadoAttribute(): float
    {
        return ($this->monto_adeudado_centavos ?? 0) / 100;
    }

    public function getSaldoPendienteAttribute(): float
    {
        return ($this->saldo_pendiente_centavos ?? 0) / 100;
    }

}
