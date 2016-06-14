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
        Commands\EnviarPeticionStock::class,
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
        
        
        // Los correos no se envian todos a la misma hora, para evitar que se confundan los mensajes en los logs de laravel
        /** ###################### Enviar correos programados pidiendo las MAESTRAS ###################### **/
        // PUC  - pedir todos los LUNES (exista o no inventario)
        $schedule->command('correo:peticionmaestra PUC')
            ->weekdays()->mondays()->at('08:00');

        // FCV  - pedir todos los LUNES y JUEVES (exista o no inventario)
        $schedule->command('correo:peticionmaestra FCV')
            ->weekdays()->mondays()->at('08:01');
        $schedule->command('correo:peticionmaestra FCV')
            ->weekdays()->thursdays()->at('08:01');

        // CKY  - pedir el dia habil anterior del inventario (si existe)
        $schedule->command('correo:peticionmaestra CKY')
            ->weekdays()->at('08:02');

        // CID  - pedir el mismo dia del inventario (si existe)
        // NO ENVIAR MIENTRAS NO SE TENGA UNA LISTA DE CORREOS VALIDA
//        $schedule->command('correo:peticionmaestra CID')
//            ->weekdays()->at('08:03');

        // FSB  - pedir el mismo dia del inventario (enviar a vicente)
        $schedule->command('correo:peticionmaestra FSB')
            ->weekdays()->at('08:04');

        // CMT  - pedir 2 dias habiles antes del inventario (si existe)
        $schedule->command('correo:peticionmaestra CMT')
            ->weekdays()->at('08:05');

        /** ################# Enviar correos programados pidiendo los archivos con STOCK ################# **/
        // PUC - pedir todos los LUNES
        $schedule->command('correo:peticionstock PUC')
            ->weekdays()->mondays()->at('08:10');

        // FCV - pedir todos los LUNES
        $schedule->command('correo:peticionstock FCV')
            ->weekdays()->mondays()->at('08:11');

        // CKY - pedir 2 dias habiles antes del inventario (si existe)
        $schedule->command('correo:peticionstock CKY')
            ->weekdays()->at('08:12');

        // FSB - pedir todos los LUNES
        $schedule->command('correo:peticionstock FSB')
            ->weekdays()->mondays()->at('08:13');
    }
}
