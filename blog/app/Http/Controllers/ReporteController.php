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

    /**
     * Reporte de créditos vencidos
     */
    public function creditosVencidos()
    {
        // Obtener todos los créditos vencidos con información del cliente
        $creditosVencidos = Credito::with(['cliente', 'detalles.producto', 'pagos'])
            ->vencidos()  // usando el scope definido en el modelo
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        // Calcular estadísticas usando accessors del modelo
        $totalCreditos = $creditosVencidos->count();
        $montoTotalVencido = $creditosVencidos->sum(function($credito) {
            return $credito->monto_total; // usar el accessor
        });
        $saldoTotalPendiente = $creditosVencidos->sum(function($credito) {
            return $credito->saldo_pendiente; // usar el accessor
        });
        $promedioVencimiento = $totalCreditos > 0 ? $montoTotalVencido / $totalCreditos : 0;

        // Calcular días vencidos para cada crédito
        $creditosConDias = $creditosVencidos->map(function ($credito) {
            $fechaVencimiento = $credito->fecha_vencimiento_ext ?? $credito->fecha_vencimiento;
            $diasVencido = now()->diffInDays($fechaVencimiento);
            $credito->dias_vencido = $diasVencido;
            return $credito;
        });

        // Generar PDF
        $pdf = Pdf::loadView('reportes.creditos_vencidos', [
            'creditosVencidos' => $creditosConDias,
            'totalCreditos' => $totalCreditos,
            'montoTotalVencido' => $montoTotalVencido,
            'saldoTotalPendiente' => $saldoTotalPendiente,
            'promedioVencimiento' => $promedioVencimiento,
            'fechaReporte' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->download('creditos_vencidos_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Reporte de resumen de cartera (tipo cierre de caja)
     */
    public function resumenCartera()
    {
        // Obtener todos los créditos activos
        $creditosActivos = Credito::with(['cliente', 'pagos'])
            ->where('estado', '!=', 'pagado')
            ->get();

        // Calcular estadísticas generales
        $totalCreditos = Credito::count();
        $creditosPagados = Credito::where('estado', 'pagado')->count();
        $creditosPendientes = Credito::where('estado', 'pendiente')->count();
        $creditosVencidos = Credito::vencidos()->count();

        // Montos totales usando accessors y relaciones
        $todosCreditos = Credito::with(['detalles', 'pagos'])->get();
        $montoTotalCartera = $todosCreditos->sum(function($credito) {
            return $credito->monto_total; // usar el accessor
        });
        // Sumar desde la columna real y convertir de centavos a decimales
        $montoTotalPagado = \App\Models\Pago::sum('monto_pagado_centavos') / 100;
        $saldoPendienteTotal = $creditosActivos->sum(function($credito) {
            return $credito->saldo_pendiente; // usar el accessor
        });

        // Estadísticas por estado usando accessors
        $montoPorEstado = [
            'activos' => $todosCreditos->where('estado', 'activo')->sum(function($credito) {
                return $credito->monto_total;
            }),
            'pendientes' => $todosCreditos->where('estado', 'pendiente')->sum(function($credito) {
                return $credito->monto_total;
            }),
            'vencidos' => $todosCreditos->filter(function($credito) {
                $vence = $credito->fecha_vencimiento_ext ?? $credito->fecha_vencimiento;
                return $vence->isPast() && $credito->estado !== 'pagado';
            })->sum(function($credito) {
                return $credito->monto_total;
            }),
            'pagados' => $todosCreditos->where('estado', 'pagado')->sum(function($credito) {
                return $credito->monto_total;
            }),
        ];

        // Top 10 clientes con mayor deuda usando collection
        $creditosPorCliente = $todosCreditos->where('estado', '!=', 'pagado')
            ->groupBy('cliente_id')
            ->map(function($creditos) {
                $deudaTotal = $creditos->sum(function($credito) {
                    return $credito->saldo_pendiente;
                });
                return (object)[
                    'cliente_id' => $creditos->first()->cliente_id,
                    'cliente' => $creditos->first()->cliente,
                    'deuda_total' => $deudaTotal,
                ];
            })
            ->filter(function($cliente) {
                return $cliente->deuda_total > 0;
            })
            ->sortByDesc('deuda_total')
            ->take(10);
        
        $clientesConDeuda = $creditosPorCliente->values();

        // Generar PDF
        $pdf = Pdf::loadView('reportes.resumen_cartera', [
            'totalCreditos' => $totalCreditos,
            'creditosPagados' => $creditosPagados,
            'creditosPendientes' => $creditosPendientes,
            'creditosVencidos' => $creditosVencidos,
            'montoTotalCartera' => $montoTotalCartera,
            'montoTotalPagado' => $montoTotalPagado,
            'saldoPendienteTotal' => $saldoPendienteTotal,
            'montoPorEstado' => $montoPorEstado,
            'clientesConDeuda' => $clientesConDeuda,
            'fechaReporte' => now()->format('d/m/Y H:i'),
            'porcentajePagado' => $montoTotalCartera > 0 ? ($montoTotalPagado / $montoTotalCartera) * 100 : 0,
        ]);

        return $pdf->download('resumen_cartera_' . now()->format('Y-m-d') . '.pdf');
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
