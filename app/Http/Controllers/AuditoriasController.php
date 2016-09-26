<?php

namespace App\Http\Controllers;

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
    
    // GET programacionAI/
    public function showProgramacionIndex(){
        return view('operacional.programacionAI.programacion-index');
    }

    // GET programacionAI/mensual
    public function showMensual(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaAuditorias_ver'))
            return view('errors.403');
        
        // Array Auditores
        $rolAuditor = Role::where('name', 'Auditor')->first();
        $auditores = $rolAuditor!=null? $rolAuditor->users : '[]';
        return view('operacional.programacionAI.programacion-mensual', [
            'puedeAgregarAuditorias'   => $user->can('programaAuditorias_agregar')? "true":"false",
            'puedeModificarAuditorias' => $user->can('programaAuditorias_modificar')? "true":"false",
            'clientes' => Clientes::todos_conLocales(),
            'auditores' => $auditores
        ]);
    }

    // GET programacionAI/semanal
    public function showSemanal(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaAuditorias_ver'))
            return view('errors.403');

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
        return view('operacional.programacionAI.programacion-semanal', [
            'puedeModificarAuditorias' => $user->can('programaAuditorias_modificar')? "true":"false",
            'puedeRevisarAuditorias' => $user->can('programaAuditorias_revisar')? "true":"false",
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

    // GET XXXXX
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

    // GET api/auditoria/cliente/{idCliente}/mes/{annoMesDia}/estadoGeneral
    function api_estadoGeneral($idCliente, $annoMesDia){
        $dia = DiasHabiles::where('fecha', '=', $annoMesDia)->first();

        if(!$dia){
            return response()->json(['msg'=>'No se encuentra informacion de los dias habiles de esta fecha'], 400);
        }
        $cliente = Clientes::find($idCliente);
        if($cliente){
            $_fecha = explode('-', $annoMesDia);
            $anno = $_fecha[0];
            $mes  = $_fecha[1];
            $diasHabilesMes = $dia->getDiasHabilesMes();
            $diasHabilesTranscurridos = $dia->getDiasHabilesTranscurridosMes();
            $diasHabilesRestantes = $dia->getDiasHabilesRestantesMes();
            
            $reporte_general = array_map(function($zona)
            use($idCliente, $anno, $mes, $diasHabilesMes, $diasHabilesRestantes, $diasHabilesTranscurridos){

                $auditorias = Auditorias::with([
                    'local.direccion.comuna.provincia.region'
                ])
                // mismo mes
                ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
                ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
                // mismo cliente
                ->whereHas('local', function($q) use ($idCliente) {
                    $q->where('idCliente', '=', $idCliente);
                })
                // misma zona
                ->whereHas('local.direccion.comuna.provincia.region', function($q) use ($zona) {
                    $q->where('idZona', '=', $zona['idZona']);
                })
                ->get();

                // Filtrar las REALIZADAS, de las PENDIENTES
                $realizadasInformado = [];
                $pendientesInformado = [];
                foreach ($auditorias as $auditoria){
                    // IMPORTANTE: separar por Realizado Manual -----  SE IGNORAN LOS MANUALES
                    // separar por Realizado Informado
                    if($auditoria->realizadaInformada==0){
                        array_push($pendientesInformado, $auditoria);
                    }else{
                        array_push($realizadasInformado, $auditoria);
                    }
                };

                // Realizar los calculos de zona
                $total = count($auditorias);
                $realizadas = count($realizadasInformado);
                $pendientes = count($pendientesInformado);
                // Estado de avance Esperado
                $auditoriasPorDia_esperado = $diasHabilesMes==0? 0 : round($total/$diasHabilesMes, 1);          // division por cero
                $realizadasALaFecha_esperado = round($diasHabilesTranscurridos*$auditoriasPorDia_esperado);
                $pendientesALaFecha_esperado = $total - $realizadasALaFecha_esperado;
                $porcentajeAvance_esperado = $total==0? 0 : round(($realizadasALaFecha_esperado*100)/$total);   // division por cero
                $diasParaTerminar_esperado = $auditoriasPorDia_esperado==0? 0 : round($pendientesALaFecha_esperado/$auditoriasPorDia_esperado, 1);
                // Estado de avance Real
                $auditoriasPorDia_real = $diasHabilesTranscurridos==0? 0 : round($realizadas/$diasHabilesTranscurridos, 1); // division por cero
                $porcentajeAvance_real = $total==0? 0 : round(($realizadas*100)/$total);                        // division por cero
                $diasParaTerminar_real = $auditoriasPorDia_real==0? 0 : round($pendientes/$auditoriasPorDia_real, 1);
                
                return [
                    'zona'=>$zona,
                    #'regiones'=>Zonas::find($zona['idZona'])->regiones,
                    'informado'=>[
                        // conteo de auditorias
                        'totalMes'=>$total,
                        'realizadas'=>$realizadas,
                        'pendientes'=>$pendientes,
                        // dias habiles del mes
                        'diasHabilesMes'=>$diasHabilesMes,
                        'diasHabilesRestantes'=>$diasHabilesRestantes,
                        'diasHabilesTranscurridos'=>$diasHabilesTranscurridos,
                        // Estado de avance Esperado
                        'auditoriasPorDia_esperado'=>$auditoriasPorDia_esperado,
                        'realizadasALaFecha_esperado'=>$realizadasALaFecha_esperado,
                        'pendientesALaFecha_esperado'=>$pendientesALaFecha_esperado,
                        'porcentajeCumplimiento_esperado'=>$porcentajeAvance_esperado,
                        'diasParaTerminar_esperado'=>$diasParaTerminar_esperado,
                        // Estado de avance Real
                        'auditoriasPorDia_real'=>$auditoriasPorDia_real,
                        'realizadasALaFecha_real'=>$realizadas,
                        'porcentajeCumplimiento_real'=>$porcentajeAvance_real,
                        'diasParaTerminar_real'=>$diasParaTerminar_real,
                    ]
                ];
            }, Zonas::orderBy('idZona')->get()->toArray());

            return response()->json($reporte_general, 200);
        }else{
            return response()->json(['msg'=>'cliente no encontrado'], 404);
        }
    }

    // POST api/auditoria/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-realizado
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
