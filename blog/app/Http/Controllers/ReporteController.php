<?php

namespace App\Http\Controllers;

use App\Models\Credito;
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

        // Calcular totales
        $datos = $this->calcularTotalesCredito($credito);

        // Retornar vista HTML
        return view('reportes.vista_reporte_cliente', compact('credito') + $datos);
    }

    // Método para descargar el reporte por cliente como PDF
    public function descargarReportePorCliente($credito_id)
    {
        $credito = Credito::with(['cliente', 'detalles.producto', 'pagos'])
                    ->findOrFail($credito_id);

        // Calcular totales
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

        // Obtener detalles de crédito de la fecha seleccionada
        $detallesPorFecha = \DB::table('detalle_credito')
            ->join('creditos', 'detalle_credito.credito_id', '=', 'creditos.id')
            ->join('clientes', 'creditos.cliente_id', '=', 'clientes.id')
            ->join('productos', 'detalle_credito.producto_id', '=', 'productos.id')
            ->select(
                'detalle_credito.id',
                'detalle_credito.cantidad',
                'detalle_credito.precio_unitario',
                'detalle_credito.subtotal',
                'detalle_credito.created_at',
                'clientes.nombre as cliente_nombre',
                'productos.nombre as producto_nombre',
                'creditos.fecha_credito',
                'creditos.estado as credito_estado'
            )
            ->whereDate('detalle_credito.created_at', $fecha)
            ->orderBy('clientes.nombre')
            ->orderBy('productos.nombre')
            ->get();

        // Calcular totales
        $totalDelDia = $detallesPorFecha->sum('subtotal');
        $totalTransacciones = $detallesPorFecha->count();
        $clientesUnicos = $detallesPorFecha->unique('cliente_nombre')->count();

        // Agrupar por cliente para mejor presentación
        $detallesPorCliente = $detallesPorFecha->groupBy('cliente_nombre');

        // Generar PDF
        $pdf = Pdf::loadView('reportes.reporte_diario_detalles', compact(
            'detallesPorFecha', 
            'detallesPorCliente', 
            'fecha', 
            'totalDelDia', 
            'totalTransacciones', 
            'clientesUnicos'
        ));

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
        $totalProductos = $credito->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });

        $totalPagado = $credito->pagos->sum('monto_pago');
        $saldoPendiente = $totalProductos - $totalPagado;
        $totalCredito = $credito->monto_total;

        return compact('totalProductos', 'totalPagado', 'saldoPendiente', 'totalCredito');
    }
}