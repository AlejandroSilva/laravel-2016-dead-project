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
use App\Nominas;

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
            //'horaLlegada'=> 'required',
            //'stockTeorico'=> 'required',
            //'dotacionAsignadaTotal'=> 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'request'=> $request->all(),
                'errors'=> $validator->errors()
            ], 400);
        }else{
            $local = Locales::find($request->idLocal);
            if(!$local){
                return response()->json([
                    'request'=> $request->all(),
                    'errors'=> 'local no encontrado / no existe'
                ], 404);
            }

            $inventario = new Inventarios();
            $inventario->idLocal = $request->idLocal;
            $inventario->idJornada = $request->idJornada;
            $inventario->fechaProgramada = $request->fechaProgramada;
            $inventario->dotacionAsignadaTotal = $request->dotacionAsignadaTotal;
            // todo $inventario->fechaStock = $request->fechaStock;
            // todo $inventario->stockTeorico = $request->stockTeorico;

            // Crear las dos nominas
            $nominaDia = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaDia->horaPresentacionLider = $local->llegadaSugeridaLider();
            $nominaDia->horaPresentacionEquipo = $local->llegadaSugeridaPersonal();
            // Todo: la dotacion sugerida deberia dividirse en dos cuando la jornada sea doble:
            $nominaDia->dotacionAsignada = $local->dotacionSugerida();
            $nominaDia->dotacionCaptador1 = 0;
            $nominaDia->dotacionCaptador2 = 0;
            $nominaDia->horaTermino = '';
            $nominaDia->horaTerminoConteo = '';
            $nominaDia->save();

            $nominaNoche = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaNoche->horaPresentacionLider = $local->llegadaSugeridaLider();
            $nominaNoche->horaPresentacionEquipo = $local->llegadaSugeridaPersonal();
            // Todo: la dotacion sugerida deberia dividirse en dos cuando la jornada sea doble:
            $nominaNoche->dotacionAsignada = $local->dotacionSugerida();
            $nominaNoche->dotacionCaptador1 = 0;
            $nominaNoche->dotacionCaptador2 = 0;
            $nominaNoche->horaTermino = '';
            $nominaNoche->horaTerminoConteo = '';
            $nominaNoche->save();

            $inventario->nominaDia()->associate($nominaDia);
            $inventario->nominaNoche()->associate($nominaNoche);

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
            if(isset($request->fechaProgramada))
                $inventario->fechaProgramada = $request->fechaProgramada;
            if(isset($request->dotacionAsignadaTotal))
                $inventario->dotacionAsignadaTotal = $request->dotacionAsignadaTotal;
            if(isset($request->idJornada))
                $inventario->idJornada = $request->idJornada;

            $resultado = $inventario->save();

            if($resultado) {
                // mostrar el dato tal cual como esta en la BD
                return response()->json(
                    Inventarios::with([
                        'local.cliente',
                        'local.formatoLocal',
                        'local.direccion.comuna.provincia.region',
                        'nominaDia',
                        'nominaNoche'
                    ])->find($inventario->idInventario),
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
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche'
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