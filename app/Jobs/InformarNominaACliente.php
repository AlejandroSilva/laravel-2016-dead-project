<?php

namespace App\Jobs;

use App\Jobs\Job;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        $fechaProgramada = Carbon::parse($inventario->fechaProgramada)->formatLocalized('%A %d %B');
        $horaPresentacionLider = $this->nomina->horaPresentacionLider;

        // La nomina a notificar es de FCV
        if($cliente->idCliente==2){
            $this->enviarFCV($local, $fechaProgramada, $horaPresentacionLider);
        }else{
            $this->enviarGENERICA($cliente->nombre, $local, $fechaProgramada, $horaPresentacionLider);
        }
        Log::info('#### JOB:InformarNominaACliente (fin) ####');
    }

    private function enviarFCV($local, $fechaProgramada, $horaPresentacionLider){
        Mail::send('emails.informarNomina.FCV', [
            // datos generales
            'local' => $local,
            'fechaProgramada' => $fechaProgramada,
            'horaPresentacionLider' => $horaPresentacionLider,
            // personal
            'lider' => $this->nomina->lider,
            'supervisor' => $this->nomina->supervisor,
            'dotacionTitular' => $this->nomina->dotacionTitular,
            'dotacionReemplazo' => $this->nomina->dotacionReemplazo,
        ], function ($message) use($local){
            $message
                ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                ->to('asilva@seiconsultores.cl', 'Alejandro Silva')
                ->to('mgamboa@seiconsultores.cl', 'Marco Gamboa')
                ->subject("Nomina Cruz Verde Local Nº $local->numero");
        });
    }

    private function enviarGENERICA($clienteNombre, $local, $fechaProgramada, $horaPresentacionLider){
        Mail::send('emails.informarNomina.GENERICA', [
            // datos generales
            'local' => $local,
            'fechaProgramada' => $fechaProgramada,
            'horaPresentacionLider' => $horaPresentacionLider,
            // personal
            'lider' => $this->nomina->lider,
            'supervisor' => $this->nomina->supervisor,
            'dotacionTitular' => $this->nomina->dotacionTitular,
            'dotacionReemplazo' => $this->nomina->dotacionReemplazo,
        ], function ($message) use($local, $clienteNombre){
            $message
                ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                ->to('asilva@seiconsultores.cl', 'Alejandro Silva')
                ->to('mgamboa@seiconsultores.cl', 'Marco Gamboa')
                ->subject("Nomina $clienteNombre Local Nº $local->numero");
        });
    }
}