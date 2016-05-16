<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use App\Http\Requests;
// Modelos
use App\Comunas;
use App\Inventarios;
use App\Locales;
use App\Nominas;
use App\User;

class NominasController extends Controller {

    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    // GET programacionIG/nomina/{idNomina}
    function show_nomina($idNomina){
        // Todo: agregar seguriddad, solo para usuarios con permisos
//        $user = Auth::user();
//        if(!$user || !$user->can('programaAuditorias_ver'))
//            return view('errors.403');

        $nomina = Nominas::find($idNomina);
        if(!$nomina){
            return view('errors.errorConMensaje', [
                'titulo' => 'Nomina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);
        }

        // Buscar el inventario al que pertenece (junto con el cliente, el formato de local y la direccion
        $_inventario = $nomina->inventario1? $nomina->inventario1 : $nomina->inventario2;
        $inventario = Inventarios::find($_inventario->idInventario);

        return view('operacional.nominas.nomina', [
            'nomina' => Nominas::formatearConLiderCaptadorDotacion($nomina),
            'inventario' => Inventarios::formatoClienteFormatoRegion($inventario),
            'comunas' => Comunas::all()
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

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

    // GET api/nomina/{idNomina}/dotacion
    function api_get($idNomina){
        $nomina = Nominas::find($idNomina);

        return $nomina?
            response()->json(Nominas::formatearConLiderCaptadorDotacion($nomina))
            :
            response()->json([], 404);
    }

    // POST api/nomina/{idNomina}/operador/{operadorRUN}
    function api_agregarOperador($idNomina, $operadorRUN, Request $request){
        // Todo: El usuario tiene los permisos para agregar un usuario a una nomina?

        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json('Nomina no encontrada', 404);

        // el operador existe? se entrega un 204 y en el frontend se muestra un formulario
        $operador = User::where('usuarioRUN', $operadorRUN)->first();
        if(!$operador)
            return response()->json('', 204);

        // Si el operador ya esta en la nomina, no hacer nada y devolver la lista como esta
        $operadorExiste = $nomina->usuarioEnDotacion($operador);
        if($operadorExiste)
            return response()->json(Nominas::formatearDotacion($nomina), 200);

        // Todo: trabajar este dato
        // Si la dotacion esta completa, no hacer nada y retornar el error
        if(sizeof($nomina->dotacion) >= $nomina->dotacionAsignada)
            return response()->json('Ha alcanzado el maximo de dotacion', 400);
        if($request->esTitular==true){
            // No hay problemas en este punto, agregar usuario y retornar la dotacion
            $nomina->dotacion()->save($operador, ['titular'=>true]);
        }else{
            // No hay problemas en este punto, agregar usuario y retornar la dotacion
            $nomina->dotacion()->save($operador, ['titular'=>false]);
        }

        // se debe actualizar la dotacion
        $nominaActualizada = Nominas::find($nomina->idNomina);
        return response()->json(Nominas::formatearDotacion($nominaActualizada), 201);
    }

    // DELETE api/nomina/{idNomina}/operador/{operadorRUN}
    function api_quitarOperador($idNomina, $operadorRUN){
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json('Nomina no encontrada', 404);

        // el operador existe?
        $operador = User::where('usuarioRUN', $operadorRUN)->first();
        if(!$operador)
            return response()->json('Operador no encontrado', 404);

        $nomina->dotacion()->detach($operador);

        return response()->json(Nominas::formatearDotacion($nomina), 200);
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
                    Log::info("[NOMINA:INFORMAR_REALIZADO:OK] CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia' (idInventario '$inventario->idInventario') informado correctamente.");
                }else{
                    Log::info("[NOMINA:INFORMAR_REALIZADO:ERROR] CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia' (idInventario '$inventario->idInventario') ya habia sido informado.");
                }

                return response()->json(Inventarios::with(['nominaDia', 'nominaNoche'])->find($inventario->idInventario), 200);
            }else {
                // inventario con esa fecha no existe
                $errorMsg = "CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia'; no existe un inventario programado para el idLocal '$local->idLocal' en esa fecha.";
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

    function api_informarDisponible2($idCliente, $ceco, $annoMesDia, $annoMesDia2){
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
                    $nominaDia->fechaSubidaNomina = $annoMesDia2;
                    $nominaNoche->fechaSubidaNomina = $annoMesDia2;
                    $nominaDia->save();
                    $nominaNoche->save();
                    Log::info("[NOMINA:INFORMAR_REALIZADO:OK] CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia' (idInventario '$inventario->idInventario') informado correctamente.");
                }else{
                    Log::info("[NOMINA:INFORMAR_REALIZADO:ERROR] CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia' (idInventario '$inventario->idInventario') ya habia sido informado.");
                }

                return response()->json(Inventarios::with(['nominaDia', 'nominaNoche'])->find($inventario->idInventario), 200);
            }else {
                // inventario con esa fecha no existe
                $errorMsg = "CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia'; no existe un inventario programado para el idLocal '$local->idLocal' en esa fecha.";
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
