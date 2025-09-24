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
        'limite_credito'
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2'
    ];

    // Relaciones
    public function detallesCredito()
    {
        return $this->hasMany(DetalleCredito::class);
    }

    public function cuentasPorCobrar()
    {
        return $this->hasMany(CuentaPorCobrar::class);
    }
    public function creditos() {
    return $this->hasMany(Credito::class);
}

    // Métodos calculados
    public function getTotalPendienteAttribute()
    {
        return $this->detallesCredito()
                   ->pendientes()
                   ->get()
                   ->sum('monto_pendiente');
    }

    public function getCreditoDisponibleAttribute()
    {
        return $this->limite_credito - $this->total_pendiente;
    }

    public function getCreditosAgrupadosAttribute()
    {
        return $this->detallesCredito()
                   ->pendientes()
                   ->with('producto')
                   ->get()
                   ->groupBy('numero_credito');
    }
    
}