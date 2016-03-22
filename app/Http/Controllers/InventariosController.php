<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
// Modelos
use App\Clientes;
use App\Inventarios;

class InventariosController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET inventario
    function showIndex(){
        return view('operacional.inventario.inventario-index');
    }

    // GET inventario/lista
    function showLista(){
        return view('operacional.inventario.inventario-lista');
    }

    // GET inventario/nuevo
    function showNuevo(){
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

    // POST api/inventario/nuevo
    function api_nuevo(Request $request){
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

    // GET api/inventario/{idInventario}
    function api_get($idInventario){
        $inventario = Inventarios::find($idInventario);
        if($inventario){
            return response()->json($inventario, 200);
        }else{
            return response()->json([], 404);
        }
    }

    // PUT api/inventario/{idInventario}
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
                // mostrar el dato tal cual como esta en la BD
                return response()->json(
                    Inventarios::find($inventario->idInventario),
                    200);
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

    // GET api/inventario/mes/{annoMesDia}
    function api_getPorMes($annoMesDia){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        return response()->json(
            Inventarios::   //\DB::table('inventarios')
                whereRaw("extract(year from fechaProgramada) = ?", [$anno])
                ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
                ->get()
        , 200);
    }
    // GET api/inventario/{fecha1}/al/{fecha2}
    function api_getPorRango($annoMesDia1, $annoMesDia2){
        $inventarios = Inventarios::with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region'
        ])
            ->where('fechaProgramada', '>=', $annoMesDia1)
            ->where('fechaProgramada', '<=', $annoMesDia2)
            ->get();

        // se modifican algunos campos para ser tratados mejor en el frontend
        $inventariosMod = array_map(function($inventario){
            $local = $inventario['local'];
            $local['nombreCliente'] = $local['cliente']['nombreCorto'];
            $local['nombreComuna'] = $local['direccion']['comuna']['nombre'];
            $local['nombreProvincia'] = $local['direccion']['comuna']['provincia']['nombre'];
            $local['nombreRegion'] = $local['direccion']['comuna']['provincia']['region']['numero'];
            $inventario['local'] = $local;

            return $inventario;
        }, $inventarios->toArray());

        return response()->json($inventariosMod, 200);
    }

}