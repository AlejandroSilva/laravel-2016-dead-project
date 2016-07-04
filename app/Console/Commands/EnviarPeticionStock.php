<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App;
use Log;
use Mail;
use App\Clientes;

class EnviarPeticionStock extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correo:peticionstock {nombreCortoCliente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    protected $SEI_DESARROLLO = [
        ['pm5k.sk@gmail.com', 'ASILVA DESARROLLO'],
    ];
    protected $SEI_stock_bcc = [
        ['mgamboa@seiconsultores.cl', 'Marco Gamboa'],
        ['eponce@seiconsultores.cl', 'Esteban Ponce'],
        ['asilva@seiconsultores.cl', 'Alejandro Silva'],
    ];
    // Cliente 1: PUC
    protected $PUC_stock_to = [
        ['amundaca@sb.cl', 'Alvaro Mundaca']
    ];
    // Cliente 2: FCV
    protected $FCV_stock_to = [
        ['gabriel.vera@cruzverde.cl', 'Gabriel Vera'],
        ['pajorquera@cruzverde.cl', 'Pamela Jorquera'],
        ['jorge.alcaya@cruzverde.cl', 'Jorge Alcaya'],
        ['mauricio.ojeda@cruzverde.cl', 'Mauricio Ojeda']
    ];
    // Cliente 3: CKY
    protected $CKY_stock_to = [
        ['rlopez@colloky.cl',           'Roberto Lopez'],
        ['jose.perez@colgram.com',      'Jose Perez'],
        ['ricardo.perez@colgram.com',   'Ricardo Perez Fuentes'],
        ['manuel.vera@colgram.com',     'Manuel Vera']
    ];
    // Cliente 4: CID
    protected $CID_stock_to = [
        ['ximena.delgado@casaideas.com', 'Ximena Delgado'],
        ['control.existencias@casaideas.com', 'Control existencias']
    ];
    // Cliente 5: FSB
    protected $FSB_stock_to = [
        ['mbenavente@sb.cl', 'Marianela Benavente'],
        ['sguajardo@sb.cl', 'Silvia Guajardo'],
        ['controldeinventariosb@sb.cl', 'Control de Inventario'],
        ['vcaceres@sb.cl', 'Vicente Caceres'],
        ['jnecochea@sb.cl', 'Janet Necochea']
    ];
    // Cliente 7: CMT
    protected $CMT_stock_to = [
        ['mgamboa@seiconsultores.cl', 'Marco Gamboa']
        //        ['bgamboa@seiconsultores.cl', 'Bernardita Gamboa']
        // TODO: evelin, cerna, cesar
    ];
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $nombreCliente = $this->argument('nombreCortoCliente');

        if($nombreCliente=="PUC"){
            $this->log("[PeticionStock] Cliente PUC: pedir stock actualizado todos los LUNES.");

            // se asume de que cuando se llama este metodo, es porque ES LUNES, no se hacen validaciones
            $this->enviarCorreos('emails.peticionStock.GENERICA', [
                'subject' => 'Solicitud de Stock actualizado PUC',
                'to' => $this->PUC_stock_to,
                'bcc' => $this->SEI_stock_bcc
            ], []);
            
        }else if($nombreCliente=="FCV"){
            $this->log("[PeticionStock] Cliente FCV: pedir stock actualizado todos los LUNES.");

            // se asume de que cuando se llama este metodo, es porque ES LUNES, no se hacen validaciones
            $this->enviarCorreos('emails.peticionStock.GENERICA', [
                'subject' => 'Solicitud de Stock actualizado FCV',
                'to' => $this->FCV_stock_to,
                'bcc' => $this->SEI_stock_bcc
            ], []);

        } else if($nombreCliente=="CKY"){
            $this->log("[PeticionStock] Cliente CKY: pedir 2 dias habiles ANTES del inventario (si existe).");

            // Carbon busca el 2 dias habiles despues del dia actual (de la semana Jueves->Lunes)
            // documentacion: http://carbon.nesbot.com/docs/#api-addsub
            $sgteHabil = Carbon::now()->addWeekday(2)->toDateString();
            $totalInventarios = app('App\Http\Controllers\InventariosController')
                ->buscarInventarios($sgteHabil, $sgteHabil, null, 3, null, null)    // 3 = CKY
                ->count();
            if( $totalInventarios>0 ){
                $this->log("[PeticionStock] CKY tiene '$totalInventarios' inventarios programados para el sub-siguiente dia habil ($sgteHabil)...");
                
                // enviar correo al cliente
                $this->enviarCorreos('emails.peticionStock.GENERICA', [
                    'subject' => 'Solicitud de Maestra CKY',
                    'to' => $this->CKY_stock_to,
                    'bcc' => $this->SEI_stock_bcc
                ], []);
            }else{
                $this->log("[PeticionStock] CKY no tiene inventarios programados para el sub-siguiente dia habil ($sgteHabil), no se enviara el correo.");
            }

        } else if($nombreCliente=="FSB"){
            $this->log("[PeticionStock] Cliente FSB: pedir stock actualizado todos los LUNES.");
            // FSB - pedir todos los LUNES
            // se asume de que cuando se llama este metodo, es porque ES LUNES, no se hacen validaciones
            $this->enviarCorreos('emails.peticionStock.GENERICA', [
                'subject' => 'Solicitud de Stock actualizado FSB',
                'to' => $this->FSB_stock_to,
                'bcc' => $this->SEI_stock_bcc
            ], []);

        }else{
            $this->log("[PeticionStock] cliente '$nombreCliente' no programado.");
        }
        
        
    }

    // Exactamente el mismo metodo de InformarNominaACliente.php  
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
                        $this->log("[PeticionStock] enviando TO: $destinatario[0] - $destinatario[1]");
                    }
                    // enviar las copias ocultas
                    foreach($datosCorreo['bcc'] as $destinatario){
                        $message->bcc($destinatario[0], $destinatario[1]);
                        $this->log("[PeticionStock] enviando BCC: $destinatario[0] - $destinatario[1]");
                    }
                }else{
                    foreach($this->SEI_DESARROLLO as $destinatario){
                        $message->to($destinatario[0], $destinatario[1]);
                        $this->log("[PeticionStock-DEV] enviando TO: $destinatario[0] - $destinatario[1]");
                    }
                }
            }
        );
    }

    private function log($msg){
        Log::info($msg);
        $this->info($msg);
    }
}
