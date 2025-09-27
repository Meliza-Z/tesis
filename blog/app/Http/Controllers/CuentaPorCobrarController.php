<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\Credito;
use App\Models\DetalleCredito;
use App\Models\Pago;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CuentaPorCobrarController extends Controller
{
    public function index()
    {
        // Construir filas por crédito con cliente y progresos
        $creditos = Credito::with(['cliente', 'detalles', 'pagos'])->get()->map(function (Credito $c) {
            $monto = $c->monto_total_centavos;
            $pagado = $c->total_pagado_centavos;
            $saldo = max(0, $monto - $pagado);
            $vence = $c->fecha_vencimiento_ext ?? $c->fecha_vencimiento;
            $progreso = $monto > 0 ? round(($pagado / $monto) * 100, 2) : 0;
            return (object) [
                'id' => $c->id,
                'codigo' => $c->codigo,
                'cliente' => optional($c->cliente)->nombre ?? 'N/D',
                'monto' => $monto / 100,
                'saldo' => $saldo / 100,
                'vence' => $vence ? $vence->toDateString() : null,
                'progreso' => $progreso,
                'estado' => $c->estado,
            ];
        });

        return view('cuenta_cobrar.index', [
            'creditos' => $creditos,
        ]);
    }

    public function edit(CuentaPorCobrar $cuenta_cobrar)
    {
        // Obtener la fecha de vencimiento del crédito asociado
        $fechaVencimientoCredito = Credito::where('cliente_id', $cuenta_cobrar->cliente_id)
            ->orderByRaw('COALESCE(fecha_vencimiento_ext, fecha_vencimiento) DESC')
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
                ->orderByRaw('COALESCE(fecha_vencimiento_ext, fecha_vencimiento) DESC')
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

    // Mostrar un crédito con sus ítems y pagos, y permitir registrar pagos
    public function showCredit(Credito $credito)
    {
        $credito->load(['cliente', 'detalles.producto', 'pagos']);
        // Mostrar misma UI que /creditos/{id}
        return view('creditos.show', compact('credito'));
    }

    // Marcar crédito como pagado si el progreso está al 100% (saldo 0)
    public function markPaid(Credito $credito)
    {
        if ($credito->saldo_pendiente_centavos > 0) {
            return back()->with('error', 'No se puede marcar como pagado: el saldo no es 0.');
        }
        $credito->estado = 'pagado';
        $credito->save();

        // Actualizar cuentas por cobrar del cliente
        $cliente = $credito->cliente()->with('creditos.detalles', 'creditos.pagos')->first();
        if ($cliente) {
            $creditosCliente = $cliente->creditos;
            $montoAdeudado = (int) $creditosCliente->sum(fn($cr) => $cr->monto_total_centavos);
            $saldoPend = (int) $creditosCliente->sum(fn($cr) => $cr->saldo_pendiente_centavos);
            $abiertos = $creditosCliente->filter(fn($cr) => in_array($cr->estado, ['activo','vencido']));
            $vencimientos = $creditosCliente->map(fn($cr) => ($cr->fecha_vencimiento_ext ?? $cr->fecha_vencimiento));
            if ($vencimientos->isNotEmpty()) {
                $fechaVenc = $abiertos->isNotEmpty() ? $abiertos->map(fn($cr)=>($cr->fecha_vencimiento_ext ?? $cr->fecha_vencimiento))->min() : $vencimientos->max();
                CuentaPorCobrar::updateOrCreate(
                    ['cliente_id' => $cliente->id],
                    [
                        'monto_adeudado_centavos' => $montoAdeudado,
                        'saldo_pendiente_centavos' => $saldoPend,
                        'fecha_vencimiento' => $fechaVenc?->toDateString(),
                        'estado' => $creditosCliente->contains(fn($cr) => $cr->estado === 'vencido') ? 'mora' : 'al_dia',
                    ]
                );
            }
        }

        return back()->with('success', 'Crédito marcado como pagado.');
    }
}
