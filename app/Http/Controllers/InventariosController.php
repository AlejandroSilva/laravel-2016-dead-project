<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
// Modelos
use App\Clientes;
use App\Inventarios;
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

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // POST inventario/nuevo
    function api_crear(Request $request){
        $validator = Validator::make($request->all(), [
            // FK
            'idLocal'=> 'required',
            // 'idCliente'=> 'required', // ignorar
            'idJornada'=> 'required',
            // otros campos
            'fechaProgramada'=> 'required',
            'horaLlegada'=> 'required',
            'stockTeorico'=> 'required',
            'dotacionAsignada'=> 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'request'=> $request->all(),
                'errors'=> $validator->errors()
            ], 400);
//            return redirect('inventario/nuevo')->withInput()->withErrors($validator);
        }else{
            $inventario = new Inventarios();
            $inventario->idLocal = $request->idLocal;
            $inventario->idJornada = $request->idJornada;
            $inventario->fechaProgramada = $request->fechaProgramada;
            $inventario->horaLlegada = $request->horaLlegada;
            $inventario->stockTeorico = $request->stockTeorico;
            $inventario->dotacionAsignada = $request->dotacionAsignada;
            $resultado =  $inventario->save();

            if($resultado){
                return response()->json($inventario, 201);
            }else{
                return response()->json([
                    'request'=> $request->all(),
                    'errors'=> $validator->errors(),
                    'resultado'=>$resultado,
                    'inventario'=>$inventario
                ], 400);
            }
//            return view('operacional.inventario.inventario-nuevo-ok');
        }
    }

    function api_get($idInventario){
        $inventario = Inventarios::find($idInventario);
        if($inventario){
            return response()->json($inventario, 200);
        }else{
            return response()->json([], 404);
        }
    }

    function api_actualizar($idInventario, Request $request){
        $inventario = Inventarios::find($idInventario);
        // si no existe retorna un objeto vacio con statusCode 404 (not found)
        if($inventario){
            if(isset($request->idJornada))
                $inventario->idJornada = $request->idJornada;
            if(isset($request->fechaProgramada))
                $inventario->fechaProgramada = $request->fechaProgramada;
            if(isset($request->horaLlegada))
                $inventario->horaLlegada = $request->horaLlegada;
            if(isset($request->stockTeorico))
                $inventario->stockTeorico = $request->stockTeorico;
            if(isset($request->dotacionAsignada))
                $inventario->dotacionAsignada = $request->dotacionAsignada;

            $resultado =  $inventario->save();

            if($resultado) {
                return response()->json($inventario, 200);
            }else{
                return response()->json([
                    'request'=>$request->all(),
                    'resultado'=>$resultado,
                    'inventario'=>$inventario
                ], 400);
            }
        }else{
            return response()->json([], 404);
        }
    }
}
