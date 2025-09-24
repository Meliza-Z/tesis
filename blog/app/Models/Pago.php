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
        'monto_pago',
        'metodo_pago',
        'estado_pago',
    ];

    
    public function credito()
{
    return $this->belongsTo(Credito::class);
}

}

