<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;
use Mail;

class NominasPendientes extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nominas:pendientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa las nominas que se encuentran pendientes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->log('#### CRON NOMINAS:PENDIENTES (inicio) ####');
        setlocale(LC_TIME, 'es_CL.utf-8');

        $hoy = Carbon::now();
        $hoy_mas_2dias = Carbon::now()->addDay(2);
        $hoy_mas_7dias = Carbon::now()->addDay(7);

        // Inventarios con Nominas Urgentes
        $inventarios_nominasUrgentes = app('App\Http\Controllers\InventariosController')->buscarInventarios_conFormato(
            // fechaInicio,       fechaFin,                       mes,FCV,lider, fechaSubida
            $hoy->toDateString(), $hoy_mas_2dias->toDateString(), null, 2, null, '0000-00-00'
        );

        $inventarios_nominasPendientes = app('App\Http\Controllers\InventariosController')->buscarInventarios_conFormato(
            //fechaInicio,                  fechaFin,                       mes,FCV,lider, fechaSubida
            $hoy->toDateString(), $hoy_mas_7dias->toDateString(), null, 2, null, '0000-00-00'
        );

        $this->log(count($inventarios_nominasUrgentes)." 'urgentes', sin nomina en los proximos 2 dias.");
        $this->log(count($inventarios_nominasPendientes)." 'pendientes' sin nomina en los proximos 7 dias.");
        
//        if(count($inventarios_nominasUrgentes)>0 || count($inventarios_nominasPendientes)>0){
            Mail::queue('emails.nominasPendientes', [
                'hoy' => $hoy->formatLocalized('%A %d %B'),
                'hoy_mas_2dias' => $hoy_mas_2dias->formatLocalized('%A %d %B'),
                'hoy_mas_7dias' => $hoy_mas_7dias->formatLocalized('%A %d %B'),
                'inventarios_nominasUrgentes'   => $inventarios_nominasUrgentes,
                'inventarios_nominasPendientes' => $inventarios_nominasPendientes
            ], function ($message) use($hoy){
                $message
                    ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                    ->to('pm5k.sk@gmail.com', 'Alejandro Silva')
//                    ->to('mgamboa@seiconsultores.cl', 'Marco Gamboa')
//                    ->cc('asilva@seiconsultores.cl', 'Alejandro Silva')
//                    ->cc('pm5k.sk@gmail.com', 'Alejandro Silva')
                    ->subject("Nominas pendientes ".$hoy->toDateString());
            });
//        }
        $this->log('#### CRON NOMINAS:PENDIENTES (fin) ####');
    }

    private function log($msg){
        //Log::info($msg);
        $this->info($msg);
    }
}
