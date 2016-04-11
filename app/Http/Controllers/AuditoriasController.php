<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Auditorias;
use App\Locales;

class AuditoriasController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */


    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // POST api/auditoria/nuevo
    function api_nuevo(Request $request){
        $validator = Validator::make($request->all(), [
            // FK
            'idLocal'=> 'required',
            'fechaProgramada'=> 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'request'=> $request->all(),
                'this.props.auditoria.errors'=> $validator->errors()
            ], 400);
        }else{
            $local = Locales::find($request->idLocal);
            if(!$local){
                return response()->json([
                    'request'=> $request->all(),
                    'errors'=> 'local no encontrado / no existe'
                ], 404);
            }

            $auditoria = new Auditorias();
            $auditoria->idLocal = $request->idLocal;
            $auditoria->fechaProgramada = $request->fechaProgramada;
            $auditoria->horaPresentacionAuditor = $local->llegadaSugeridaLiderDia();


            $resultado =  $auditoria->save();

            if($resultado){
                return response()->json(
                    $auditoria = Auditorias::with([
                        'local.cliente',
                        'local.direccion.comuna.provincia.region'
                    ])->find($auditoria->idAuditoria)
                    , 201);
            }else{
                return response()->json([
                    'request'=> $request->all(),
                    'errors'=> $validator->errors(),
                    'resultado'=>$resultado,
                    'auditoria'=>$auditoria
                ], 400);
            }
        }
    }

    // GET api/auditoria/mes/{annoMesDia}
    function api_getPorMes($annoMesDia){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        return response()->json(
            Auditorias::   //\DB::table('auditorias')
            with([
                'local.cliente',
                'local.direccion.comuna.provincia.region'
            ])
                ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
                ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
                ->get()
            , 200);
    }

    // PUT api/auditorias/{idAuditoria}
    function api_actualizar($idAuditoria, Request $request){
        $auditoria = Auditorias::find($idAuditoria);
        // si no existe retorna un objeto vacio con statusCode 404 (not found)
        if($auditoria){
            if(isset($request->fechaProgramada))
                $auditoria->fechaProgramada = $request->fechaProgramada;
            
            // actualizar auditor
            if(isset($request->idAuditor))
                $auditoria->idAuditor = $request->idAuditor==0? null: $request->idAuditor;
            // actualizar hora de presentacion de auditor
            if(isset($request->horaPresentacionAuditor))
                $auditoria->horaPresentacionAuditor = $request->horaPresentacionAuditor==0? null: $request->horaPresentacionAuditor;

            $resultado = $auditoria->save();

            if($resultado) {
                // mostrar el dato tal cual como esta en la BD
                return response()->json(
                    Auditorias::with([
                        'local.cliente',
                        'local.direccion.comuna.provincia.region'
                    ])->find($auditoria->idAuditoria),
                    200);
            }else{
                return response()->json([
                    'request'=>$request->all(),
                    'resultado'=>$resultado,
                    'auditoria'=>$auditoria
                ], 400);
            }
        }else{
            return response()->json([], 404);
        }
    }

    // GET api/auditoria/{fecha1}/al/{fecha2}/cliente/{idCliente}
    function api_getPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente){
        $query = Auditorias::with([
            'local.cliente',
            'local.direccion.comuna.provincia.region'
        ])
            ->where('fechaProgramada', '>=', $annoMesDia1)
            ->where('fechaProgramada', '<=', $annoMesDia2);

        if($idCliente==0){
            // No se realiza un filtro por clientes
            $auditorias = $query->get();

            //**** TODO TEMPORAL; ELIMINAR
            $auditorias2 = array_map(function($auditoria) {
                $local = Locales::find($auditoria['idLocal']);
                $auditoria['HORAAUDITOR'] = $local->llegadaSugeridaLiderDia();
                return $auditoria;
            }, $auditorias->toArray());
            return response()->json($auditorias2, 200);
            ///********************
            
//            return response()->json($auditorias->toArray(), 200);
        }
        else{
            // Se filtran por cliente
            $auditorias = $query
                ->whereHas('local', function($query) use ($idCliente){
                    $query->where('idCliente', '=', $idCliente);
                })
                ->get();

            return json_encode($auditorias->toArray());
        }
    }

}
