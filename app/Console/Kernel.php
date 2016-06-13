<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\NominasPendientes::class,
        Commands\PersonalCargar::class,
        Commands\EnviarPeticionMaestra::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        // Para configurar un cron:     sudo crontab -u [user] -e
        // documentacion:               https://laravel.com/docs/master/scheduling

        //$schedule->command('inspire')->hourly();
        //$schedule->command('nominas:pendientes')->dailyAt('08:00');	;

        /* */
        // PUC  - pedir todos los LUNES (exista o no inventario)
        $schedule->command('correo:peticionmaestra PUC')
            ->weekdays()->mondays()->at('08:00');

        // FCV  - pedir todos los LUNES y JUEVES (exista o no inventario)
        $schedule->command('correo:peticionmaestra FCV')
//            ->weekdays()->mondays()->at('08:01');
            ->weekdays()->mondays()->at('18:10');
        $schedule->command('correo:peticionmaestra FCV')
            ->weekdays()->thursdays()->at('08:01');

        // CKY  - pedir el dia habil anterior del inventario (si existe)
        $schedule->command('correo:peticionmaestra CKY')
            ->weekdays()->at('08:02');

        // CID  - pedir el mismo dia del inventario (si existe)
        $schedule->command('correo:peticionmaestra CID')
            ->weekdays()->at('08:03');

        // FSB  - pedir el mismo dia del inventario (enviar a vicente)
        $schedule->command('correo:peticionmaestra FSB')
            ->weekdays()->at('08:04');

        // CMT  - pedir 2 dias habiles antes del inventario (si existe)
        $schedule->command('correo:peticionmaestra CMT')
            ->weekdays()->at('08:05');
    }
}
