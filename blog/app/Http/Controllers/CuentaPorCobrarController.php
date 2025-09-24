<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\Credito;
use App\Models\DetalleCredito;
use Illuminate\Http\Request;

class CuentaPorCobrarController extends Controller
{
    public function index()
    {
        // ÚNICO CAMBIO: Solo obtener cuentas que tengan créditos activos
        $cuentas = CuentaPorCobrar::with('cliente')
            ->whereExists(function($query) {
                $query->select('id')
                    ->from('creditos')
                    ->whereColumn('creditos.cliente_id', 'cuentas_por_cobrar.cliente_id');
            })
            ->get();
        
        // El resto del código permanece exactamente igual
        foreach ($cuentas as $cuenta) {
            // Obtener el total de productos usando join
            $totalProductos = DetalleCredito::join('creditos', 'detalle_credito.credito_id', '=', 'creditos.id')
                ->where('creditos.cliente_id', $cuenta->cliente_id)
                ->sum('detalle_credito.subtotal');
            
            // Obtener el total de pagos realizados por el cliente
            $totalPagado = \App\Models\Pago::join('creditos', 'pagos.credito_id', '=', 'creditos.id')
                ->where('creditos.cliente_id', $cuenta->cliente_id)
                ->sum('pagos.monto_pago');
            
            // Calcular el saldo pendiente real
            $saldoPendiente = $totalProductos - $totalPagado;
            
            // Obtener la fecha de vencimiento más reciente del crédito del cliente
            $fechaVencimientoCredito = Credito::where('cliente_id', $cuenta->cliente_id)
                ->orderBy('fecha_vencimiento', 'desc')
                ->value('fecha_vencimiento');
            
            // Si existe fecha de vencimiento en crédito, usarla; sino usar la de la cuenta
            $fechaVencimientoFinal = $fechaVencimientoCredito ?? $cuenta->fecha_vencimiento;
            
            // Agregar los totales como atributos temporales
            $cuenta->totalProductos = $totalProductos;
            $cuenta->saldoPendienteReal = $saldoPendiente;
            $cuenta->fecha_vencimiento_real = $fechaVencimientoFinal;
        }
        
        return view('cuenta_cobrar.index', compact('cuentas'));
    }

    public function edit(CuentaPorCobrar $cuenta_cobrar)
    {
        // Obtener la fecha de vencimiento del crédito asociado
        $fechaVencimientoCredito = Credito::where('cliente_id', $cuenta_cobrar->cliente_id)
            ->orderBy('fecha_vencimiento', 'desc')
            ->value('fecha_vencimiento');
        
        // Agregar la fecha de vencimiento real como atributo temporal
        $cuenta_cobrar->fecha_vencimiento_credito = $fechaVencimientoCredito;
        
        return view('cuenta_cobrar.edit', compact('cuenta_cobrar'));
    }

    public function update(Request $request, CuentaPorCobrar $cuenta_cobrar)
    {
        $request->validate([
            'fecha_vencimiento' => 'required|date',
        ]);

        $cuenta_cobrar->update([
            'fecha_vencimiento' => $request->fecha_vencimiento,
        ]);

        // También actualizar la fecha de vencimiento en el crédito relacionado
        Credito::where('cliente_id', $cuenta_cobrar->cliente_id)
            ->update(['fecha_vencimiento' => $request->fecha_vencimiento]);

        return redirect()->route('cuenta_cobrar.index')
            ->with('success', 'Cuenta por cobrar y crédito actualizados correctamente.');
    }

    /**
     * Sincronizar fecha de vencimiento desde el crédito
     */
    public function sincronizar($id)
    {
        try {
            $cuenta = CuentaPorCobrar::findOrFail($id);
            
            // Buscar el crédito más reciente del cliente
            $credito = Credito::where('cliente_id', $cuenta->cliente_id)
                ->orderBy('fecha_vencimiento', 'desc')
                ->first();
            
            if (!$credito) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay crédito asociado a este cliente'
                ]);
            }
            
            // Actualizar fecha de vencimiento de la cuenta con la del crédito
            $cuenta->update([
                'fecha_vencimiento' => $credito->fecha_vencimiento
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Fecha de vencimiento sincronizada correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al sincronizar: ' . $e->getMessage()
            ]);
        }
    }
}