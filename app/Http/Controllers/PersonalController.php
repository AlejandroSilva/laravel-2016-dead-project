<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
// Modelos
use App\User;
// Permisos
use Auth;

class PersonalController extends Controller {

    // GET api/usuarios
    function api_getUsuarios(){
        // todo buscar lista de personal
        $personal = User::all();
        return response()->json($personal, 200);
    }

    // GET personal/nuevo
    function show_formulario(){

        return view('operacional.personal.usuarios');
    }

    // POST personal/nuevo
    function show_postFormulario(Request $request){
        $this->validate($request, [
            'RUN' => 'required|unique:users|max:15',
            'email' => 'required',
            'nombre1' => 'required|min:3|max:20',
            'nombre2' => 'required|min:3|max:20',
            'apellidoPaterno' => 'required|min:3|max:20',
            'apellidoMaterno' => 'required|min:3|max:20',
            'fechaNacimiento' => 'required',
        ]);

        $usuario = User::create([
            'RUN' => $request->RUN,
            'email' => $request->email,
            'nombre1' => $request->nombre1,
            'nombre2' => $request->nombre2,
            'apellidoPaterno' => $request->apellidoPaterno,
            'apellidoMaterno' => $request->apellidoMaterno,
            'fechaNacimiento' => $request->fechaNacimiento,
            'telefono1' => $request->telefono1,
            'telefono2' => $request->telefono2,
            'contratado' => false,
            'bloqueado' => false,
            'password' => 'sincontraseña',
        ]);
        
        $usuario->attachRole(4);   // operador por defecto
        return response()->json($usuario, 200);
    }

    // PUT api/usuario/{idUsuario}
    public function api_actualizar($idUsuario){
        return response()->json(['msg'=>'por implementar'], 501);
    }

    public function api_getRolesUsuario($idUsuario){
        $usuario = User::find($idUsuario);
        if($usuario){
            return response()->json($usuario->roles, 200);
        }else{
            return response()->json([], 404);
        }
    }
}
