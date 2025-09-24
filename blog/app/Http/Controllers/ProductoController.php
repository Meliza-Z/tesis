<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // Definir las categorías disponibles
    public static function getCategorias()
    {
        return [
            'Abarrotes Secos' => 'Abarrotes Secos',
            'Productos Enlatados y Conservas' => 'Productos Enlatados y Conservas',
            'Lácteos y Refrigerados' => 'Lácteos y Refrigerados',
            'Bebidas' => 'Bebidas',
            'Botanas y Confitería' => 'Botanas y Confitería',
            'Cuidado Personal' => 'Cuidado Personal',
            'Limpieza del Hogar' => 'Limpieza del Hogar',
            'Alimentos para Mascotas' => 'Alimentos para Mascotas',
            'Verduras y Frutas Frescas' => 'Verduras y Frutas Frescas',
            'Pan y Postres' => 'Pan y Postres'
        ];
    }

    public function index(Request $request)
    {
        $query = Producto::query();

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $productos = $query->get();
        
        // Agrupar productos por categoría
        $productosPorCategoria = $productos->groupBy('categoria');
        
        $categorias = self::getCategorias();

        return view('productos.index', compact('productos', 'productosPorCategoria', 'categorias'));
    }

    public function create()
    {
        $categorias = self::getCategorias();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|string|in:' . implode(',', array_keys(self::getCategorias())),
            'precio' => 'required|numeric|min:0',
        ]);

        Producto::create($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = self::getCategorias();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|string|in:' . implode(',', array_keys(self::getCategorias())),
            'precio' => 'required|numeric|min:0',
        ]);

        $producto->update($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}