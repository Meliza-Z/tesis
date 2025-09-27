<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Credito extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'codigo',
        'fecha_credito',
        'fecha_vencimiento',
        'plazo_dias',
        'fecha_vencimiento_ext',
        'estado'
    ];

    protected $casts = [
        'fecha_credito' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_vencimiento_ext' => 'date',
        'plazo_dias' => 'integer',
    ];

    /**
     * Relación con el cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con los detalles del crédito (productos)
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCredito::class);
    }

    /**
     * Relación con los pagos (si existe el modelo)
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Accessor para determinar si el crédito está vencido
     */
    public function getEstaVencidoAttribute()
    {
        $vence = $this->fecha_vencimiento_ext ?? $this->fecha_vencimiento;
        return $vence->isPast() && $this->estado !== 'pagado';
    }

    /**
     * Accessor para obtener los días de plazo
     */
    public function getDiasPlazoAttribute()
    {
        return $this->plazo_dias ?? $this->fecha_credito->diffInDays($this->fecha_vencimiento);
    }

    // Derivados en centavos
    public function getMontoTotalCentavosAttribute(): int
    {
        return (int) $this->detalles()->sum('subtotal_centavos');
    }

    public function getTotalPagadoCentavosAttribute(): int
    {
        return (int) $this->pagos()->sum('monto_pagado_centavos');
    }

    public function getSaldoPendienteCentavosAttribute(): int
    {
        return max(0, $this->monto_total_centavos - $this->total_pagado_centavos);
    }

    // Compatibilidad con API previa en decimales
    public function getMontoTotalAttribute(): float
    {
        return $this->monto_total_centavos / 100;
    }

    public function getSaldoPendienteAttribute(): float
    {
        return $this->saldo_pendiente_centavos / 100;
    }

    public function getPorcentajePagadoAttribute(): float
    {
        $total = $this->monto_total_centavos;
        if ($total <= 0) return 0.0;
        return 100.0 * ($this->total_pagado_centavos / $total);
    }

    /**
     * Scope para créditos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para créditos pagados
     */
    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    /**
     * Scope para créditos vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->whereRaw('COALESCE(fecha_vencimiento_ext, fecha_vencimiento) < ?', [now()])
                     ->where('estado', '!=', 'pagado');
    }

    /**
     * Método para actualizar el monto total basado en los detalles
     */
    public function actualizarMontoTotal(): void {}

    /**
     * Método para recalcular el estado basado en el saldo
     */
    public function recalcularEstado(): void
    {
        $vence = $this->fecha_vencimiento_ext ?? $this->fecha_vencimiento;
        $saldo = $this->saldo_pendiente_centavos;
        $this->estado = $saldo <= 0 ? 'pagado' : ($vence->isPast() ? 'vencido' : 'activo');
        $this->save();
    }
}
