<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
// Modelos
use App\User;
// Permisos
use Auth;

class PersonalController extends Controller {
    private $userRules = [
        'usuarioRUN' => 'required|unique:users|max:15',
        'usuarioDV' => 'required|max:1',
        'email' => 'max:60',
        'emailPersonal' => 'max:60',
        'nombre1' => 'required|min:3|max:20',
        'nombre2' => 'required|min:3|max:20',
        'apellidoPaterno' => 'required|min:3|max:20',
        'apellidoMaterno' => 'required|min:3|max:20',
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
    // GET personal/nuevo
    function show_formulario(){
        return view('operacional.personal.usuarios');
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */
    // GET api/usuario/buscar
    function api_buscar(){
        // agrega cabeceras para las peticiones con CORS
        header('Access-Control-Allow-Origin: *');
        
        $arrayUsuarios = User::with(['comuna.provincia.region', 'roles'])
            ->get()
            ->sortBy('id')
            ->toArray();

        // se parsean los usuarios con el formato "estandar"
        $arrayUsuarios_formato = array_map( [$this, 'darFormatoUsuario'], $arrayUsuarios );
        return response()->json($arrayUsuarios_formato, 200);
    }

    // POST api/usuario/nuevo
    function api_nuevo(){
        // agrega cabeceras para las peticiones con CORS
        header('Access-Control-Allow-Origin: *');

        $validator = Validator::make(Input::all(), $this->userRules);
        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }else{
            $usuario = User::create( Input::all() );
            // se parsea el usuario con el formato "estandar"
            $usuario_db = User::with(['comuna.provincia.region', 'roles'])->find($usuario->id);
            $usuario_formato = $this->darFormatoUsuario( $usuario_db );
            return response()->json($usuario_formato, 200);
        }
    }

    // PUT api/usuario/{idUsuario}
    public function api_actualizar($idUsuario){
        return response()->json(['msg'=>'por implementar'], 501);
    }

    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA
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
            'roles' => sizeof($user['roles'])>0?
                array_map(function($role){
                    return $role['name'];
                }, $user['roles'])
                :
                []
        ];
    }
}
