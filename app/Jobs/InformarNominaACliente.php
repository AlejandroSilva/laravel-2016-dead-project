<?php

namespace App\Jobs;

use App\Jobs\Job;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App;
use Log;
use Mail;

class InformarNominaACliente extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    protected $nomina;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nomina) {
        $this->nomina = $nomina;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        Log::info('#### JOB:InformarNominaACliente (inicio) ####');
        setlocale(LC_TIME, 'es_CL.utf-8');

        // diferenciar por cliente
        $inventario1 = $this->nomina->inventario1;
        $inventario2 = $this->nomina->inventario2;
        // la nomina puede ser la nomina de dia o la nomina de noche de un inventario, por eso se debe buscar en una de las dos relaciones
        $inventario = $inventario1? $inventario1: $inventario2;
        $local = $inventario->local;
        $cliente = $local->cliente;
        // --
        $_hlider = $this->nomina->horaPresentacionLider;
        $_hequipo = $this->nomina->horaPresentacionEquipo;
        $datosVista = [
            // datos generales
            'local' => $local,
            'fechaProgramada' => Carbon::parse($inventario->fechaProgramada)->formatLocalized('%A %d %B'),
            'horaPresentacionLider' => (new \DateTime($_hlider))->format('H:i'),
            'horaPresentacionEquipo' => (new \DateTime($_hequipo))->format('H:i'),
            // personal
            'lider' => $this->nomina->lider,
            'supervisor' => $this->nomina->supervisor,
            'dotacionTitular' => $this->nomina->dotacionTitular,
            'dotacionReemplazo' => $this->nomina->dotacionReemplazo,
        ];

        // La nomina a notificar es de FCV
        if($cliente->idCliente==2){
            $this->enviarFCV($local, $datosVista);
        }else{
            $this->enviarGENERICA($local, $datosVista, $cliente->nombreCorto);
        }
        Log::info('#### JOB:InformarNominaACliente (fin) ####');
    }

    private function enviarFCV($local, $datosVista){
        Mail::send('emails.informarNomina.FCV', $datosVista,
            function ($message) use($local){
                $message
                    ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                    ->subject("Nomina Cruz Verde Local Nº $local->numero");
                if(App::environment('production')){
                    $message
                        ->to('asilva@seiconsultores.cl', 'Alejandro Silva')
                        ->to('mgamboa@seiconsultores.cl', 'Marco Gamboa');
                }else{
                    $message
                        ->to('pm5k.sk@gmail.com', 'Alejandro Silva DEV');
                }
            }
        );
    }

    private function enviarGENERICA($local, $datosVista, $clienteNombre){
        Mail::send('emails.informarNomina.GENERICA', $datosVista,
            function ($message) use($local, $clienteNombre){
                $message
                    ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                    ->subject("Nomina $clienteNombre Local Nº $local->numero");

                // diferencias las listas de correo dependiendo del environment de ejecucion
                if(App::environment('production')){
                    $message
                        ->to('asilva@seiconsultores.cl', 'Alejandro Silva')
                        ->to('mgamboa@seiconsultores.cl', 'Marco Gamboa');
                }else{
                    $message
                        ->to('pm5k.sk@gmail.com', 'Alejandro Silva DEV');
                }
            }
        );
    }
}