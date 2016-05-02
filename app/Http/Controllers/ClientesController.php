<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;

// Modelos
use App\Clientes;
use App\Locales;
use Symfony\Component\HttpKernel\Client;

class ClientesController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    // GET admin/clientes
    public function show_Lista(){
        $clientes = Clientes::all();
        
        return view('operacional.clientes.clientes', [
            'clientes' => $clientes
        ]);
    }
    
    public function api_get($idCliente){
        $cliente = Clientes::find($idCliente);
        if($cliente){
        return view('operacional.clientes.cliente', [
            'cliente'=>$cliente
        ]);
        }else{
            return response()->json([], 404);
        }
    }
    
    public function api_actualizar($idCliente, Request $request){

        $cliente = Clientes::find($idCliente);
        if($cliente) {
            if (isset($request->nombre)) {
                $cliente->nombre = $request->nombre;
            }
            if (isset($request->nombreCorto)) {
                $cliente->nombreCorto = $request->nombreCorto;
            }
            $cliente->save();
            return Redirect::to("clientes");

        }else{
            return response()->json([], 404);
        }
    }
    
    public function postFormulario(Request $request){
        $this->validate($request,
            [
                'nombre'=> 'required|min:3|max:20|unique:clientes',
                'nombreCorto'=> 'required|min:3|max:20|unique:clientes',
            ]);
        $cliente = new Clientes();
        $cliente->nombre = $request->nombre;
        $cliente->nombreCorto = $request->nombreCorto;
        $cliente->save();
        $cliente = Clientes::all();

        return view('operacional.clientes.clientes', [
            'clientes'=> $cliente
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // GET api/clientes
    public function api_getClientes(){
        return Clientes::all();
    }

    // GET api/cliente/{idCliente}/locales
    public function api_getLocales($idCliente){
        $cliente = Clientes::find($idCliente);
        if($cliente){
            $locales = Locales::with([
                'direccion'
            ])
            ->where('idCliente', '=', $idCliente)
            ->get();
            return response()->json($locales, 200);
        }else{
            return response()->json([], 404);
        }
    }

    // GET api/clientes/locales
    public function api_getClientesWithLocales(){
        return Clientes::allWithSimpleLocales();
    }
}
