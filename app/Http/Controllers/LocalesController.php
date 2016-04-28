<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
// Modelos
use Auth;
use App\Locales;
use App\Clientes;
use App\FormatoLocales;
use App\Jornadas;

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
        // se modifican algunos campos para ser tratados mejor en el frontend
        $localAsArray['nombreCliente'] = $local->cliente->nombreCorto;
        $localAsArray['nombreComuna'] = $local->direccion->comuna->nombre;
        $localAsArray['nombreProvincia'] = $local->direccion->comuna->provincia->nombre;
        $localAsArray['nombreRegion'] = $local->direccion->comuna->provincia->region->numero;
        //$localAsArray['horaLlegadaSugerida'] = $local->llegadaSugeridaLider();
//        $localAsArray['horaLlegadaSugeridaLiderDia'] = $local->llegadaSugeridaLiderDia();
//        $localAsArray['horaLlegadaSugeridaLiderNoche'] = $local->llegadaSugeridaLiderNoche();

        // Calcular la dotacion sugerida
        $localAsArray['dotacionSugerida'] = $local->dotacionSugerida();
        return response()->json($localAsArray);
    }

    public function api_getFormatos(){
        $formatos = FormatoLocales::all();
        return view('operacional.locales.formatos', [
            'formatos' => $formatos
        ]);
    }
    
    public function postFormulario(Request $request){
        $this->validate($request,
            [   //unique:formato_locales-----> el nombre formato_locales debe ser igual a la tabla de la base de datos
                'nombre'=> 'required|min:2|max:40|unique:formato_locales',
                'siglas'=> 'required|min:2|max:10|unique:formato_locales',
                'produccionSugerida'=> 'required',
                'descripcion',
            ]);

        $formato = new FormatoLocales();
        $formato->nombre = $request->nombre;
        $formato->siglas = $request->siglas;
        $formato->produccionSugerida = $request->produccionSugerida;
        $formato->descripcion = $request->descripcion;

        $formato->save();
        
        $formato = FormatoLocales::all();
        return view('operacional.locales.formatos', [
            'formatos' => $formato
        ]);
    }

    public function api_get($idFormato){
        $formato = FormatoLocales::find($idFormato);
        if($formato){
            return view('operacional.locales.formato', [
                'formato'=>$formato
            ]);

        }else{
            return response()->json([], 404);
        }
    }

    public function api_actualizar($idFormato, Request $request){
        $formato = FormatoLocales::find($idFormato);
        if($formato) {
            if (isset($request->nombre)) {
                $formato->nombre = $request->nombre;
            }
            if (isset($request->siglas)) {
                $formato->siglas = $request->siglas;
            }
            if (isset($request->produccionSugerida)) {
                $formato->produccionSugerida = $request->produccionSugerida;
            }
            $formato->descripcion = $request->descripcion;

            $formato->save();
            return Redirect::to("formatoLocales");

        }else{
            return response()->json([], 404);
        }
    }
}