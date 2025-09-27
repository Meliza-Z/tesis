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
        $detalles = DetalleCredito::with(['producto', 'credito.cliente'])->get();

        $detallesPorFecha = $detalles->map(function ($d) {
            $fecha = optional($d->credito)->fecha_credito?->toDateString() ?? $d->created_at->toDateString();
            return (object) [
                'id' => $d->id,
                'fecha' => $fecha,
                'cliente' => optional(optional($d->credito)->cliente)->nombre ?? 'N/D',
                'producto' => optional($d->producto)->nombre ?? 'N/D',
                'cantidad' => $d->cantidad,
                'precio_unitario' => $d->precio_unitario,
            ];
        })->groupBy('fecha');

        return view('detallescredito.index', compact('detallesPorFecha'));
    }

    public function create()
    {
        $creditos = \App\Models\Credito::with('cliente')->get();
        $productos = Producto::all();
        return view('detallescredito.create', compact('creditos', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'credito_id' => 'required|exists:creditos,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        $credito = \App\Models\Credito::with('cliente')->findOrFail($request->credito_id);

        // Validación de límite de crédito (centavos)
        $actualTotalCentavos = $credito->monto_total_centavos;
        $adicionalCentavos = 0;
        foreach ($request->productos as $detalle) {
            $adicionalCentavos += (int) round(((float) $detalle['precio_unitario']) * 100) * (int) $detalle['cantidad'];
        }
        $expuestoCentavos = $credito->cliente->total_pendiente_centavos;
        $limiteCentavos = (int) ($credito->cliente->limite_credito_centavos ?? 0);
        if ($expuestoCentavos - $actualTotalCentavos + ($actualTotalCentavos + $adicionalCentavos) > $limiteCentavos) {
            return back()->withErrors(['credito_id' => 'El crédito excede el límite disponible del cliente.'])->withInput();
        }

        foreach ($request->productos as $detalle) {
            DetalleCredito::create([
                'credito_id' => $credito->id,
                'producto_id' => $detalle['producto_id'],
                'cantidad' => (int) $detalle['cantidad'],
                'precio_unitario_centavos' => (int) round(((float) $detalle['precio_unitario']) * 100),
                'subtotal_centavos' => (int) round(((float) $detalle['precio_unitario']) * 100) * (int) $detalle['cantidad'],
                'observaciones' => $request->observaciones,
            ]);
        }

        // Recalcular estado tras agregar detalles
        $credito->refresh();
        $credito->recalcularEstado();

        return redirect()->route('detalle_credito.index')->with('success', 'Detalles agregados al crédito correctamente.');
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
