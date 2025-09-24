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
        'monto_total',
        'descripcion',
        'detalles',
    ];

    protected $casts = [
        'fecha_reporte' => 'date',
        'detalles' => 'array', // convierte el JSON automáticamente a un array de PHP
    ];
}
