<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
// Modelos
use App\Clientes;
use App\Locales;

class InventariosController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET inventario
    function index(){
        return view('operacional.inventario.inventario-index');
    }

    // GET inventario/lista
    function lista(){
        return view('operacional.inventario.inventario-lista');
    }

    // GET inventario/nuevo
    function nuevo(){
        $clientesWithLocales = Clientes::allWithSimpleLocales();
        return view('operacional.inventario.inventario-nuevo', [
            'clientes' => $clientesWithLocales
        ]);
    }

    // POST inventario/nuevo
    function postNuevo(Request $request){
        $validator = Validator::make($request->all(), [
            // FK
            'idLocal'=> 'required',
            // 'idCliente'=> 'required', // ignorar
            'idJornada'=> 'required',
            // otros campos
            'fechaProgramada'=> 'required',
            'horaLlegada'=> 'required',
            'stockTeorico'=> 'required',

        ]);
        if($validator->fails()){
            return [
                'request'=> $request->all(),
                'errors'=> $validator->errors()
            ];
//            return redirect('inventario/nuevo')->withInput()->withErrors($validator);
        }else{
            return view('operacional.inventario.inventario-nuevo-ok');
        }
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */
}
