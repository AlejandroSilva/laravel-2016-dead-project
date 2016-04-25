<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use App\Http\Requests;
// Modelos
use App\Nominas;
use App\Inventarios;
use App\Locales;

class NominasController extends Controller {

    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    
    function showNominas(){
        return view('operacional.nominas.nominas-index');
    }
    
    function showNominasFinales(){
        return view('operacional.nominasFinales.nominasFinales-index');
    }
    
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
                    'nominaNoche',
                    'nominaDia.lider',
                    'nominaNoche.lider',
                    'nominaDia.captador',
                    'nominaNoche.captador',
                ])->find($inventarioPadre->idInventario),
                200
            );

        }else{
            return response()->json([], 404);
        }
    }

    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA
     * ##########################################################
     */

    // POST api/nomina/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-disponible
    function api_informarDisponible($idCliente, $ceco, $annoMesDia, Request $request){
//        $fecha = explode('-', $annoMesDia);
//        $anno = $fecha[0];
//        $mes  = $fecha[1];

        // Buscar el Local (por idCliente y CECO)
        $local = Locales::where('idCliente', '=', $idCliente)
            ->where('numero', '=', $ceco)
            ->first();
        if($local) {
            // Buscar inventario
            $inventario = Inventarios::where('idLocal', '=', $local->idLocal)
                ->where('fechaProgramada', $annoMesDia)
                //->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
                //->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
                ->first();
            if($inventario) {
                // fijar la 'fechaSubidaNomina'
                $nominaDia = $inventario->nominaDia;
                $nominaNoche = $inventario->nominaNoche;
                // Si la fecha de subida ya habia sido fijada, no cambiar esa fecha
                // esto puede suceder cuando re-suben la nomina para corregir algun error
                if($nominaDia->fechaSubidaNomina=='0000-00-00'){
                    $nominaDia->fechaSubidaNomina = Carbon::now();
                    $nominaNoche->fechaSubidaNomina = Carbon::now();
                    $nominaDia->save();
                    $nominaNoche->save();
                    Log::info("[AUDITORIA:INFORMAR_REALIZADO:OK] idAuditoria '$inventario->idInventario' informada correctamente. ceco '$ceco', idCliente '$idCliente', mes '$annoMesDia'.");
                }else{
                    Log::info("[AUDITORIA:INFORMAR_REALIZADO:ERROR] idAuditoria '$inventario->idInventario' ya habia sido informada. ceco '$ceco', idCliente '$idCliente', mes '$annoMesDia'.");
                }

                return response()->json(Inventarios::with(['nominaDia', 'nominaNoche'])->find($inventario->idInventario), 200);
            }else {
                // inventario con esa fecha no existe
                $errorMsg = "no existe un inventario para el idLocal '$local->idLocal', idCliente '$idCliente' en el mes '$annoMesDia'";
                Log::info("[NOMINA:INFORMAR_REALIZADO:ERROR] $errorMsg");
                return response()->json(['msg' => $errorMsg], 404);
            }

        } else{
            // local de ese usuario, con ese ceco no existe
            $errorMsg = "no existe el CECO '$ceco' del idCliente '$idCliente'";
            Log::info("[NOMINA:INFORMAR_DISPONIBLE:ERROR] $errorMsg");
            return response()->json(['msg'=>$errorMsg], 404);
        }

        return response()->json(['msg'=>'falta por implementar'], 404);
    }
}
