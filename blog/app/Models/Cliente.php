<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cedula',
        'direccion',
        'telefono',
        'email',
        'limite_credito_centavos'
    ];

    protected $casts = [
        'limite_credito_centavos' => 'integer',
    ];

    // Relaciones
    public function cuentasPorCobrar()
    {
        return $this->hasMany(CuentaPorCobrar::class);
    }
    public function creditos() {
    return $this->hasMany(Credito::class);
}
    
    // Compatibilidad y utilidades en centavos
    public function getLimiteCreditoAttribute(): float
    {
        return ($this->limite_credito_centavos ?? 0) / 100;
    }

    public function setLimiteCreditoAttribute($value): void
    {
        $this->attributes['limite_credito_centavos'] = (int) round(((float) $value) * 100);
    }

    public function getTotalPendienteCentavosAttribute(): int
    {
        return (int) $this->creditos->sum(fn ($c) => $c->saldo_pendiente_centavos);
    }

    public function getTotalPendienteAttribute(): float
    {
        return $this->total_pendiente_centavos / 100;
    }

    public function getCreditoDisponibleCentavosAttribute(): int
    {
        return max(0, (int) ($this->limite_credito_centavos ?? 0) - $this->total_pendiente_centavos);
    }

    public function getCreditoDisponibleAttribute(): float
    {
        return $this->credito_disponible_centavos / 100;
    }
}
