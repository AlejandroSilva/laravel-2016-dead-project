<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App;
use Log;
use Mail;

class InformarArchivoRespuestaWOM extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    protected $archivoRespuestaWOM;
    protected $WOM_to = [
        ['felipe.pavez@wom.cl', 'Felipe Pavez'],
        ['wilson.mollo@wom.cl', 'Wilson Mollo'],
    ];
    protected $SEI_nomina_bcc = [
        ['clopez@seiconsultores.cl', 'Carlos Lopez'],
        ['bgamboa@seiconsultores.cl', 'Bernardita Gamboa'],
        ['asilva@seiconsultores.cl', 'Alejandro Silva']
    ];
    protected $SEI_DESARROLLO = [
        ['pm5k.sk@gmail.com', 'ASILVA DESARROLLO'],
    ];

    public function __construct($archivoRespuesta) {
        $this->archivoRespuestaWOM = $archivoRespuesta;
    }
    public function handle() {

        Mail::send('emails.informarArchivo.archivo-respuesta-wom', [
                'archivoRespuesta' => $this->archivoRespuestaWOM
            ],
            function ($message){
                $message
                    ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                    ->subject('Archivo de respuesta disponible');

                if(App::environment('production')){
                    // en produccion enviar las nominas a los destinatarios reales
                    foreach($this->WOM_to as $destinatario){
                        $message->to($destinatario[0], $destinatario[1]);
                    }
                    // enviar las copias ocultas
                    foreach($this->SEI_nomina_bcc as $destinatario){
                        $message->bcc($destinatario[0], $destinatario[1]);
                    }
                }else{
                    // en desarrollo solo enviar a la lista de SEI_DESARROLLO
                    foreach($this->SEI_DESARROLLO as $destinatario){
                        $message->to($destinatario[0], $destinatario[1]);
                    }
                }
            }
        );
    }
}
