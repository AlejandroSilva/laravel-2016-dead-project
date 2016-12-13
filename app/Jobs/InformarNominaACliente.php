<?php

namespace App\Jobs;

use App\Jobs\Job;
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
//        ['logistica@seiconsultores.cl', 'SEI']
    ];
    protected $SEI_nomina_bcc = [
        ['mgamboa@seiconsultores.cl', 'Marco Gamboa'],
        ['clopez@seiconsultores.cl', 'Carlos Lopez'],
        ['gbriones@seiconsultores.cl', 'Gabriela Briones'],
        ['fpizarro@seiconsultores.cl', 'Francisca Pizarro'],
        ['psobarzo@seiconsultores.cl', 'Paula Sobarzo'],
        ['logistica@seiconsultores.cl', 'SEI'],
        ['asilva@seiconsultores.cl', 'Alejandro Silva']
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
        ['ximena.delgado@casaideas.com', 'Ximena Delgado'],
        ['control.existencias@casaideas.com', 'Control existencias']
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
        ['cmartinez@construmart.cl', 'Cesar Martinez'],
        ['bespinaza@construmart.cl', 'Beatriz Espinaza'],
        ['emolina@construmart.cl', 'Cesar Martinez'],
        ['vcaamano@construmart.cl', 'Victor Caamaño'],
        ['jjimenez@construmart.cl', 'Juan Carlos Jimenez Velasquez'],
        ['cristian.cerna@construmart.cl', 'Cristian Cerna'],
    ];
    // Cliente 9: WOM
    protected $WOM_nomina_to = [
        ['felipe.pavez@wom.cl', 'Felipe Pavez'],
        ['wilson.mollo@wom.cl', 'Wilson Mollo'],
        ['mariella.leiva@wom.cl', 'Mariella Leiva'],
        ['pablo.jorquera@wom.cl', 'Pablo Jorquera'],
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
            // personal
            'lider' => $lider,
            'supervisor' => $this->nomina->supervisor,
            'dotacionTitular' => $this->nomina->dotacionTitular,
            'dotacionReemplazo' => $this->nomina->dotacionReemplazo,
        ];

        // Si el correo esta RECTIFICADO, cambiar el titulo
        $nominaRectificada = $this->nomina->rectificada==1? "Nomina RECTIFICADA" : "Nomina";

        if($cliente->idCliente==1){                         // 1: PREUNIC
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "$nominaRectificada PREUNIC Nº$local->numero",
                'to' => $this->PUC_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==2){                   // 2: FCV
            // FCV tambien envia un correo al local (solo si esta definido)
            $destinatariosFCV = $this->FCV_nomina_to;
            $correoLocal = $local->emailContacto;
            if($correoLocal)
                array_unshift($destinatariosFCV, [$correoLocal, "Local $local->numero"]);   // agrega al inicio
            $this->enviarCorreos('emails.informarNomina.FCV', [
                'subject' => "$nominaRectificada Cruz Verde Nº$local->numero",
                'to' => $destinatariosFCV,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==3){                   // 3: CKY
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "$nominaRectificada CKY Local Nº$local->numero",
                'to' => $this->CKY_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==4){                   // 4: CID
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "$nominaRectificada CID Local Nº$local->numero",
                'to' => $this->CID_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==5){                   // 5: SALCOBRAND
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "$nominaRectificada SB Local Nº$local->numero",
                'to' => $this->SB_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==7){                   // 7: CMT
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "$nominaRectificada CMT Local Nº$local->numero",
                'to' => $this->CMT_nomina_to,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else if($cliente->idCliente==9){                   // 9: WOM
            // WOM tambien envia un correo al local (solo si esta definido)
            $destinatariosWOM = $this->WOM_nomina_to;
            $correoLocal = $local->emailContacto;
            if($correoLocal)
                array_unshift($destinatariosWOM, [$correoLocal, "Local $local->numero"]);   // agrega al inicio

            $this->enviarCorreos('emails.informarNomina.WOM', [
                'subject' => "$nominaRectificada WOM organización $local->numero",
                'to' => $destinatariosWOM,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }else{                                              // Otros clientes
            $this->enviarCorreos('emails.informarNomina.GENERICA', [
                'subject' => "$nominaRectificada $cliente->nombreCorto Local Nº $local->numero (GENERICA)",
                'to' => $this->SEI_nomina_bcc,
                'bcc' => $this->SEI_nomina_bcc
            ], $datosVista);
        }
        Log::info('#### JOB:InformarNominaACliente (fin) ####');
    }

    private function enviarCorreos($plantilla, $datosCorreo, $datosVista){
        Mail::send($plantilla, $datosVista,
            function ($message) use($datosCorreo){
                $subject = $datosCorreo['subject'];
                Log::info("[prod] subject: $subject");

                $message
                    ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                    ->subject($subject);
                
                if(App::environment('production')){
                    // en produccion enviar las nominas a los destinatarios reales
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
                    // en desarrollo solo enviar a la lista de SEI_DESARROLLO
                    foreach($this->SEI_DESARROLLO as $destinatario){
                        $message->to($destinatario[0], $destinatario[1]);
                        Log::info("[dev] enviando TO: $destinatario[0] - $destinatario[1]");
                    }
                }
            }
        );
    }
}
