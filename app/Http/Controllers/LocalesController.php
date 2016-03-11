<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
// Modelos
use App\Locales;
use Symfony\Component\HttpFoundation\Response;

class LocalesController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // GET api/locales/{idLocal}
    // Entrega la informacion de un local, sin sus relaciones
    public function api_getLocal($idLocal){
        $local = Locales::find($idLocal);
        // si no existe retorna un objeto vacio con statusCode 404 (not found)
        if(!$local)
            return response()->json([], 404);
        return response()->json($local);
    }

    // GET api/locales/{idLocal}/verbose
    // Entrega la ifnormacion de un local, junto con sus relaciones
    public function api_getLocalVerbose($idLocal){
        $local = Locales::find($idLocal);

        // si no existe retorna un objeto vacio con statusCode 404 (not found)
        if(!$local){
            return response()->json([], 404);
        }

        // incluir en el query el cliente
        $local->cliente;

        // incluir en el query la "direccion", "comuna", "region" y "zona"
        $local->direccion->comuna->provincia->region->zona;

        // incluir en el query el "formato de local" para conocer la "produccion sugerida"
        $local->formatoLocal;

        // incluir en el query la "jornada" por defecto del local
        $local->jornada;

        // -----------------------------------------------
        // incluir en el objeto la "hora de llegada sugerida" y la "dotacion sugerida" del local
        $localAsArray = $local->toArray();
        // ordenar campos para que sean mas accesibles
        $localAsArray['nombreCliente'] = $local->cliente->nombreCorto;
        $localAsArray['nombreComuna'] = $local->direccion->comuna->nombre;
        $localAsArray['nombreProvincia'] = $local->direccion->comuna->provincia->nombre;
        $localAsArray['nombreRegion'] = $local->direccion->comuna->provincia->region->numero;


        $localAsArray['horaLlegadaSugerida'] = $local->llegadaSugerida();

        // Calcular la dotacion sugerida
        $localAsArray['dotacionSugerida'] = $local->dotacionSugerida();
        return response()->json($localAsArray);
    }
}