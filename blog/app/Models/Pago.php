<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'credito_id',
        'fecha_pago',
        'monto_pagado_centavos',
        'metodo_pago',
    ];

    protected $casts = [
        'monto_pagado_centavos' => 'integer',
        'fecha_pago' => 'date',
    ];

    
    public function credito()
{
    return $this->belongsTo(Credito::class);
}

    // Compatibilidad con API previa
    public function getMontoPagoAttribute(): float
    {
        return ($this->monto_pagado_centavos ?? 0) / 100;
    }

    public function setMontoPagoAttribute($value): void
    {
        $this->attributes['monto_pagado_centavos'] = (int) round(((float) $value) * 100);
    }

}
