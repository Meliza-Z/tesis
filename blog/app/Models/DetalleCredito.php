<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCredito extends Model
{
    use HasFactory;

    protected $table = 'detalle_creditos';

    protected $fillable = [
        'credito_id',
        'producto_id',
        'cantidad',
        'precio_unitario_centavos',
        'subtotal_centavos',
        'observaciones'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario_centavos' => 'integer',
        'subtotal_centavos' => 'integer',
    ];

    /**
     * Relación con el crédito
     */
    public function credito()
    {
        return $this->belongsTo(Credito::class);
    }

    /**
     * Relación con el producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Compatibilidad y helpers en decimales
    public function getPrecioUnitarioAttribute(): float
    {
        return ($this->precio_unitario_centavos ?? 0) / 100;
    }

    public function setPrecioUnitarioAttribute($value): void
    {
        $this->attributes['precio_unitario_centavos'] = (int) round(((float) $value) * 100);
        $this->attributes['subtotal_centavos'] = (int) (($this->attributes['cantidad'] ?? 1) * $this->attributes['precio_unitario_centavos']);
    }

    public function getSubtotalAttribute(): float
    {
        return ($this->subtotal_centavos ?? 0) / 100;
    }

    public function getSubtotalFormateadoAttribute(): string
    {
        return number_format($this->subtotal, 2);
    }

    public function getPrecioUnitarioFormateadoAttribute(): string
    {
        return number_format($this->precio_unitario, 2);
    }
}
