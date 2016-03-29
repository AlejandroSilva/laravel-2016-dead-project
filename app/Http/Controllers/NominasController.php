<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Nominas;
use App\Inventarios;

class NominasController extends Controller {

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // PUT api/nomina/{idNomina}
    function api_get($idNomina){
        $nomina = Nominas::find($idNomina);
        $inventario = $nomina->inventario1? $nomina->inventario1 : $nomina->inventario2;
        return response()->json(
            $inventario
        );
    }

    // PUT api/nomina/{idNomina}
    function api_actualizar($idNomina, Request $request){
        // identificar la nomina indicada
        $nomina = Nominas::find($idNomina);
        if($nomina){
            // Actualizar con los datos entregados
            if(isset($request->dotacionAsignada))
                $nomina->dotacionAsignada = $request->dotacionAsignada;
            if(isset($request->idLider))
                $nomina->idLider = $request->idLider;
            if(isset($request->idSupervisor))
                $nomina->idSupervisor = $request->idSupervisor;
            $nomina->save();

            // entregar la informacion completa del inventario al que pertenece esta nomina
            $inventario = $nomina->inventario1? $nomina->inventario1 : $nomina->inventario2;
            return response()->json(
                Inventarios::with([
                    'local.cliente',
                    'local.formatoLocal',
                    'local.direccion.comuna.provincia.region',
                    'nominaDia',
                    'nominaNoche'
                ])->find($inventario->idInventario),
                200
            );


        }else{
            return response()->json([], 404);
        }
    }
}
