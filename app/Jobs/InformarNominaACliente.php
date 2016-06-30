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
        ['pm5k.sk@gmail.com', 'ASILVA DESARROLLO'],
//        ['eponce@seiconsultores.cl', 'Esteban Ponce'],
//        ['logistica@seiconsultores.cl', 'SEI']
    ];
    protected $SEI_nomina_bcc = [
        ['mgamboa@seiconsultores.cl', 'Marco Gamboa'],
        ['clopez@seiconsultores.cl', 'Carlos Lopez'],
        ['gbriones@seiconsultores.cl', 'Gabriela Briones'],
        ['fpizarro@seiconsultores.cl', 'Francisca Pizarro'],
        ['psobarzo@seiconsultores.cl', 'Paula Sobarzo'],
        ['logistica@seiconsultores.cl', 'SEI'],
        ['eponce@seiconsultores.cl', 'Esteban Ponce'],
        ['asilva@seiconsultores.cl', 'Alejandro Silva'],
    ];
    // Cliente 1: PUC
    protected $PUC_nomina_to = [
//        ['mgamboa@seiconsultores.cl', 'Marco Gamboa']
        ['amundaca@sb.cl', 'Alvaro Mundaca']
    ];
    // Cliente 2: FCV
    protected $FCV_nomina_to = [
//        ['mgamboa@seiconsultores.cl', 'Marco Gamboa']
        ['gabriel.vera@cruzverde.cl', 'Gabriel Vera'],
        ['pajorquera@cruzverde.cl', 'Pamela Jorquera'],
        ['jorge.alcaya@cruzverde.cl', 'Jorge Alcaya'],
        ['mauricio.ojeda@cruzverde.cl', 'Mauricio Ojeda']
    ];
    // Cliente 3: CKY
    protected $CKY_nomina_to = [
        ['rlopez@colloky.cl',           'Roberto Lopez'],
        ['jose.perez@colgram.com',      'Jose Perez'],
        ['ricardo.perez@colgram.com',   'Ricardo Perez Fuentes'],
        ['manuel.vera@colgram.com',     'Manuel Vera']
    ];
    // Cliente 4: CID
    protected $CID_nomina_to = [
        ['ximena.delgado@casaideas.com', 'Ximena Delgado']
    ];
    // Cliente 5: SB
    protected $SB_nomina_to = [
//        ['mgamboa@seiconsultores.cl', 'Marco Gamboa']
        ['mbenavente@sb.cl', 'Marianela Benavente'],
        ['sguajardo@sb.cl', 'Silvia Guajardo'],
        ['controldeinventariosb@sb.cl', 'Control de Inventario'],
        ['vcaceres@sb.cl', 'Vicente Caceres'],
        ['jnecochea@sb.cl', 'Janet Necochea']
    ];
    // Cliente 7: CMT
    protected $CMT_nomina_to = [
        ['mgamboa@seiconsultores.cl', 'Marco Gamboa']
//        ['bgamboa@seiconsultores.cl', 'Bernardita Gamboa']
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
        $lider = $this->nomina->lider;
        // --

        $datosVista = [
            // datos generales
            'local' => $local,
            'inventario' => $inventario,
            'nomina' => $this->nomina,
//            'fechaProgramada' => Carbon::parse($inventario->fechaProgramada)->formatLocalized('%A %d %B'),
//            'horaPresentacionLider' => (new \DateTime($_hlider))->format('H:i'),
//            'horaPresentacionEquipo' => (new \DateTime($_hequipo))->format('H:i'),
//            'horaPresentacionLider' => $this->nomina->horaPresentacionLiderF(),
//            'horaPresentacionEquipo' => $this->nomina->horaPresentacionEquipoF(),
            // personal
            'lider' => $lider,
            'supervisor' => $this->nomina->supervisor,
            'dotacionTitular' => $this->nomina->dotacionTitular,
            'dotacionReemplazo' => $this->nomina->dotacionReemplazo,
        ];

        if($cliente->idCliente==1){                         // 1: PREUNIC
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "Nomina PREUNIC Local Nº$local->numero",
                'to' => $this->PUC_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==2){                   // 2: FCV
            // FCV tambien envia un correo al local (solo si esta definido)
            $destinatariosFCV = $this->FCV_nomina_to;
            $correoLocal = $local->emailContacto;
            if($correoLocal)
                array_unshift($destinatariosFCV, [$correoLocal, "Local $local->numero"]);   // agrega al inicio
                //array_push($destinatariosFCV, [$correoLocal, "Local $local->numero"]);    // agrega al final

            // Si el correo esta RECTIFICADO, cambiar el titulo
            $subject = $this->nomina->rectificada==1?
                "Nomina RECTIFICADA Cruz Verde Local Nº$local->numero"
                :
                "Nomina Cruz Verde Local Nº$local->numero";
            $this->enviarCorreos('emails.informarNomina.FCV', [
                'subject' => $subject,
                'to' => $destinatariosFCV,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==3){                   // 3: CKY
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "Nomina CKY Local Nº$local->numero",
                'to' => $this->CKY_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==4){                   // 4: CID
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "Nomina CID Local Nº$local->numero",
                'to' => $this->CID_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==5){                   // 5: SALCOBRAND
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "Nomina SB Local Nº$local->numero",
                'to' => $this->SB_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==7){                   // 7: CMT
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "Nomina CMT Local Nº$local->numero",
                'to' => $this->CMT_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else{                                              // Otros clientes
            $this->enviarCorreos('emails.informarNomina.GENERICA', [                         
                'subject' => "Nomina $cliente->nombreCorto Local Nº $local->numero (GENERICA)",
                'to' => $this->SEI_nomina_bcc,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }
        Log::info('#### JOB:InformarNominaACliente (fin) ####');
    }

    private function enviarCorreos($plantilla, $datosCorreo, $datosVista){
        Mail::send($plantilla, $datosVista,
            function ($message) use($datosCorreo){
                $message
                    ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                    ->subject($datosCorreo['subject']);
                if(App::environment('production')){
                    // enviar a los destinatarios
                    foreach($datosCorreo['to'] as $destinatario){
                        $message->to($destinatario[0], $destinatario[1]);
                        Log::info("[prod] enviando TO: $destinatario[0] - $destinatario[1]");
                    }
                    // enviar las copias ocultas
                    foreach($datosCorreo['bcc'] as $destinatario){
                        $message->bcc($destinatario[0], $destinatario[1]);
                        Log::info("[prod] enviando BCC: $destinatario[0] - $destinatario[1]");
                    }
                }else{
                    foreach($this->SEI_DESARROLLO as $destinatario){
                        $message->to($destinatario[0], $destinatario[1]);
                        Log::info("[dev] enviando TO: $destinatario[0] - $destinatario[1]");
                    }
                }
            }
        );
    }
}
