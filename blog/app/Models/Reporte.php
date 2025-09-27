<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    protected $fillable = [
        'fecha_reporte',
        'tipo_reporte',
        'cantidad_registros',
        'monto_total_centavos',
        'descripcion',
        'detalles',
    ];

    protected $casts = [
        'fecha_reporte' => 'date',
        'detalles' => 'array', // convierte el JSON automáticamente a un array de PHP
        'monto_total_centavos' => 'integer',
    ];

    // Compatibilidad con API previa
    public function getMontoTotalAttribute(): float
    {
        return ($this->monto_total_centavos ?? 0) / 100;
    }
}
