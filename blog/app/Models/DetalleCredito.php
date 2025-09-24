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
        'precio_unitario',
        'subtotal',
        'observaciones'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
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

    /**
     * Mutator para calcular subtotal automáticamente
     */
    public function setSubtotalAttribute()
    {
        $this->attributes['subtotal'] = $this->cantidad * $this->precio_unitario;
    }

    /**
     * Accessor para obtener el subtotal formateado
     */
    public function getSubtotalFormateadoAttribute()
    {
        return number_format($this->subtotal, 2);
    }

    /**
     * Accessor para obtener el precio unitario formateado
     */
    public function getPrecioUnitarioFormateadoAttribute()
    {
        return number_format($this->precio_unitario, 2);
    }
}