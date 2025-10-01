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

        // 1. Obtener créditos creados en la fecha seleccionada
        $creditosDelDia = Credito::with(['cliente', 'detalles.producto'])
            ->whereDate('fecha_credito', $fecha)
            ->get();

        // 2. Obtener pagos realizados en la fecha seleccionada
        $pagosDelDia = \App\Models\Pago::with(['credito.cliente'])
            ->whereDate('fecha_pago', $fecha)
            ->get();

        // 3. Preparar datos de créditos nuevos
        $creditosData = $creditosDelDia->map(function($credito) {
            return [
                'tipo' => 'credito',
                'cliente_nombre' => $credito->cliente->nombre ?? 'N/D',
                'credito_codigo' => $credito->codigo,
                'monto' => $credito->monto_total,
                'fecha' => $credito->fecha_credito,
                'detalles' => $credito->detalles->map(function($detalle) {
                    return [
                        'producto' => $detalle->producto->nombre ?? 'N/D',
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'subtotal' => $detalle->subtotal,
                    ];
                }),
            ];
        });

        // 4. Preparar datos de pagos
        $pagosData = $pagosDelDia->map(function($pago) {
            return [
                'tipo' => 'pago',
                'cliente_nombre' => optional($pago->credito->cliente)->nombre ?? 'N/D',
                'credito_codigo' => optional($pago->credito)->codigo ?? 'N/D',
                'monto' => $pago->monto_pago,
                'fecha' => $pago->fecha_pago,
                'metodo_pago' => $pago->metodo_pago ?? 'N/D',
            ];
        });

        // 5. Calcular totales
        $totalCreditosDelDia = $creditosDelDia->sum('monto_total');
        $totalPagosDelDia = $pagosDelDia->sum('monto_pago');
        $totalTransacciones = $creditosDelDia->count() + $pagosDelDia->count();
        $clientesUnicos = collect([
            ...$creditosDelDia->pluck('cliente.nombre'),
            ...$pagosDelDia->pluck('credito.cliente.nombre')
        ])->filter()->unique()->count();

        // Generar PDF
        $pdf = Pdf::loadView('reportes.reporte_diario_detalles', [
            'creditosData' => $creditosData,
            'pagosData' => $pagosData,
            'fecha' => $fecha,
            'totalCreditosDelDia' => $totalCreditosDelDia,
            'totalPagosDelDia' => $totalPagosDelDia,
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
        // Usar los accessors del modelo que calculan correctamente desde centavos
        $totalProductos = $credito->monto_total; // accessor que divide centavos/100
        $totalPagado = $credito->pagos->sum('monto_pago'); // suma directa de pagos (ya en decimales)
        $saldoPendiente = $credito->saldo_pendiente; // accessor que calcula diferencia
        $totalCredito = $totalProductos;

        return compact('totalProductos', 'totalPagado', 'saldoPendiente', 'totalCredito');
    }
}
