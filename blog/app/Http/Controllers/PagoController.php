<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Credito;
use App\Models\CuentaPorCobrar;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('credito.cliente')->get();
        return view('pagos.index', compact('pagos'));
    }

    public function create()
{
    $creditos = Credito::with('cliente')->get();
    return view('pagos.create', compact('creditos'))->with([
        'route' => route('pagos.store'), 
        'method' => 'POST', 
        'pago' => new \App\Models\Pago() // Pasar una nueva instancia para el formulario
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'credito_id' => 'required|exists:creditos,id',
            'fecha_pago' => 'required|date',
            'monto_pago' => 'required|numeric|min:0.01',
            'metodo_pago' => 'nullable|string|max:100',
            'estado_pago' => 'required|in:pagado,pendiente',
        ]);

        // 1. Registrar el pago
        $pago = Pago::create($request->all());

        // 2. Obtener el crédito con sus pagos y su cliente
        $credito = Credito::with('cliente', 'pagos')->findOrFail($request->credito_id);

        // 3. Calcular el total pagado y saldo pendiente
        $totalPagado = $credito->pagos->sum('monto_pago');
        $saldoPendiente = $credito->monto_total - $totalPagado;

        // 4. Calcular fecha de vencimiento
        $fechaVencimiento = $credito->fecha_vencimiento ?? $credito->fecha_credito->addDays(30);

        // 5. Crear o actualizar la cuenta por cobrar
        CuentaPorCobrar::updateOrCreate(
            ['cliente_id' => $credito->cliente_id],
            [
                'monto_adeudado'   => $credito->monto_total,
                'saldo_pendiente'  => max($saldoPendiente, 0),
                'fecha_vencimiento'=> $fechaVencimiento,
                'estado'           => $saldoPendiente <= 0 ? 'al_dia' : 'mora',
            ]
        );

        return redirect()->route('pagos.index')->with('success', 'Pago registrado y cuenta por cobrar actualizada.');
    }

    public function edit(Pago $pago)
{
    $creditos = Credito::with('cliente')->get();
    return view('pagos.edit', compact('pago', 'creditos'))->with([
        'route' => route('pagos.update', $pago), 
        'method' => 'PUT'
    ]);
}

    public function update(Request $request, Pago $pago)
    {
        $request->validate([
            'credito_id'  => 'required|exists:creditos,id',
            'fecha_pago'  => 'required|date',
            'monto_pago'  => 'required|numeric|min:0.01',
            'metodo_pago' => 'nullable|string|max:100',
            'estado_pago' => 'required|in:pagado,pendiente',
        ]);

        // Obtener el monto anterior
        $montoAnterior = $pago->monto_pago;

        // Actualizar el pago con los nuevos datos
        $pago->update($request->all());

        // Obtener el crédito y su cliente
        $credito = Credito::with('pagos')->findOrFail($request->credito_id);
        $clienteId = $credito->cliente_id;

        // Recalcular saldo pendiente después del cambio
        $totalPagado = $credito->pagos->sum('monto_pago');
        $saldoPendiente = $credito->monto_total - $totalPagado;

        // Actualizar cuenta por cobrar
        $cuenta = CuentaPorCobrar::where('cliente_id', $clienteId)->first();
        if ($cuenta) {
            $cuenta->saldo_pendiente = max($saldoPendiente, 0);
            $cuenta->estado = $saldoPendiente <= 0 ? 'al_dia' : 'mora';
            $cuenta->save();
        }

        return redirect()->route('pagos.index')->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy(Pago $pago)
    {
        // Obtener datos antes de eliminar
        $credito = $pago->credito;
        $clienteId = $credito->cliente_id;
        $montoEliminado = $pago->monto_pago;

        // Eliminar el pago
        $pago->delete();

        // Recalcular cuenta por cobrar
        $credito = Credito::with('pagos')->findOrFail($credito->id);
        $totalPagado = $credito->pagos->sum('monto_pago');
        $saldoPendiente = $credito->monto_total - $totalPagado;

        $cuenta = CuentaPorCobrar::where('cliente_id', $clienteId)->first();
        if ($cuenta) {
            $cuenta->saldo_pendiente = max($saldoPendiente, 0);
            $cuenta->estado = $saldoPendiente <= 0 ? 'al_dia' : 'mora';
            $cuenta->save();
        }

        return redirect()->route('pagos.index')->with('success', 'Pago eliminado y cuenta por cobrar actualizada.');
    }
}
