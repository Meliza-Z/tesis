<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleCredito;
use App\Models\Producto;
use Illuminate\Http\Request;

class DetallesCreditoController extends Controller
{
    public function index(Request $request)
    {
        $detalles = DetalleCredito::with(['producto', 'credito.cliente'])->get();

        $detallesPorFecha = $detalles->map(function ($d) {
            // Usar siempre la fecha de creación del detalle
            $fecha = $d->created_at->toDateString();

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
        $creditos = \App\Models\Credito::with('cliente')
            ->whereIn('estado', ['activo'])
            ->get();
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
        $cliente = Cliente::with(['detallesCredito' => function ($query) {
            $query->pendientes()->with('producto')->orderBy('fecha_vencimiento', 'asc');
        }])->findOrFail($clienteId);

        return view('detallescredito.por-cliente', compact('cliente'));
    }

    public function edit(\App\Models\DetalleCredito $detalle_credito)
    {
        $productos = Producto::all();

        return view('detallescredito.edit', compact('detalle_credito', 'productos'));
    }

    public function update(Request $request, \App\Models\DetalleCredito $detalle_credito)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        // Validación de límite de crédito del cliente (centrado en delta del detalle)
        $credito = $detalle_credito->credito()->with('cliente.creditos')->first();
        if ($credito && $credito->cliente) {
            $cliente = $credito->cliente;
            $limiteCentavos = (int) ($cliente->limite_credito_centavos ?? 0);

            $oldSubtotalCent = (int) ($detalle_credito->subtotal_centavos ?? 0);
            $newSubtotalCent = (int) round(((float) $request->input('precio_unitario')) * 100) * (int) $request->integer('cantidad');

            // Exposición actual del cliente (suma de saldos pendientes de todos sus créditos)
            $expuestoCentavos = (int) $cliente->creditos->sum(fn ($c) => $c->saldo_pendiente_centavos);
            $nuevoExpuesto = $expuestoCentavos + ($newSubtotalCent - $oldSubtotalCent);

            if ($limiteCentavos > 0 && $nuevoExpuesto > $limiteCentavos) {
                return back()->withErrors(['cantidad' => 'La actualización excede el límite de crédito del cliente.'])->withInput();
            }
        }

        // Actualización del detalle
        $detalle_credito->producto_id = $request->integer('producto_id');
        $detalle_credito->cantidad = $request->integer('cantidad');
        $detalle_credito->precio_unitario = $request->input('precio_unitario'); // mutator a *_centavos
        $detalle_credito->subtotal_centavos = (int) round($request->input('precio_unitario') * 100) * $detalle_credito->cantidad;
        $detalle_credito->observaciones = $request->input('observaciones');
        $detalle_credito->save();

        // Recalcular estado del crédito
        if ($detalle_credito->credito) {
            $detalle_credito->credito->refresh();
            $detalle_credito->credito->recalcularEstado();
        }

        return redirect()->route('detalle_credito.index')->with('success', 'Detalle actualizado correctamente.');
    }

    public function destroy(\App\Models\DetalleCredito $detalle_credito)
    {
        $credito = $detalle_credito->credito;
        $detalle_credito->delete();
        if ($credito) {
            $credito->refresh();
            $credito->recalcularEstado();
        }

        return redirect()->route('detalle_credito.index')->with('success', 'Detalle eliminado correctamente.');
    }
}
