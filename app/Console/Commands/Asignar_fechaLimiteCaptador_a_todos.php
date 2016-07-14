<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
// Model
use App\Inventarios;
use Log;

class Asignar_fechaLimiteCaptador_a_todos extends Command {

    protected $signature = 'tool:asignar_fechaLimiteCaptador';

    protected $description = 'Command description';

    public function __construct() {
        parent::__construct();
    }
    public function handle(){

        Inventarios::with([])
            // los primeros meses no tienen datos en la tabla DiasHabiles, genera error, por eso se omite....
            ->where('fechaProgramada', '>=', '2016-05-00')
            ->each(function($inventario){
            // fecha limite del envio de la nomina por parte del Captador
            $fechaLimite = Inventarios::calcularFechaLimiteCaptador($inventario->fechaProgramada);

            $inventario->nominaDia->fechaLimiteCaptador = $fechaLimite;
            $inventario->nominaDia->save();
            $inventario->nominaNoche->fechaLimiteCaptador = $fechaLimite;
            $inventario->nominaNoche->save();
        });
    }
}
