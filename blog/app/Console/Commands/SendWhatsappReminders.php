<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Credito;
use App\Models\CuentaPorCobrar;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendWhatsappReminders extends Command
{
    protected $signature = 'credits:send-reminders {--window-days=3 : Días previos al vencimiento para avisar} {--dry-run : Muestra recordatorios sin enviar}';
    protected $description = 'Detecta créditos por vencer o vencidos y registra recordatorios (stub WhatsApp)';

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $window = (int) $this->option('window-days');
        $hoy = Carbon::today();
        $limite = (clone $hoy)->addDays($window);

        $this->info(($dry ? '[DRY] ' : '') . "Buscando créditos por vencer (<= {$window} días) y vencidos...");

        $creditos = Credito::with('cliente')
            ->where('estado', '!=', 'pagado')
            ->get();

        $porVencer = collect();
        $vencidos = collect();

        foreach ($creditos as $c) {
            $vence = $c->fecha_vencimiento_ext ?? $c->fecha_vencimiento;
            if ($c->saldo_pendiente_centavos <= 0) continue;
            if ($vence->lt($hoy)) {
                $vencidos->push($c);
            } elseif ($vence->isBetween($hoy, $limite, true)) {
                $porVencer->push($c);
            }
        }

        $count = 0;
        foreach ([['tipo' => 'por_vencer', 'col' => $porVencer, 'proximo' => now()->addDays(2)], ['tipo' => 'vencido', 'col' => $vencidos, 'proximo' => now()->addDay()]] as $grupo) {
            foreach ($grupo['col'] as $c) {
                $msg = sprintf('%s: Cliente %s, Crédito %s, vence %s, saldo %.2f',
                    strtoupper($grupo['tipo']),
                    $c->cliente->nombre ?? 'N/D',
                    $c->codigo,
                    ($c->fecha_vencimiento_ext ?? $c->fecha_vencimiento)->toDateString(),
                    $c->saldo_pendiente_centavos / 100
                );
                if ($dry) {
                    $this->line('[DRY] ' . $msg);
                } else {
                    Log::info('[WHATSAPP-REMINDER] ' . $msg);
                    // Actualizar proximo_recordatorio_at en cuentas por cobrar
                    CuentaPorCobrar::where('cliente_id', $c->cliente_id)
                        ->update(['proximo_recordatorio_at' => $grupo['proximo']]);
                    $count++;
                }
            }
        }

        $this->info(($dry ? '[DRY] ' : '') . "Recordatorios registrados: {$count}");
        $this->info('Nota: En producción, remplazar el log por integración WhatsApp.');
        return Command::SUCCESS;
    }
}
