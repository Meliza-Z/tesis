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
        'monto_total',
        'saldo_pendiente',
        'estado'
    ];

    protected $casts = [
        'fecha_credito' => 'date',
        'fecha_vencimiento' => 'date',
        'monto_total' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
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
        return $this->fecha_vencimiento->isPast() && $this->estado !== 'pagado';
    }

    /**
     * Accessor para obtener los días de plazo
     */
    public function getDiasPlazoAttribute()
    {
        return $this->fecha_credito->diffInDays($this->fecha_vencimiento);
    }

    /**
     * Accessor para obtener el porcentaje pagado
     */
    public function getPorcentajePagadoAttribute()
    {
        if ($this->monto_total <= 0) {
            return 0;
        }
        
        $montoPagado = $this->monto_total - $this->saldo_pendiente;
        return ($montoPagado / $this->monto_total) * 100;
    }

    /**
     * Accessor para obtener el monto pagado
     */
    public function getMontoPagadoAttribute()
    {
        return $this->monto_total - $this->saldo_pendiente;
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
        return $query->where('fecha_vencimiento', '<', now())
                    ->where('estado', '!=', 'pagado');
    }

    /**
     * Método para actualizar el monto total basado en los detalles
     */
    public function actualizarMontoTotal()
    {
        $nuevoMontoTotal = $this->detalles()->sum('subtotal');
        
        // Si es un crédito nuevo, actualizar también el saldo pendiente
        if ($this->saldo_pendiente == $this->monto_total) {
            $this->saldo_pendiente = $nuevoMontoTotal;
        }
        
        $this->monto_total = $nuevoMontoTotal;
        $this->save();
    }

    /**
     * Método para recalcular el estado basado en el saldo
     */
    public function recalcularEstado()
    {
        if ($this->saldo_pendiente <= 0) {
            $this->estado = 'pagado';
        } else {
            $this->estado = 'pendiente';
        }
        $this->save();
    }
}