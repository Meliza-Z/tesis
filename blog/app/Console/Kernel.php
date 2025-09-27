<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Recalcula estados diariamente a la 01:00
        $schedule->command('credits:recalculate-states')->dailyAt('01:00');

        // Envía recordatorios por WhatsApp (stub) a las 09:00
        $schedule->command('credits:send-reminders --window-days=3')->dailyAt('09:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}

