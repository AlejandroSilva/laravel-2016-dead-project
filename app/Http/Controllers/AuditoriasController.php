<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Log;
// PHP Excel
use PHPExcel;
use PHPExcel_IOFactory;
// Modelos
use App\Auditorias;
use App\Clientes;
use App\DiasHabiles;
use App\Inventarios;
use App\Locales;
use App\Zonas;
use App\Role;
use App\User;
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

        // Array de Clientes
        $clientesWithLocales = Clientes::allWithSimpleLocales();
        // Array Auditores
        $rolAuditor = Role::where('name', 'Auditor')->first();
        $auditores = $rolAuditor!=null? $rolAuditor->users : '[]';
        return view('operacional.programacionAI.programacion-mensual', [
            'puedeAgregarAuditorias'   => $user->can('programaAuditorias_agregar')? "true":"false",
            'puedeModificarAuditorias' => $user->can('programaAuditorias_modificar')? "true":"false",
            'clientes' => $clientesWithLocales,
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
            $auditoria->fechaProgramada = $request->fechaProgramada;
            $auditoria->horaPresentacionAuditor = $local->llegadaSugeridaLiderDia();
            
            $resultado = $auditoria->save();

            if($resultado){
                Log::info("[AUDITORIA:NUEVO] auditoria con idLocal '$auditoria->idLocal' programada para '$auditoria->fechaProgramada' creada.");
                return response()->json(
                    $auditoria = Auditorias::with([
                        'local.cliente',
                        'local.direccion.comuna.provincia.region',
                        'auditor'
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
                // mostrar el dato tal cual como esta en la BD
                $auditoria = Auditorias::with([
                    'local.cliente',
                    'local.direccion.comuna.provincia.region',
                    'auditor'
                ])->find($auditoria->idAuditoria);
                // agregar si existe, un inventario que haya sido realizado en el mismo local, el mismo mes
                $auditoria['inventarioEnELMismoMes'] = Locales::find($auditoria['idLocal'])
                    ->inventarioRealizadoEn($auditoria['fechaProgramada']);

                return response()->json($auditoria, 200);
            }else{
                Log::info("[AUDITORIA:ACTUALIZAR] actualizacion fallida");
                return response()->json([
                    'request'=>$request->all(),
                    'resultado'=>$resultado,
                    'auditoria'=>$auditoria
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

    // GET api/auditoria/mes/{annoMesDia}/cliente/{idCliente}
    function api_getPorMesYCliente($annoMesDia, $idCliente) {
        $auditorias = $this->buscarPorMesYCliente($annoMesDia, $idCliente);

        // agregra a la consulta, el ultimo inventario asociado al local de la auditoria
        $auditoriasConInventario = array_map(function ($auditoria) {
            // agregar si existe, un inventario que haya sido realizado en el mismo local, el mismo mes
            $auditoria['inventarioEnELMismoMes'] = Locales::find($auditoria['idLocal'])
                ->inventarioRealizadoEn($auditoria['fechaProgramada']);
            return $auditoria;
        }, $auditorias);


        return response()->json($auditoriasConInventario, 200);
    }

    // GET api/auditoria/{fecha1}/al/{fecha2}/cliente/{idCliente}
    function api_getPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente) {
        $auditorias = $this->buscarPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente);

        // agregra a la consulta, el ultimo inventario asociado al local de la auditoria
        $auditoriasConInventario = array_map(function ($auditoria) {
            // agregar si existe, un inventario que haya sido realizado en el mismo local, el mismo mes
            $auditoria['inventarioEnELMismoMes'] = Locales::find($auditoria['idLocal'])
                ->inventarioRealizadoEn($auditoria['fechaProgramada']);
            return $auditoria;
        }, $auditorias);

        return response()->json($auditoriasConInventario, 200);
    }

    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA
     * ##########################################################
     */

    // GET api/auditoria/{fecha1}/al/{fecha2}/auditor/{idCliente}
    function api_getPorRangoYAuditor($annoMesDia1, $annoMesDia2, $idAuditor){
        if(User::find($idAuditor)){
            $auditorias = $this->buscarPorRangoYAuditor($annoMesDia1, $annoMesDia2, $idAuditor);
            return response()->json($auditorias, 200);
        }else{
            return response()->json(['msg'=>'el usuario indicado no existe'], 404);
        }
    }

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

    // POST api/auditoria/cliente/{idCliente}/numeroLocal/{CECO}/fecha/{fecha}/informarRealizado
    function api_informarRealizado($idCliente, $ceco, $annoMesDia){
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
                $auditoria->realizadaInformada = true;
                $auditoria->fechaAuditoria = $annoMesDia;
                $auditoria->save();
                // buscar la auditoria actualizada en la BD
                Log::info("[AUDITORIA:INFORMAR_REALIZADO:OK] idAuditoria '$auditoria->idAuditoria' informada correctamente. ceco '$ceco', idCliente '$idCliente', mes '$annoMesDia'.");
                return response()->json(Auditorias::find($auditoria->idAuditoria), 200);
            }else{
                // auditoria con esa fecha no existe
                $errorMsg = "no existe una auditoria para el idLocal '$local->idLocal' en el mes '$annoMesDia'";
                Log::info("[AUDITORIA:INFORMAR_REALIZADO:ERROR] $errorMsg");
                return response()->json(['msg'=>$errorMsg], 404);
            }
        }else{
            // local de ese usuario, con ese ceco no existe
            $errorMsg = "no existe el CECO '$ceco' del idCliente '$idCliente'";
            Log::info("[AUDITORIA:INFORMAR_REALIZADO:ERROR] $errorMsg");
            return response()->json(['msg'=>$errorMsg], 404);
        }
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
     * Descarga de documentos
     * ##########################################################
     */

    // GET /pdf/auditorias/{mes}/cliente/{idCliente}
    function descargarPDF_porMes($annoMesDia, $idCliente){
        $auditorias = $this->buscarPorMesYCliente($annoMesDia, $idCliente);
        $cliente = Clientes::find($idCliente);

        $workbook = $this->generarWorkbook($auditorias);
        $sheet = $workbook->getActiveSheet();

        //evaluar si cliente viene con un valor
        if(!$cliente){
            $sheet->setCellValue('A2', 'Fecha:');
            $sheet->setCellValue('B2', $annoMesDia);
            $sheet->setCellValue('A1', 'Cliente:');
            $sheet->setCellValue('B1', 'Todos');

            // guardar
            $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
            $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
            $excelWritter->save($randomFileName);

            // entregar la descarga al usuario
            return response()->download($randomFileName, "programacion $annoMesDia.xlsx");

        }else {
            $sheet->setCellValue('A2', 'Fecha:');
            $sheet->setCellValue('B2', $annoMesDia);
            $sheet->setCellValue('A1', 'Cliente:');
            //obtener nombre cliente
            $sheet->setCellValue('B1', $cliente->nombre);

            // guardar
            $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
            $randomFileName = "pmensual_" . md5(uniqid(rand(), true)) . ".xlxs";
            $excelWritter->save($randomFileName);

            //return response()->json($auditorias, 200);
            return response()->download($randomFileName, "programacion $cliente->nombre-$annoMesDia.xlsx");
        }
    }

    // GET /pdf/auditorias/{fechaInicial}/al{fechaFinal}/cliente/{idCliente}
    function descargarPDF_porRango($fechaInicial, $fechaFinal, $idCliente){
        $auditorias = $this->buscarPorRangoYCliente($fechaInicial, $fechaFinal, $idCliente);
        $cliente = Clientes::find($idCliente);

        $workbook = $this->generarWorkbook($auditorias);
        $sheet = $workbook->getActiveSheet();



        //evaluar si cliente viene con un valor
        if(!$cliente){
            $sheet->setCellValue('A1', 'Cliente:');
            $sheet->setCellValue('B1', 'Todos');

            // guardar
            $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
            $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
            $excelWritter->save($randomFileName);

            // entregar la descarga al usuario
            return response()->download($randomFileName, "programacion $fechaInicial-al-$fechaFinal.xlsx");

        }else{
            $sheet->setCellValue('A1', 'Cliente:');
            $sheet->setCellValue('A2', 'Desde:');
            $sheet->setCellValue('A3', 'Hasta:');
            //obtener nombre cliente
            $sheet->setCellValue('B1', $cliente->nombre);
            $sheet->setCellValue('B2', $fechaInicial);
            $sheet->setCellValue('B3', $fechaFinal);

            // guardar
            $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
            $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
            $excelWritter->save($randomFileName);

            // entregar la descarga al usuario
            return response()->download($randomFileName, "programacion$cliente->nombre-$fechaInicial-al-$fechaFinal.xlsx");
        }
    }

    /**
     * ##########################################################
     * funciones privadas
     * ##########################################################
     */

    private function buscarPorMesYCliente($annoMesDia, $idCliente) {
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes = $fecha[1];

        $query = Auditorias::with([
            'local.cliente',
            'local.direccion.comuna.provincia.region',
            'auditor'])
            ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
            ->orderBy('fechaProgramada', 'asc');

        if ($idCliente != 0) {
            // si el cliente no es "Todos" (0), hacer un filtro por cliente
            $query->whereHas('local', function ($query) use ($idCliente) {
                $query->where('idCliente', '=', $idCliente);
            });
        }
        return $query->get()->toArray();
    }

    private function buscarPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente) {
        $query = Auditorias::with([
            'local.cliente',
            'local.direccion.comuna.provincia.region',
            'auditor'
        ])
            ->where('fechaProgramada', '>=', $annoMesDia1)
            ->where('fechaProgramada', '<=', $annoMesDia2)
            ->orderBy('fechaProgramada', 'asc');

        if($idCliente!=0){
            // Se filtran por cliente
            $query->whereHas('local', function($query) use ($idCliente){
                $query->where('idCliente', '=', $idCliente);
            });
        }
        return $query->get()->toArray();
    }

    private function buscarPorRangoYAuditor($annoMesDia1, $annoMesDia2, $idAuditor){
        $query = Auditorias::with([
            'local.cliente',
            'local.direccion.comuna.provincia.region',
            'auditor'
        ])
            ->where('fechaProgramada', '>=', $annoMesDia1)
            ->where('fechaProgramada', '<=', $annoMesDia2)
            // Se filtran por auditor
            ->whereHas('local', function($q) use ($idAuditor){
                $q->where('idAuditor', '=', $idAuditor);
            })
            ->orderBy('fechaProgramada', 'ASC')
            ->orderBy('idLocal');
        return $query->get()->toArray();
    }

    // Function para validar que la fecha entregada sea valida
    private function fecha_valida($fechaProgramada){
        $fecha = explode('-', $fechaProgramada);
        $anno = $fecha[0];
        $mes = $fecha[1];
        $dia = $fecha[2];

        return checkdate($mes, $dia, $anno);
    }

    // Funcion para general el excel
    private function generarWorkbook($auditorias){
        //$formatoLocal = FormatoLocales::find();
        $auditoriasHeader = ['Fecha Programada', 'Fecha Auditoría', 'Hora presentación', 'Realizada', 'Aprobada', 'Cliente', 'CECO', 'Local', 'Stock', 'Fecha stock', 'Auditor', 'Dirección', 'Región', 'Provincia', 'Comuna', 'Hora apertura', 'Hora cierre', 'Email', 'Teléfono 1', 'Teléfono 2'];

        $auditoriasArray = array_map(function($auditoria){
            // la fecha programada debe estar estar en formato DD-MM-YYYY
            $_fprogramada = explode('-', $auditoria['fechaProgramada']);
            $fechaProgramada = "$_fprogramada[2]-$_fprogramada[1]-$_fprogramada[0]";
            // la fecha de auditoria se muestra solo si es distinta a '0000-00-00'
            $_fauditoria  = explode('-', $auditoria['fechaAuditoria']);
            $fechaAuditoria = $auditoria['fechaAuditoria']!='0000-00-00'? "$_fauditoria[2]-$_fauditoria[1]-$_fauditoria[0]" : '';

            return [
                $fechaProgramada,
                $fechaAuditoria,
                $auditoria['horaPresentacionAuditor'],
                // en realizada, debe mostrar lo que informo el sistema de "inventario"
                $auditoria['realizadaInformada']? 'Realizada' : 'Pendiente',
                $auditoria['aprovada']? 'Aprobada': 'Pendiente',
                $auditoria['local']['cliente']['nombreCorto'],
                $auditoria['local']['numero'],
                $auditoria['local']['nombre'],
                $auditoria['local']['stock'],
                $auditoria['local']['fechaStock'],
                $auditoria['auditor']? $auditoria['auditor']['nombre1']." ".$auditoria['auditor']['apellidoPaterno'] : '-',
                $auditoria['local']['direccion']['direccion'],
                $auditoria['local']['direccion']['comuna']['provincia']['region']['nombreCorto'],
                $auditoria['local']['direccion']['comuna']['provincia']['nombre'],
                $auditoria['local']['direccion']['comuna']['nombre'],
                $auditoria['local']['horaApertura'],
                $auditoria['local']['horaCierre'],
                $auditoria['local']['emailContacto'],
                $auditoria['local']['telefono1'],
                $auditoria['local']['telefono2']
            ];
        }, $auditorias);

        // Nuevo archivo
        $workbook = new PHPExcel();
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'name'  => 'Verdana'
            )
        );

        $sheet = $workbook->getActiveSheet();
        $hora = date('d/m/Y h:i:s A',time()-10800);

        //Agregando valores a celdas y ancho a columnas
        $sheet->getStyle('A5:X5')->applyFromArray($styleArray);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->setCellValue('D1', 'Generado el:');
        $sheet->setCellValue('E1', $hora);
        $sheet->fromArray($auditoriasHeader, NULL, 'A5');
        $sheet->fromArray($auditoriasArray,  NULL, 'A6');

        return $workbook;
    }
}
