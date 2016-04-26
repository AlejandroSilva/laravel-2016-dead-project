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
        'emailSEI' => 'max:60',
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
        $personal = User::all()->sortBy('id');
        return response()->json($personal, 200);
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
            return response()->json($usuario, 200);
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
}
