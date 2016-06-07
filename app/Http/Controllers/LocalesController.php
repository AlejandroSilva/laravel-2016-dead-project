<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use Carbon\Carbon;
//use App\Http\Controllers\Controller;
//use Symfony\Component\HttpFoundation\Response;
// Modelos
use Auth;
use App\Clientes;
use App\Comunas;
use App\Direcciones;
use App\FormatoLocales;
use App\Jornadas;
use App\Locales;
use League\Flysystem\Adapter\Local;

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
        
        return view('operacional.locales.mantenedorLocales', [
            'clientes' => Clientes::all(),
            'jornadas' => Jornadas::all(),
            'formatoLocales' => FormatoLocales::all(),
            // Todo: las comunas deben salir ordenadas alfabeticamente
            'comunas' => Comunas::all()->map('\App\Comunas::formatearSimple')
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // GET api/cliente/{idCliente}/locales
    public function api_getLocales($idCliente){
        $cliente = Clientes::find($idCliente);
        if($cliente){
            return response()->json($cliente->locales->map('\App\Locales::formatoLocal_completo'), 200);
        }else{
            return response()->json([], 404);
        }
    }

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
            'numero' => "required|numeric|unique:locales,numero,NULL,id,idCliente,$request->idCliente",
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
        ],[
            'required' => 'Obligatorio.',
            'exists' => ':attribute no existe en la BD.',
            'unique' => 'Ya existe un local con esos datos.',
            'max' => 'Largo máximo :max.',
            'numeric' => 'Debe ser un numero.'
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

        return response()->json(Locales::formatoLocal_completo($local));
    }

    // POST api/local/{idLocal}
    function api_actualizar($idLocal, Request $request){
        // Verificar que el usuario tenga los permisos para crear un local
//        $user = Auth::user();
//        if(!$user || !$user->can('admin-mantenedorLocales'))
//            return view('errors.403');

        // No se puede cambiar el idCliente, ni el CECO

        // Local existe?
        $local = Locales::find($idLocal);
        if(!$local)
            return response()->json([
                'idLocal' => 'Local no encontrado'
            ]);

        // validar campos
        $actualizar = Validator::make($request->all(), [
            'idFormatoLocal' => 'sometimes|exists:formato_locales,idFormatoLocal',
            // jornada sugerida debe existir en la tabla
            'idJornadaSugerida' => 'sometimes|exists:jornadas,idJornada',
            //'nombre' => "sometimes|unique:locales,nombre,NULL,id,IdCliente,$request->idCliente",
            //'nombre' => "sometimes|unique:locales,nombre,NULL",
            'horaApertura' => 'sometimes|date_format:H:i',
            'horaCierre' => 'sometimes|date_format:H:i',
            'emailContacto' => 'sometimes|max:50',
            'codArea1' => 'sometimes|max:10',
            'telefono1' => 'sometimes|max:20',
            'codArea2' => 'sometimes|max:10',
            'telefono2' => 'sometimes|max:20',
            'stock' => 'sometimes|numeric|digits_between:1,11',
            'fechaStock' => 'sometimes|date',
            // En el mismo validator se revisa la direccion
            'cutComuna'=> 'sometimes|exists:comunas,cutComuna',
            'direccion'=> 'sometimes|max:150|'
        ]);
        $errors = $actualizar->errors();

        // Asignar valores (solo si estan fijados y son validos)
        // buscar si existe un local con el mismo cliente, y el mismo nombre (que no sea el mismo)
        $_localNombre = Locales::where('nombre', $request->nombre)
            ->where('idCliente', $local->idCliente)
            ->where('idLocal', '!=', $local->idLocal)->first();

        if(isset($request->nombre) && !$_localNombre)
            $local->nombre= $request->nombre;
        if(isset($request->idFormatoLocal) && !$errors->get('idFormatoLocal'))
            $local->idFormatoLocal = $request->idFormatoLocal;
        if(isset($request->idJornadaSugerida) && !$errors->get('idJornadaSugerida'))
            $local->idJornadaSugerida = $request->idJornadaSugerida;
        if(isset($request->emailContacto) && !$errors->get('emailContacto'))
            $local->emailContacto= $request->emailContacto;
        if(isset($request->telefono1) && !$errors->get('telefono1'))
            $local->telefono1 = $request->telefono1;
        if(isset($request->telefono2) && !$errors->get('telefono2'))
            $local->telefono2 = $request->telefono2;
        if(isset($request->stock) && !$errors->get('stock')){
            // al actualizar el stock de un local, hacerlo tambien con los inventarios asociados al local
            $ahora = Carbon::now()->format("Y-m-d h:i:s");
            $local->actualizarStock($request->stock, $ahora);
        }
        if(isset($request->direccion) && !$errors->get('direccion')){
            $local->direccion->direccion = $request->direccion;
            $local->direccion->save();
        }

        // Este metodo no arroja errores, si hay algun dato invalido lo ignora
        // esto para simplificar la tabla del mantenedor de locales, ya que no existe espacio suficiente
        // para mostrar mensajes de error.
        //Si hay un dato mal ingresado, el formulario simplemente muestra el valor valido anterior/fijado.
        $local->save();
        return response()->json(
            Locales::formatoLocal_completo( Locales::find($local->idLocal) ), 200
        );
    }
}