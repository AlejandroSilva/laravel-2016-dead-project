<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Log;
// Modelos
use App\Auditorias;
use App\Clientes;
use App\DiasHabiles;
use App\Inventarios;
use App\Locales;
use App\Zonas;
use App\Role;
// Permisos
use Auth;

class AuditoriasController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    // GET auditorias/estado-general-fcv
    function show_estado_general_fcv(){
        // verificar permisos
        if(!Auth::user()->can('fcv-verEstadoGeneralAuditorias'))
            return view('errors.403');

       $estadoGeneral = Auditorias::estadoGeneralCliente(2);
        return view('auditorias.estado-general-fcv.index', [
            'ega_dia' => $estadoGeneral->dia,
            'ega_zonas' => $estadoGeneral->zonas,
            'ega_totales' => $estadoGeneral->totales,
        ]);
    }
    // GET auditorias/estado-general-fcv-publico
    // RUTA PUBLICA
    function show_estado_general_fcv_publico(){
        $estadoGeneral = Auditorias::estadoGeneralCliente(2);
        return view('auditorias.estado-general-fcv.iframe-publico', [
            'ega_dia' => $estadoGeneral->dia,
            'ega_zonas' => $estadoGeneral->zonas,
            'ega_totales' => $estadoGeneral->totales,
        ]);
    }

    // GET auditorias-verProgramacion
    public function show_programacionMensual(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('auditorias-verProgramacion'))
            return view('errors.403');
        
        // Array Auditores
        $rolAuditor = Role::where('name', 'Auditor')->first();
        $auditores = $rolAuditor!=null? $rolAuditor->users : '[]';
        return view('auditorias.index-programacion-mensual', [
            'puedeAgregarAuditorias'   => $user->can('auditorias-crearModificarEliminar')? "true":"false",
            'puedeModificarAuditorias' => $user->can('auditorias-crearModificarEliminar')? "true":"false",
            'clientes' => Clientes::todos_conLocales(),
            'auditores' => $auditores
        ]);
    }

    // GET auditorias/programacion-semanal
    public function show_programacionSemanal(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('auditorias-verProgramacion'))
            return response()->json([], 403);

        // buscar la menor fechaProgramada en los inventarios
        $select = Inventarios::
        selectRaw('min(fechaProgramada) as primerInventario, max(fechaProgramada) as ultimoInventario')
            ->get();
        $minymax = $select[0];

        // Array de Clientes
        $clientes = Clientes::all();
        // Array Auditores
        $rolAuditor = Role::where('name', 'Auditor')->first();
        $auditores = $rolAuditor!=null? $rolAuditor->users : '[]';

        // buscar la mayor fechaProgramada en los iventarios
        return view('auditorias.index-programacion-semanal', [
            'puedeModificarAuditorias'  => $user->can('auditorias-crearModificarEliminar')? "true":"false",
            'puedeRevisarAuditorias'    => $user->can('auditorias-crearModificarEliminar')? "true":"false",
            'clientes' => $clientes,
            'primerInventario'=> $minymax->primerInventario,
            'ultimoInventario'=> $minymax->ultimoInventario,
            'auditores'=> $auditores,
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // POST api/auditoria/nuevo
    function api_nuevo(Request $request) {
        // validar permisos
        if(!Auth::user()->can('auditorias-crearModificarEliminar'))
            return response()->json([], 403);

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
            $auditoria->idAuditor = $request->idAuditor;
            $auditoria->fechaProgramada = $request->fechaProgramada;
            $auditoria->horaPresentacionAuditor = $local->llegadaSugeridaLiderDia();
            
            $resultado = $auditoria->save();

            if($resultado){
                Log::info("[AUDITORIA:NUEVO] auditoria con idLocal '$auditoria->idLocal' programada para '$auditoria->fechaProgramada' creada.");
                $auditoria = Auditorias::find($auditoria->idAuditoria);
                return response()->json(Auditorias::formato_programacionIGSemanalMensual($auditoria), 201);
            }else{
                return response()->json([
                    'request'=> $request->all(),
                    'errors'=> $validator->errors(),
                    'resultado'=>$resultado,
                    'auditoria'=>Auditorias::formato_programacionIGSemanalMensual($auditoria)
                ], 400);
            }
        }
    }

    // PUT api/auditorias/{idAuditoria}
    function api_actualizar($idAuditoria, Request $request) {
        // validar permisos
        if(!Auth::user()->can('auditorias-crearModificarEliminar'))
            return response()->json([], 403);

        $auditoria = Auditorias::find($idAuditoria);
        // si no existe retorna un objeto vacio con statusCode 404 (not found)
        if($auditoria){
            $mensajeActualizar = "[AUDITORIA:ACTUALIZAR] auditoria '$idAuditoria' del idLocal '$auditoria->idLocal'; ";

            // actualizar fecha siempre y cuando sea valida dependiendo el mes
            if(isset($request->fechaProgramada)) {
                if ($this->fecha_valida($request->fechaProgramada)) {
                    Log::info( $mensajeActualizar."fechaProgramada '$auditoria->fechaProgramada' > '$request->fechaProgramada'." );
                    $auditoria->fechaProgramada = $request->fechaProgramada;
                }
            }
            // actualizar auditor
            if(isset($request->idAuditor)){
                $idAuditor = $request->idAuditor==0? null: $request->idAuditor;
                Log::info( $mensajeActualizar."idAuditor '$auditoria->idAuditor' > '$idAuditor'." );
                $auditoria->idAuditor = $idAuditor;
            }
            if(isset($request->aprovada)){
                Log::info( $mensajeActualizar."aprovada '$auditoria->aprovada' > '$request->aprovada'." );
                $auditoria->aprovada = $request->aprovada;
            }
            // actualizar hora de presentacion de auditor
            if(isset($request->horaPresentacionAuditor)){
                $horaPresentacion = $request->horaPresentacionAuditor==0? null: $request->horaPresentacionAuditor;
                $auditoria->horaPresentacionAuditor = $horaPresentacion;
                Log::info( $mensajeActualizar."horaPresentacionAuditor '$auditoria->horaPresentacionAuditor' > '$horaPresentacion'." );
            }

            $resultado = $auditoria->save();

            if($resultado) {
                // volver a pedir para mostrar el dato tal cual como esta en la BD
                $auditoria = Auditorias::find($auditoria->idAuditoria);
                // yan o se utiliza esto en el front-end
                // agregar si existe, un inventario que haya sido realizado en el mismo local, el mismo mes
                //$auditoria['inventarioEnELMismoMes'] = Locales::find($auditoria['idLocal'])
                //    ->inventarioRealizadoEn($auditoria['fechaProgramada']);
                return response()->json(Auditorias::formato_programacionIGSemanalMensual($auditoria), 200);
            }else{
                Log::info("[AUDITORIA:ACTUALIZAR] actualizacion fallida");
                return response()->json([
                    'request'=>$request->all(),
                    'resultado'=>$resultado,
                    'auditoria'=> Auditorias::formato_programacionIGSemanalMensual($auditoria)
                ], 400);
            }
        } else {
            return response()->json([], 404);
        }
    }

    // DELETE api/auditorias/{idAuditoria}
    function api_eliminar($idAuditoria) {
        // validar permisos
        if(!Auth::user()->can('auditorias-crearModificarEliminar'))
            return response()->json([], 403);

        $auditoria = Auditorias::find($idAuditoria);
        if ($auditoria) {
            Log::info("[AUDITORIA:ELIMINAR] auditoria '$idAuditoria' del idLocal '$auditoria->idLocal' programada para '$auditoria->fechaProgramada' eliminada.");
            DB::transaction(function () use ($auditoria) {
                $auditoria->delete();
                return response()->json([], 204);
            });
        } else {
            return response()->json([], 404);
        }
    }

    // GET api/auditoria/buscar
    function api_buscar(Request $request){
        $auditorias = Auditorias::buscar((object)[
            'idCliente' => $request->query('idCliente'),
            'fechaInicio' => $request->query('fechaInicio'),
            'fechaFin' => $request->query('fechaFin'),
            'mes' => $request->query('mes'),
            'incluirConFechaPendiente' => $request->query('incluirConFechaPendiente')
            //'idLider' => $request->query('idLider'),
        ])->map('\App\Auditorias::formato_programacionIGSemanalMensual');

        return response()->json($auditorias);
    }

    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA
     * ##########################################################
     */

    // POST api/auditoria/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-realizado
    // PUBLICA
    function api_informarRealizado($idCliente, $ceco, $annoMesDia){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];

        // Buscar el Local (por idCliente y CECO)
        $local = Locales::where('idCliente', '=', $idCliente)
            ->where('numero', '=', $ceco)
            ->first();
        if(!$local) {
            // local de ese cliente, con ese ceco no existe
            $errorMsg = "CECO:'$ceco' idCliente:'$idCliente' fecha:'$annoMesDia'. No existe el Local.";
            Log::info("[AUDITORIA:INFORMAR_REALIZADO:ERROR] $errorMsg");
            return response()->json(['msg' => $errorMsg], 404);
        }
        
        // Buscar una aditoria del LOCAL en el mismo MES
        $auditoria = Auditorias::where('idLocal', '=', $local->idLocal)
            ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
            ->first();
        if(!$auditoria) {
            // auditoria con esa fecha no existe
            $errorMsg = "CECO:'$ceco' idCliente:'$idCliente' fecha:'$annoMesDia'. No existe auditoria para el mes indicado.";
            Log::info("[AUDITORIA:INFORMAR_REALIZADO:ERROR] $errorMsg");
            return response()->json(['msg'=>$errorMsg], 404);
        }
        
        // Verificar que no haya sido informada anteriormente
        if($auditoria->fechaAuditoria!='0000-00-00'){
            $errorMsg = "CECO:'$ceco' idCliente:'$idCliente' fecha:'$annoMesDia'. Auditoria ya informada previamente.";
            Log::info("[AUDITORIA:INFORMAR_REALIZADO:ERROR] $errorMsg");
            return response()->json(['msg'=>$errorMsg], 400);
        }
        
        $auditoria->realizadaInformada = true;      // Eliminar
        $auditoria->fechaAuditoria = $annoMesDia;   // guardar la fecha en que se realizo la auditoria (es distinta a la fecha programada)
        $auditoria->save();
        // buscar la auditoria actualizada en la BD
        Log::info("[AUDITORIA:INFORMAR_REALIZADO:OK] CECO:'$ceco' idCliente:'$idCliente' fecha:'$annoMesDia'. idAuditoria:'$auditoria->idAuditoria' informada correctamente.");
        return response()->json(Auditorias::find($auditoria->idAuditoria), 200);
    }

    // PUBLICA
    function api_informarRevisado($idCliente, $ceco, $annoMesDia){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        $dia  = $fecha[2];

        // Buscar el Local (por idCliente y CECO)
        $local = Locales::where('idCliente', '=', $idCliente)
            ->where('numero', '=', $ceco)
            ->first();
        if(!$local) {
            // local de ese cliente, con ese ceco no existe
            $errorMsg = "CECO:'$ceco' idCliente:'$idCliente' fecha:'$annoMesDia'. No existe el Local.";
            Log::info("[AUDITORIA:INFORMAR_REVISADO:ERROR] $errorMsg");
            return response()->json(['msg' => $errorMsg], 404);
        }

        // Buscar una aditoria del LOCAL en el mismo MES, y el mismo DIA
        $auditoria = Auditorias::where('idLocal', '=', $local->idLocal)
            ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
            // no considerar el dia, la plataforma "inventario.seiconsultores.cl" siempre se equivoca en +-1 o +-2 dias....
            //->whereRaw("extract(day from fechaProgramada) = ?", [$dia])
            ->first();
        if(!$auditoria) {
            // auditoria con esa fecha no existe
            $errorMsg = "CECO:'$ceco' idCliente:'$idCliente' fecha:'$annoMesDia'. No existe auditoria para el dia indicado.";
            Log::info("[AUDITORIA:INFORMAR_REVISADO:ERROR] $errorMsg");
            return response()->json(['msg'=>$errorMsg], 404);
        }

        // Verificar que no haya sido informada anteriormente
        // (esto no se hace por ahora, para evitar "problemas de integracion" en caso que hagan la peticion varias veces)

        $auditoria->aprovada = true;      // marcar como aprovada
        $auditoria->save();
        // buscar la auditoria actualizada en la BD
        Log::info("[AUDITORIA:INFORMAR_REVISADO:OK] CECO:'$ceco' idCliente:'$idCliente' fecha:'$annoMesDia'. idAuditoria:'$auditoria->idAuditoria' revisada correctamente.");
        return response()->json(Auditorias::find($auditoria->idAuditoria), 200);
    }

    // TEMPORAL; ELIMINAR ASAP
    function api_informarFecha($idCliente, $ceco, $annoMesDia){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];

        // Buscar el Local (por idCliente y CECO)
        $local = Locales::where('idCliente', '=', $idCliente)
            ->where('numero', '=', $ceco)
            ->first();
        if($local){
            // Buscar una aditoria del LOCAL en el mismo MES
            $auditoria = Auditorias::where('idLocal', '=', $local->idLocal)
                ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
                ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
                ->first();

            if($auditoria){
                $auditoria->fechaAuditoria = $annoMesDia;
                $auditoria->save();
                // buscar la auditoria actualizada en la BD
                return response()->json(Auditorias::find($auditoria->idAuditoria), 200);
            }else{
                // auditoria con esa fecha no existe
                $errorMsg = "no existe una auditoria para el idLocal '$local->idLocal' en el mes '$annoMesDia'";
                return response()->json(['msg'=>$errorMsg], 404);
            }
        }else{
            // local de ese usuario, con ese ceco no existe
            $errorMsg = "no existe el CECO '$ceco' del idCliente '$idCliente'";
            return response()->json(['msg'=>$errorMsg], 404);
        }
    }

    /**
     * ##########################################################
     * funciones privadas
     * ##########################################################
     */
    // Function para validar que la fecha entregada sea valida
    private function fecha_valida($fechaProgramada){
        $fecha = explode('-', $fechaProgramada);
        $anno = $fecha[0];
        $mes = $fecha[1];
        $dia = $fecha[2];

        return checkdate($mes, $dia, $anno);
    }
}
