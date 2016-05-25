<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response;
// Modelos
use Auth;
use App\Locales;
use App\Clientes;
use App\FormatoLocales;
use App\Jornadas;
use App\Comunas;
use App\Direcciones;
use App\User;

class LocalesController extends Controller {

    private $localesRules = [
        'idCliente' => 'required|max:10',
        'idFormatoLocal' => 'required|max:10',
        'idJornadaSugerida' => 'required|max:10',
        'numero' => 'required|unique:locales',
        'nombre' => 'required|unique:locales',
        'horaApertura' => 'required|date_format:H:i:s',
        'horaCierre' => 'required|date_format:H:i:s',
        'emailContacto' => 'max:50',
        'codArea1' => 'max:10',
        'telefono1' => 'max:20',
        'codArea2' => 'max:10',
        'telefono2' => 'max:20',
        'stock' => 'required|numeric|digits_between:1,11',
        'fechaStock' => 'date'
    ];

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

    /*public function api_getLocalVerbose($idLocal){
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
    }*/

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

    public function showClientes(){
        $user = Auth::user();
        if(!$user || !$user->can('programaLocales_ver'))
            return view('errors.403');
        
        $clientes = Clientes::all();
        
        return view('operacional.locales.mantenedorLocales', [
            'clientes' => $clientes
        ]);
    }

    public function api_getLocales($idCliente){
        $user = Auth::user();
        if(!$user || !$user->can('programaLocales_ver'))
            return view('errors.403');
        
        $formatoLocales = FormatoLocales::all()->sortBy('idFormatoLocal');
        $jornadas = Jornadas::all();
        $clientes = Clientes::all();
        $comunas = Comunas::all();
        $cliente = Clientes::find($idCliente);
        if($cliente){
            $locales = Locales::with(['direccion'])
                ->where('idCliente', '=', $idCliente)->paginate(15)
                ;
            return view('operacional.locales.locales', [
                'locales' => $locales,
                'formatoLocales' => $formatoLocales,
                'jornadas' => $jornadas,
                'clientes' => $clientes,
                'comunas' => $comunas,
                'user' => $user
            ]);
        }else{
            return response()->json([], 404);
        }
    }
    
    public function api_actualizarLocal($idLocal, Request $request){
        $user = Auth::user();
        if(!$user || !$user->can('programaLocales_modificar'))
            return view('errors.403');

        $local = Locales::find($idLocal);
        // tomar las reglas originales, y agregar la excepcion
        $localesRules = $this->localesRules;
        $localesRules["numero"] = "required|unique:locales,numero,$local->numero,numero";
        $localesRules["nombre"] = "required|unique:locales,nombre,$local->nombre,nombre";

        $validator= Validator::make(Input::all(), $localesRules);
        if($validator->fails()) {
            return Redirect::to("admin/cliente/$local->idCliente")->withErrors($validator,'error')->withInput();
            
        }else{
            $direccion = $local->direccion;
            
            if($local){

                if (isset($request->idCliente))
                    $local->idCliente = $request->idCliente;

                if (isset($request->idFormatoLocal))
                    $local->idFormatoLocal = $request->idFormatoLocal;

                if (isset($request->idJornadaSugerida))
                    $local->idJornadaSugerida = $request->idJornadaSugerida;

                if (isset($request->numero))
                    $local->numero = $request->numero;

                if (isset($request->nombre))
                    $local->nombre = $request->nombre;

                if (isset($request->horaApertura))
                    $local->horaApertura = $request->horaApertura;

                if (isset($request->horaCierre))
                    $local->horaCierre = $request->horaCierre;

                if (isset($request->emailContacto))
                    $local->emailContacto = $request->emailContacto;

                if (isset($request->codArea1))
                    $local->codArea1 = $request->codArea1;

                if (isset($request->codArea2))
                    $local->codArea2 = $request->codArea2;

                if (isset($request->telefono1))
                    $local->telefono1 = $request->telefono1;

                if (isset($request->telefono2))
                    $local->telefono2 = $request->telefono2;

                if (isset($request->stock))
                    $local->stock = $request->stock;

                if (isset($request->fechaStock))
                    $local->fechaStock = $request->fechaStock;

                if (isset($request->cutComuna))
                    $direccion->cutComuna = $request->cutComuna;

                if (isset($request->direccion))
                    $direccion->direccion = $request->direccion;

                $direccion->save();

                $local->save();

                return Redirect::to("admin/cliente/$local->idCliente");
            }else{
                return response()->json([], 404);
            }
        }
    }
    
    public function post_formulario(Request $request){
        $user = Auth::user();
        if(!$user || !$user->can('programaLocales_agregar'))
            return view('errors.403');

        $validator= Validator::make(Input::all(), $this->localesRules);
        if($validator->fails()){
            $idCliente = $request->idCliente;
            return Redirect::to("admin/cliente/$idCliente")->withErrors($validator);
        }else{
            $local = Locales::create( Input::all() );
            $idLocal = $local->idLocal;
        }
        $this->validate($request,
            [
                'cutComuna'=> 'required|min:3|max:10|',
                'direccion'=> 'max:150|'
            ]);
        $direccion = new Direcciones();
        $direccion->idLocal = $idLocal;
        $direccion->cutComuna = $request->cutComuna;
        $direccion->direccion = $request->direccion;
        $direccion->save();
        return Redirect::to("admin/cliente/$local->idCliente");
    }
}