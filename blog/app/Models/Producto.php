<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'categoria', // Por ahora usamos string, luego migraremos a categoria_id
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    /**
     * Obtener el color de la categoría para UI
     */
    public function getCategoriaColorAttribute()
    {
        $colores = [
            'Abarrotes Secos' => '#3B82F6',
            'Productos Enlatados y Conservas' => '#10B981',
            'Lácteos y Refrigerados' => '#8B5CF6',
            'Bebidas' => '#F59E0B',
            'Botanas y Confitería' => '#EF4444',
            'Cuidado Personal' => '#EC4899',
            'Limpieza del Hogar' => '#06B6D4',
            'Alimentos para Mascotas' => '#84CC16',
            'Verduras y Frutas Frescas' => '#22C55E',
            'Pan y Postres' => '#F97316'
        ];

        return $colores[$this->categoria] ?? '#6B7280';
    }

    /**
     * Obtener el icono de la categoría para UI
     */
    public function getCategoriaIconoAttribute()
    {
        $iconos = [
            'Abarrotes Secos' => 'fas fa-boxes',
            'Productos Enlatados y Conservas' => 'fas fa-archive',
            'Lácteos y Refrigerados' => 'fas fa-temperature-low',
            'Bebidas' => 'fas fa-glass-whiskey',
            'Botanas y Confitería' => 'fas fa-candy-cane',
            'Cuidado Personal' => 'fas fa-user-circle',
            'Limpieza del Hogar' => 'fas fa-broom',
            'Alimentos para Mascotas' => 'fas fa-paw',
            'Verduras y Frutas Frescas' => 'fas fa-apple-alt',
            'Pan y Postres' => 'fas fa-birthday-cake'
        ];

        return $iconos[$this->categoria] ?? 'fas fa-tag';
    }

    /**
     * Scope para filtrar por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function detallesCredito()
    {
        return $this->hasMany(DetalleCredito::class);
    }
}


