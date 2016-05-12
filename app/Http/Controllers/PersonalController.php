<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Log;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
// Modelos
use App\User;
use App\Role;
// Permisos
use Auth;

class PersonalController extends Controller {
    private $userRules = [
        'usuarioRUN' => 'required|unique:users|max:15',
        'usuarioDV' => 'required|max:1',
        'email' => 'max:60',
        'emailPersonal' => 'max:60',
        'nombre1' => 'required|min:3|max:20',
        'nombre2' => 'max:20',
        'apellidoPaterno' => 'required|min:3|max:20',
        'apellidoMaterno' => 'max:20',
        'fechaNacimiento' => 'required',
        'telefono' => 'max:20',
        'telefonoEmergencia' => 'max:20',
        'direccion' => 'max:150',
        'cutComuna' => 'required|exists:comunas,cutComuna',
        'tipoContrato' => 'max:30',
        'fechaInicioContrato' => 'date',
        'fechaCertificadoAntecedentes' => 'date',
        'banco' => 'max:30',
        'tipoCuenta' => 'max:30',
        'numeroCuenta' => 'max:20',
    ];

    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET personal/nuevo (SIN USO
//    function show_formulario(){
//        return view('operacional.personal.usuarios');
//    }


    /**
     * ##########################################################
     * Rutas para consumo del API REST (CON autentificacion)
     * ##########################################################
     */
    // GET api/usuario/buscar
    function api_buscar(Request $request){
        $query = User::withRegionRoles();

        // buscar por RUN?
        $run = $request->query('run');
        if(isset($run)){
            $query->where('usuarioRUN', $run);
        }
        
        $arrayUsuarios = $query->get()
            ->sortBy('id')
            ->toArray();

        // se parsean los usuarios con el formato "estandar"
        $arrayUsuarios_formato = array_map( [$this, 'darFormatoUsuario'], $arrayUsuarios );
        return response()->json($arrayUsuarios_formato, 200);
    }

    // POST api/usuario/nuevo-operador
    function api_nuevoOperador(){
        // Todo: solo algunos usuarios pueden agregar operadores (crear un permiso?)
        $usuarioAuth = Auth::user();

        $validator = Validator::make(Input::all(), $this->userRules);
        if($validator->fails()){
            $error = $validator->messages();
            Log::info("[USUARIO:NUEVO_OPERADOR:ERROR] Usuario:'$usuarioAuth->nombre1 $usuarioAuth->apellidoPaterno'($usuarioAuth->id)'. Validador: $error");

            return response()->json($error, 400);
        }else{
            $usuario = User::create( Input::all() );
            $rolOperador = Role::where('name', 'Operador')->first();
            $usuario->attachRole($rolOperador);     // attach llama a save (?)

            Log::info("[USUARIO:NUEVO_OPERADOR:OK] Usuario:'$usuarioAuth->nombre1 $usuarioAuth->apellidoPaterno'($usuarioAuth->id)' ha creado:'$usuario->nombre1 $usuario->apellidoPaterno'($usuario->id)'");

            // se parsea el usuario con el formato "estandar"
            $usuario_db = User::withRegionRoles()->find($usuario->id);
            $usuario_formato = $this->darFormatoUsuario( $usuario_db->toArray() );
            return response()->json($usuario_formato, 200);
        }
    }

    // PUT api/usuario/{idUsuario}
    public function api_actualizar($idUsuario){
        return response()->json(['msg'=>'por implementar'], 501);
    }

    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA (publicas: SIN autentificacion)
     * ##########################################################
     */
    // GET /api/usuario/{idUsuario}/roles
    public function api_getRolesUsuario($idUsuario){
        $usuario = User::find($idUsuario);
        if($usuario){
            return response()->json($usuario->roles, 200);
        }else{
            return response()->json([], 404);
        }
    }

    /**
     * ##########################################################
     * funciones privadas
     * ##########################################################
     */

    private function darFormatoUsuario($user){
        return [
            // datos personales
            'id' => $user['id'],
            'nombre1' => $user['nombre1'],
            'nombre2' => $user['nombre2'],
            'apellidoPaterno' => $user['apellidoPaterno'],
            'apellidoMaterno' => $user['apellidoMaterno'],
            'telefono' => $user['telefono'],
            'telefonoEmergencia' => $user['telefonoEmergencia'],
            'fechaNacimiento' => $user['fechaNacimiento'],
            'usuarioRUN' => $user['usuarioRUN'],
            'usuarioDV' => $user['usuarioDV'],
            'email' => $user['email'],
            'emailPersonal' => $user['emailPersonal'],
            // Contrato
            'tipoContrato' => $user['tipoContrato'],
            'fechaInicioContrato' => $user['fechaInicioContrato'],
            'fechaCertificadoAntecedentes' => $user['fechaCertificadoAntecedentes'],
            // Datos bancarios
            'banco' => $user['banco'],
            'tipoCuenta' => $user['tipoCuenta'],
            'numeroCuenta' => $user['numeroCuenta'],
            // Direccion
            'direccion' => $user['direccion'],
            'comuna' => $user['comuna']['nombre'],
            'provincia' => $user['comuna']['provincia']['nombre'],
            'region' => $user['comuna']['provincia']['region']['nombre'],
            // si "roles" es un arreglo vacio, array_map lanza un error
//            'roles' => sizeof($user['roles'])>0?
//                array_map(function($role){
//                    return $role['name'];
//                }, $user['roles'])
//                :
//                []
            'roles' => array_map( [$this, 'darFormatoRole'], $user['roles'] )
        ];
    }

    private function darFormatoRole($role){
        return [
            'id' => $role['id'],
            'name' => $role['name'],
            'description' => $role['description'],
        ];
    }
}
