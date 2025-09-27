<?php

namespace App\Http\Controllers;

use App\Models\Credito;
use App\Models\DetalleCredito;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReporteController extends Controller
{
    // Método para mostrar el reporte por cliente en HTML (vista previa)
    public function vistaReportePorCliente($credito_id)
    {
        $credito = Credito::with(['cliente', 'detalles.producto', 'pagos'])
                    ->findOrFail($credito_id);

        // Calcular totales con la nueva lógica en centavos
        $datos = $this->calcularTotalesCredito($credito);

        // Retornar vista HTML
        return view('reportes.vista_reporte_cliente', compact('credito') + $datos);
    }

    // Método para descargar el reporte por cliente como PDF
    public function descargarReportePorCliente($credito_id)
    {
        $credito = Credito::with(['cliente', 'detalles.producto', 'pagos'])
                    ->findOrFail($credito_id);

        // Calcular totales con la nueva lógica en centavos
        $datos = $this->calcularTotalesCredito($credito);

        // Generar PDF
        $pdf = Pdf::loadView('reportes.reporte_cliente', compact('credito') + $datos);

        return $pdf->download('reporte_cliente_'.$credito->cliente->nombre.'.pdf');
    }

    // Método para reporte diario
    public function reportePorDia(Request $request)
    {
        // Validar que se envió una fecha
        $request->validate([
            'fecha' => 'required|date'
        ]);

        $fecha = Carbon::parse($request->input('fecha'))->toDateString();

        // Obtener detalles de crédito de la fecha seleccionada usando el nuevo esquema
        $detalles = DetalleCredito::with(['producto', 'credito.cliente'])
            ->whereDate('created_at', $fecha)
            ->orderBy(DB::raw("(select nombre from clientes where clientes.id = (select cliente_id from creditos where creditos.id = detalle_creditos.credito_id))"))
            ->orderBy(DB::raw("(select nombre from productos where productos.id = detalle_creditos.producto_id)"))
            ->get()
            ->map(function ($d) {
                return (object) [
                    'id' => $d->id,
                    'cantidad' => $d->cantidad,
                    'precio_unitario' => $d->precio_unitario, // accessor en decimales
                    'subtotal' => $d->subtotal, // accessor en decimales
                    'created_at' => $d->created_at,
                    'cliente_nombre' => optional(optional($d->credito)->cliente)->nombre ?? 'N/D',
                    'producto_nombre' => optional($d->producto)->nombre ?? 'N/D',
                    'fecha_credito' => optional($d->credito)->fecha_credito,
                    'credito_estado' => optional($d->credito)->estado,
                ];
            });

        // Calcular totales
        $totalDelDia = $detalles->sum('subtotal');
        $totalTransacciones = $detalles->count();
        $clientesUnicos = $detalles->pluck('cliente_nombre')->unique()->count();

        // Agrupar por cliente para mejor presentación
        $detallesPorCliente = $detalles->groupBy('cliente_nombre');

        // Generar PDF
        $pdf = Pdf::loadView('reportes.reporte_diario_detalles', [
            'detallesPorFecha' => $detalles,
            'detallesPorCliente' => $detallesPorCliente,
            'fecha' => $fecha,
            'totalDelDia' => $totalDelDia,
            'totalTransacciones' => $totalTransacciones,
            'clientesUnicos' => $clientesUnicos,
        ]);

        return $pdf->download('reporte_diario_detalles_'.$fecha.'.pdf');
    }

    // Método para debugging/verificación de datos
    public function verificarDatos()
    {
        $creditos = Credito::with(['cliente', 'detalles.producto', 'pagos'])->get();
        
        return view('reportes.verificar_datos', compact('creditos'));
    }

    // Método privado para evitar duplicación de código
    private function calcularTotalesCredito($credito)
    {
        $totalProductos = $credito->monto_total; // derivado (decimales)
        $totalPagado = $credito->total_pagado_centavos / 100;
        $saldoPendiente = $credito->saldo_pendiente; // derivado (decimales)
        $totalCredito = $totalProductos;

        return compact('totalProductos', 'totalPagado', 'saldoPendiente', 'totalCredito');
    }
}
