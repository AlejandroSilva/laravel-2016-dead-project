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

    // Entrega la informacion de un local, sin sus relaciones
    public function getLocal_json($idLocal){
        $local = Locales::find($idLocal);
        // si no existe retorna un objeto vacio con statusCode 404 (not found)
        if(!$local)
            return response()->json([], 404);
        return response()->json($local);
    }

    // Entrega la ifnormacion de un local, junto con sus relaciones
    public function getLocalVerbose_json($idLocal){
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
        $local->formatolocal;

        // incluir en el query la "jornada" por defecto del local
        $local->jornada;

        // incluir en el objeto la "hora de llegada sugerida" del local
        $localAsArray = $local->toArray();
        $localAsArray['horaLlegadaSugerida'] = $local->llegadaSugerida();

        return response()->json($localAsArray);
    }
}