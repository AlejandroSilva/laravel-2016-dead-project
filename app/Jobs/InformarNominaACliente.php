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
    protected $SEI_DESARROLLO = [
        ['pm5k.sk@gmail.com', 'ALESILVA DESARROLLO']
    ];
    protected $SEI_nomina_bcc = [
        ['asilva@seiconsultores.cl', 'Alejandro Silva'],
        ['eponce@seiconsultores.cl', 'Esteban Ponce'],
        ['mgamboa@seiconsultores.cl', 'Marco Gamboa'],
        ['clopez@seiconsultores.cl', 'Carlos Lopez'],
        ['gbriones@seiconsultores.cl', 'Gabriela Briones'],
        ['fpizarro@seiconsultores.cl', 'Francisca Pizarro'],
        ['psobarzo@seiconsultores.cl', 'Paula Sobarzo'],
        ['logistica@seiconsultores.cl', 'SEI']
    ];
    // Cliente 1: PUC
    protected $PUC_nomina_to = [
        ['amundaca@sb.cl', 'Alvaro Mundaca']
    ];
    // Cliente 2: FCV
    protected $FCV_nomina_to = [
        ['gabriel.vera@cruzverde.cl', 'Gabriel Vera'],
        ['pajorquera@cruzverde.cl', 'Pamela Jorquera'],
        ['jorge.alcaya@cruzverde.cl', 'Jorge Alcaya'],
        ['mauricio.ojeda@cruzverde.cl', 'Mauricio Ojeda']
    ];
    // Cliente 3: CKY
    protected $CKY_nomina_to = [
        ['XXXX', 'Jose Perez']
        // TODO: falta correo
    ];
    // Cliente 5: SB
    protected $SB_nomina_to = [
        ['amundaca@sb.cl', 'Alvaro Mundaca']
        //TODO: otro mas
    ];
    // Cliente 7: CMT
    protected $CMT_nomina_to = [
        // sin nomina, no va gente
        // TODO: evelin, cerna, cesar
    ];
    

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

        if($cliente->idCliente==1){                         // 1: PREUNIC
            $this->enviarGENERICA([
                'subject' => "Nomina PREUNIC Local Nº$local->numero",
                'to' => $this->PUC_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==2){                   // 2: FCV
            $this->enviarGENERICA([
                'subject' => "Nomina Cruz Verde Local Nº$local->numero",
                'to' => $this->FCV_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==3){                   // 3: CKY
            $this->enviarGENERICA([
                'subject' => "Nomina CKY Local Nº$local->numero",
                'to' => $this->CKY_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==5){                   // 5: SALCOBRAND
            $this->enviarGENERICA([
                'subject' => "Nomina SB Local Nº$local->numero",
                'to' => $this->SB_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==7){                   // 7: CMT
            $this->enviarGENERICA([
                'subject' => "Nomina CMT Local Nº$local->numero",
                'to' => $this->CMT_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else{
            $this->enviarGENERICA([                         // Otros clientes
                'subject' => "Nomina $cliente->nombreCorto Local Nº $local->numero (GENERICA)",
                'to' => $this->SEI_nomina_bcc,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }
        Log::info('#### JOB:InformarNominaACliente (fin) ####');
    }

    private function enviarGENERICA($datosCorreo, $datosVista){
        Mail::send('emails.informarNomina.GENERICA', $datosVista,
            function ($message) use($datosCorreo){
                $message
                    ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                    ->subject($datosCorreo['subject']);
                if(App::environment('production')){
                    // enviar a los destinatarios
                    foreach($datosCorreo['to'] as $destinatario)
                        $message->to($destinatario[0], $destinatario[1]);
                    // enviar las copias ocultas
                    foreach($datosCorreo['bcc'] as $destinatario)
                        $message->bcc($destinatario[0], $destinatario[1]);
                }else{
                    foreach($this->SEI_DESARROLLO as $destinatario)
                        $message->to($destinatario[0], $destinatario[1]);
                }
            }
        );
    }
}