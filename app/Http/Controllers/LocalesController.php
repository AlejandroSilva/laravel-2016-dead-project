<?php

namespace App\Http\Controllers;

use App\FormatoLocales;
use App\Jornadas;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
// Modelos
use Auth;
//use App\Locales;
use App\Clientes;

class LocalesController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET admin/locales
    function show_mantenedor(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->hasRole('Administrador'))
            return view('errors.403');

        $clientes = Clientes::all();
        $jornadas = Jornadas::all();
        $formatos = FormatoLocales::all();
        return view('operacional.locales.mantenedorLocales', [
            'clientes'=>$clientes,
            'jornadas'=>$jornadas,
            'formatoLocales'=>$formatos
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // GET api/locales/{idLocal}
    // Entrega la informacion de un local, sin sus relaciones
//    public function api_getLocal($idLocal){
//        $local = Locales::find($idLocal);
//        // si no existe retorna un objeto vacio con statusCode 404 (not found)
//        if(!$local)
//            return response()->json([], 404);
//        return response()->json($local);
//    }

    // GET api/locales/{idLocal}/verbose
    // Entrega la ifnormacion de un local, junto con sus relaciones
//    public function api_getLocalVerbose($idLocal){
//        $local = Locales::find($idLocal);
//
//        // si no existe retorna un objeto vacio con statusCode 404 (not found)
//        if(!$local){
//            return response()->json([], 404);
//        }
//
//        // incluir en el query el cliente
//        $local->cliente;
//
//        // incluir en el query la "direccion", "comuna", "region" y "zona"
//        $local->direccion->comuna->provincia->region->zona;
//
//        // incluir en el query el "formato de local" para conocer la "produccion sugerida"
//        $local->formatoLocal;
//
//        // incluir en el query la "jornada" por defecto del local
//        $local->jornada;
//
//        // -----------------------------------------------
//        // incluir en el objeto la "hora de llegada sugerida" y la "dotacion sugerida" del local
//        $localAsArray = $local->toArray();
//        // se modifican algunos campos para ser tratados mejor en el frontend
//        $localAsArray['nombreCliente'] = $local->cliente->nombreCorto;
//        $localAsArray['nombreComuna'] = $local->direccion->comuna->nombre;
//        $localAsArray['nombreProvincia'] = $local->direccion->comuna->provincia->nombre;
//        $localAsArray['nombreRegion'] = $local->direccion->comuna->provincia->region->numero;
//        //$localAsArray['horaLlegadaSugerida'] = $local->llegadaSugeridaLider();
////        $localAsArray['horaLlegadaSugeridaLiderDia'] = $local->llegadaSugeridaLiderDia();
////        $localAsArray['horaLlegadaSugeridaLiderNoche'] = $local->llegadaSugeridaLiderNoche();
//
//        // Calcular la dotacion sugerida
//        $localAsArray['dotacionSugerida'] = $local->dotacionSugerida();
//        return response()->json($localAsArray);
//    }
}