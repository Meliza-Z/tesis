<?php

namespace App\Http\Controllers;

use App\Models\DetalleCredito;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DetallesCreditoController extends Controller
{
    public function index(Request $request)
    {
        // Obtener todos los clientes con sus créditos pendientes
        $clientes = Cliente::with(['detallesCredito' => function($query) {
            $query->pendientes()
                  ->with('producto')
                  ->orderBy('fecha_vencimiento', 'asc');
        }])
        ->whereHas('detallesCredito', function($query) {
            $query->pendientes();
        })
        ->get();

        return view('detallescredito.index', compact('clientes'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $productos = Producto::all();
        return view('detallescredito.create', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date|after:today',
        ]);

        $cliente = Cliente::find($request->cliente_id);
        $numeroCredito = 'CRED-' . str_pad($cliente->detallesCredito()->count() + 1, 3, '0', STR_PAD_LEFT);

        foreach ($request->productos as $detalle) {
            $producto = Producto::find($detalle['producto_id']);

            // Verificar stock
            if ($producto->stock < $detalle['cantidad']) {
                return redirect()->back()->with('error', 'Stock insuficiente para el producto: ' . $producto->nombre);
            }

            // Verificar límite de crédito
            $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
            if (($cliente->total_pendiente + $subtotal) > $cliente->limite_credito) {
                return redirect()->back()->with('error', 'El crédito excede el límite disponible del cliente.');
            }

            // Actualizar stock
            $producto->stock -= $detalle['cantidad'];
            $producto->save();

            // Crear detalle de crédito
            DetalleCredito::create([
                'cliente_id' => $request->cliente_id,
                'numero_credito' => $numeroCredito,
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'subtotal' => $subtotal,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'observaciones' => $request->observaciones
            ]);
        }

        return redirect()->route('detalle_credito.index')->with('success', 'Crédito registrado correctamente.');
    }

    // Método para mostrar créditos de un cliente específico
    public function porCliente($clienteId)
    {
        $cliente = Cliente::with(['detallesCredito' => function($query) {
            $query->pendientes()->with('producto')->orderBy('fecha_vencimiento', 'asc');
        }])->findOrFail($clienteId);

        return view('detallescredito.por-cliente', compact('cliente'));
    }
}