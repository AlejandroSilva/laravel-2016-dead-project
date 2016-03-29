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
            // Dotacion de la Nomina
            if(isset($request->dotacionAsignada))
                $nomina->dotacionAsignada = $request->dotacionAsignada;
            // En el Lider, Supervisor, Captador1 y Captador 2 si la selecciona es '', se agrega un valor null al registro
            // Lider
            if(isset($request->idLider))
                $nomina->idLider = $request->idLider==''? null : $request->idLider;
            // Supervisor
            if(isset($request->idSupervisor))
                $nomina->idSupervisor = $request->idSupervisor==''? null : $request->idSupervisor;
            // Captador 1
            if(isset($request->idCaptador1))
                $nomina->idCaptador1 = $request->idCaptador1==''? null : $request->idCaptador1;
            // Captador 2
            if(isset($request->idCaptador2))
                $nomina->idCaptador2 = $request->idCaptador2==''? null : $request->idCaptador2;
            //  Dotacion Captador 1
            if(isset($request->dotacionCaptador1))
                $nomina->dotacionCaptador1 = $request->dotacionCaptador1;
            //  Dotacion Captador 2
            if(isset($request->dotacionCaptador2))
                $nomina->dotacionCaptador2 = $request->dotacionCaptador2;
            // Hora llegada Lider
            if(isset($request->horaPresentacionLider))
                $nomina->horaPresentacionLider = $request->horaPresentacionLider;
            // hora llegada Equipo
            if(isset($request->horaPresentacionEquipo))
                $nomina->horaPresentacionEquipo = $request->horaPresentacionEquipo;

            $nomina->save();

            // entregar la informacion completa del inventario al que pertenece esta nomina
            $inventarioPadre = $nomina->inventario1? $nomina->inventario1 : $nomina->inventario2;
            return response()->json(
                Inventarios::with([
                    'local.cliente',
                    'local.formatoLocal',
                    'local.direccion.comuna.provincia.region',
                    'nominaDia',
                    'nominaNoche'
                ])->find($inventarioPadre->idInventario),
                200
            );


        }else{
            return response()->json([], 404);
        }
    }
}
