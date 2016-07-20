<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App;
use Log;
use Mail;
use App\Clientes;

class EnviarPeticionMaestra extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correo:peticionmaestra {nombreCortoCliente}';

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
    protected $SEI_nomina_bcc = [
        ['mgamboa@seiconsultores.cl', 'Marco Gamboa'],
        ['eponce@seiconsultores.cl', 'Esteban Ponce'],
        ['asilva@seiconsultores.cl', 'Alejandro Silva'],
    ];
    // Cliente 1: PUC
    protected $PUC_maestra_to = [
        ['amundaca@sb.cl', 'Alvaro Mundaca']
    ];
    // Cliente 2: FCV
    protected $FCV_maestra_to = [
        ['gabriel.vera@cruzverde.cl', 'Gabriel Vera'],
        ['pajorquera@cruzverde.cl', 'Pamela Jorquera'],
        ['jorge.alcaya@cruzverde.cl', 'Jorge Alcaya'],
        ['mauricio.ojeda@cruzverde.cl', 'Mauricio Ojeda']
    ];
    // Cliente 3: CKY
    protected $CKY_maestra_to = [
        ['rlopez@colloky.cl',           'Roberto Lopez'],
        ['jose.perez@colgram.com',      'Jose Perez'],
        ['ricardo.perez@colgram.com',   'Ricardo Perez Fuentes'],
        ['manuel.vera@colgram.com',     'Manuel Vera']
    ];
    // Cliente 4: CID
    protected $CID_maestra_to = [
        ['ximena.delgado@casaideas.com', 'Ximena Delgado'],
        ['control.existencias@casaideas.com', 'Control existencias']
    ];
    // Cliente 5: SB
    protected $FSB_maestra_to = [
        ['mbenavente@sb.cl', 'Marianela Benavente'],
        ['sguajardo@sb.cl', 'Silvia Guajardo'],
        ['controldeinventariosb@sb.cl', 'Control de Inventario'],
        ['vcaceres@sb.cl', 'Vicente Caceres'],
        ['jnecochea@sb.cl', 'Janet Necochea']
    ];
    // Cliente 7: CMT
    protected $CMT_maestra_to = [
        ['cmartinez@construmart.cl', 'Cesar Martinez']
    ];

    public function handle() {
        $nombreCliente = $this->argument('nombreCortoCliente');

        if($nombreCliente=="PUC")
            $this->procesarPUC();
        else if($nombreCliente=="FCV")
            $this->procesarFCV();
        else if($nombreCliente=="CKY")
            $this->procesarCKY();
        else if($nombreCliente=="CID")
            $this->procesarCID();
        else if($nombreCliente=="FSB")
            $this->procesarFSB();
        else if($nombreCliente=="CMT")
            $this->procesarCMT();
        else{
            $this->log("[PeticionMaestra] cliente '$nombreCliente' no programado.");
        }
    }
    
    
    private function procesarPUC(){
        $this->log("[PeticionMaestra] Cliente PUC: pedir maestra todos los dias LUNES...");
        // PUC  - pedir todos los lunes
        // se asume que cuando se llama este metodo, ya es LUNES, no se valida la fecha

        // enviar el correo al cliente
        $this->enviarCorreos('emails.peticionMaestra.GENERICA', [
            'subject' => 'Solicitud de Maestra PUC',
            'to' => $this->PUC_maestra_to,
            'bcc' => $this->SEI_nomina_bcc
        ], []);
    }

    private function procesarFCV(){
        $this->log("[PeticionMaestra] Cliente FCV: pedir maestra todos los dias LUNES y JUEVES...");
        // FCV  - pedir todos los LUNES y JUEVES
        // se asume que cuando se llama este metodo, ya es LUNES o JUEVES, no se valida la fecha

        // enviar el correo al cliente
        $this->enviarCorreos('emails.peticionMaestra.GENERICA', [
            'subject' => 'Solicitud de Maestra FCV',
            'to' => $this->FCV_maestra_to,
            'bcc' => $this->SEI_nomina_bcc
        ], []);
    }

    private function procesarCKY(){
        $this->log("[PeticionMaestra] Cliente CKY: pedir el dia habil anterior del inventario (si existe)");
        // CKY  - pedir el dia habil anterior del inventario

        // Carbon busca el dia habil siguiente (de la semana Viernes->Lunes)
        // documentacion: http://carbon.nesbot.com/docs/#api-addsub
        $sgteHabil = Carbon::now()->addWeekday()->toDateString();

        $totalInventarios = app('App\Http\Controllers\InventariosController')
            ->buscarInventarios($sgteHabil, $sgteHabil, null, 3, null, null)    // 3 = CKY
            ->count();
        if( $totalInventarios>0 ){
            $this->log("[PeticionMaestra] CKY tiene '$totalInventarios' inventarios programados para el siguiente dia habil ($sgteHabil)...");

            // enviar el correo al cliente
            $this->enviarCorreos('emails.peticionMaestra.GENERICA', [
                'subject' => 'Solicitud de Maestra CKY',
                'to' => $this->CKY_maestra_to,
                'bcc' => $this->SEI_nomina_bcc
            ], []);
        }else{
            $this->log("[PeticionMaestra] CKY no tiene inventarios programados para el siguiente dia habil ($sgteHabil), no se enviara el correo.");
        }
    }

    private function procesarCID(){
        // CID - pedir el mismo dia del inventario (si existe)
        $this->log("[PeticionMaestra] Cliente CID: pedir el mismo dia del inventario (si existe)");
        $hoy = Carbon::now()->toDateString();

        $totalInventarios = app('App\Http\Controllers\InventariosController')
            ->buscarInventarios($hoy, $hoy, null, 4, null, null)    // 4 = CID
            ->count();
        if( $totalInventarios>0 ) {
            $this->log("[PeticionMaestra] CID tiene '$totalInventarios' inventarios programados para hoy...");

            // enviar el correo al cliente
            $this->enviarCorreos('emails.peticionMaestra.GENERICA', [
                'subject' => 'Solicitud de Maestra CID',
                'to' => $this->CID_maestra_to,
                'bcc' => $this->SEI_nomina_bcc
            ], []);
        }else{
            $this->log("[PeticionMaestra] CID no tiene inventarios programados para hoy, no se enviara el correo.");
        }
    }

    private function procesarFSB(){
        $this->log("[PeticionMaestra] Cliente FSB: pedir el mismo dia del inventario (si existe)");
        $hoy = Carbon::now()->toDateString();

        // si "hoy" existe algun inventario programado para SB, entonces enviar un correo pidiendo la maestra
        $totalInventarios = app('App\Http\Controllers\InventariosController')
            ->buscarInventarios($hoy, $hoy, null, 5, null, null)    // 5 = FSB
            ->count();
        if( $totalInventarios>0 ){
            $this->log("[PeticionMaestra] FSB tiene '$totalInventarios' inventarios programados para hoy...");

            // enviar el correo al cliente
            $this->enviarCorreos('emails.peticionMaestra.GENERICA', [
                'subject' => 'Solicitud de Maestra FSB',
                'to' => $this->FSB_maestra_to,
                'bcc' => $this->SEI_nomina_bcc
            ], []);
        }else{
            $this->log("[PeticionMaestra] FSB no tiene inventarios programados para hoy, no se enviara el correo.");
        }
    }

    private function procesarCMT(){
        $this->log("[PeticionMaestra] Cliente CMT: pedir 2 dias habiles antes del inventario (si existe).");
        // CMT - pedir 2 dias habiles antes del inventario (si existe)

        // Carbon busca el 2 dias habiles despues del dia actual (de la semana Jueves->Lunes)
        // documentacion: http://carbon.nesbot.com/docs/#api-addsub
        $sgteHabil = Carbon::now()->addWeekday(2)->toDateString();

        $totalInventarios = app('App\Http\Controllers\InventariosController')
            ->buscarInventarios($sgteHabil, $sgteHabil, null, 7, null, null)    // 7 = CMT
            ->count();
        if( $totalInventarios>0 ){
            $this->log("[PeticionMaestra] CMT tiene '$totalInventarios' inventarios programados para el sub-siguiente dia habil ($sgteHabil)...");

            // enviar el correo al cliente
            $this->enviarCorreos('emails.peticionMaestra.GENERICA', [
                'subject' => 'Solicitud de Maestra CMT',
                'to' => $this->CMT_maestra_to,
                'bcc' => $this->SEI_nomina_bcc
            ], []);
        }else{
            $this->log("[PeticionMaestra] CMT no tiene inventarios programados para el sub-siguiente dia habil ($sgteHabil), no se enviara el correo.");
        }
    }
    
    // Exactamente el mismo metodo de InformarNominaACliente.php  
    private function enviarCorreos($plantilla, $datosCorreo, $datosVista){
        Mail::send($plantilla, $datosVista,
            function ($message) use($datosCorreo){
                $subject = $datosCorreo['subject'];
                $this->log("[prod] subject: $subject");

                $message
                    ->from('no-responder@plataforma.seiconsultores.cl', 'SEI Consultores')
                    ->subject($subject);
                if(App::environment('production')){
                    // enviar a los destinatarios
                    foreach($datosCorreo['to'] as $destinatario){
                        $message->to($destinatario[0], $destinatario[1]);
                        $this->log("[prod] enviando TO: $destinatario[0] - $destinatario[1]");
                    }
                    // enviar las copias ocultas
                    foreach($datosCorreo['bcc'] as $destinatario){
                        $message->bcc($destinatario[0], $destinatario[1]);
                        $this->log("[prod] enviando BCC: $destinatario[0] - $destinatario[1]");
                    }
                }else{
                    foreach($this->SEI_DESARROLLO as $destinatario){
                        $message->to($destinatario[0], $destinatario[1]);
                        $this->log("[dev] enviando TO: $destinatario[0] - $destinatario[1]");
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
