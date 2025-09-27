<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Credito;
use App\Models\CuentaPorCobrar;
use App\Models\Cliente;
use Carbon\Carbon;

class RecalculateCreditStates extends Command
{
    protected $signature = 'credits:recalculate-states {--dry-run : Muestra cambios sin guardar}';
    protected $description = 'Recalcula estado de créditos y actualiza cuentas por cobrar (centavos, sin decimales)';

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $hoy = Carbon::today();
        $this->info(($dry ? '[DRY] ' : '') . 'Recalculando estados de créditos...');

        $creditos = Credito::with(['detalles', 'pagos', 'cliente'])->get();
        $actualizados = 0;

        foreach ($creditos as $c) {
            $total = $c->monto_total_centavos; // derivado
            $pagado = $c->total_pagado_centavos; // derivado
            $saldo = max(0, $total - $pagado);
            $vence = $c->fecha_vencimiento_ext ?? $c->fecha_vencimiento;

            $nuevoEstado = $saldo <= 0 ? 'pagado' : ($vence->lt($hoy) ? 'vencido' : 'activo');
            if (!$dry) {
                if ($c->estado !== $nuevoEstado) {
                    $c->estado = $nuevoEstado;
                    $c->save();
                    $actualizados++;
                }
            }
        }

        $this->info(($dry ? '[DRY] ' : '') . "Créditos con estado actualizado: {$actualizados}");

        $this->info(($dry ? '[DRY] ' : '') . 'Actualizando cuentas por cobrar...');
        $clientes = Cliente::with(['creditos.detalles', 'creditos.pagos'])->get();
        $cuentas = 0;

        foreach ($clientes as $cliente) {
            if ($cliente->creditos->isEmpty()) {
                continue;
            }
            $creditosCliente = $cliente->creditos;
            $montoAdeudado = (int) $creditosCliente->sum(fn($cr) => $cr->monto_total_centavos);
            $saldoPend = (int) $creditosCliente->sum(fn($cr) => $cr->saldo_pendiente_centavos);
            $abiertos = $creditosCliente->filter(fn($cr) => in_array($cr->estado, ['activo','vencido']));
            $vencimientos = $creditosCliente->map(fn($cr) => ($cr->fecha_vencimiento_ext ?? $cr->fecha_vencimiento));
            if ($vencimientos->isEmpty()) continue;
            $fechaVenc = $abiertos->isNotEmpty() ? $abiertos->map(fn($cr)=>($cr->fecha_vencimiento_ext ?? $cr->fecha_vencimiento))->min() : $vencimientos->max();
            $estado = $creditosCliente->contains(fn($cr) => $cr->estado === 'vencido') ? 'mora' : 'al_dia';

            if (!$dry) {
                CuentaPorCobrar::updateOrCreate(
                    ['cliente_id' => $cliente->id],
                    [
                        'monto_adeudado_centavos' => $montoAdeudado,
                        'saldo_pendiente_centavos' => $saldoPend,
                        'fecha_vencimiento' => $fechaVenc->toDateString(),
                        'estado' => $estado,
                        'proximo_recordatorio_at' => $estado === 'mora' ? now()->addDay() : now()->addDays(3),
                    ]
                );
                $cuentas++;
            }
        }

        $this->info(($dry ? '[DRY] ' : '') . "Cuentas por cobrar actualizadas: {$cuentas}");
        return Command::SUCCESS;
    }
}

