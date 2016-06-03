<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Direcciones;
use App\FormatoLocales;
use App\Jornadas;
use App\Locales;
use App\Http\Requests;
//use App\Http\Controllers\Controller;
//use Symfony\Component\HttpFoundation\Response;
// Modelos
use Auth;
//use App\Locales;
use App\Clientes;
use App\Comunas;

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
        if(!$user || !$user->can('admin-mantenedorLocales'))
            return view('errors.403');

        $clientes = Clientes::all();
        $jornadas = Jornadas::all();
        $formatos = FormatoLocales::all();
        return view('operacional.locales.mantenedorLocales', [
            'clientes'=>$clientes,
            'jornadas'=>$jornadas,
            'formatoLocales'=>$formatos,
            'comunas' => Comunas::all()->map('\App\Comunas::formatearSimple')
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // POST api/locales
    function api_nuevo(Request $request){
        // Verificar que el usuario tenga los permisos para crear un local
        $user = Auth::user();
        if(!$user || !$user->can('admin-mantenedorLocales'))
            return view('errors.403');

        // Validar que el local sea valido
        $crearLocal = Validator::make($request->all(), [
            'idCliente' => 'required|max:10',
            // formato local debe existir en la tabla
            'idFormatoLocal' => 'required|exists:formato_locales,idFormatoLocal',
            // jornada sugerida debe existir en la tabla
            'idJornadaSugerida' => 'required|exists:jornadas,idJornada',
            // valida que el numero y el nombre sean unicos, pero solo para el cliente indicado
                                //unique:table,column,except,idColumn
            'numero' => "required|unique:locales,numero,NULL,id,idCliente,$request->idCliente",
            'nombre' => "required|unique:locales,nombre,NULL,id,IdCliente,$request->idCliente",
            'horaApertura' => 'required|date_format:H:i',
            'horaCierre' => 'required|date_format:H:i',
            'emailContacto' => 'sometimes|max:50',
            'codArea1' => 'sometimes|max:10',
            'telefono1' => 'sometimes|max:20',
            'codArea2' => 'sometimes|max:10',
            'telefono2' => 'sometimes|max:20',
            'stock' => 'required|numeric|digits_between:1,11',
            'fechaStock' => 'required|date',
            // En el mismo validator se revisa la direccion
            'cutComuna'=> 'required|exists:comunas,cutComuna',
            'direccion'=> 'required|max:150|'
        ]);
        if($crearLocal->fails())
            return response()->json($crearLocal->errors(), 400);
        
        // Crear el Local y la direccion
        $local = Locales::create( $request->all() );
        $direccion = new Direcciones([
            'idLocal'=> $local->idLocal,
            'direccion' => $request->direccion,
            'cutComuna' => $request->cutComuna
        ]);
        $direccion->save();

        return response()->json(Locales::formatearConClienteFormatoDireccionRegion($local));
    }


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