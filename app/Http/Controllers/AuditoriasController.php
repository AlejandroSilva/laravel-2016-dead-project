<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
// PHP Excel
use PHPExcel;
use PHPExcel_IOFactory;
// Modelos
use App\Auditorias;
use App\Clientes;
use App\Inventarios;
use App\Locales;
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
            // actualizar fecha siempre y cuando sea valida dependiendo el mes
            if(isset($request->fechaProgramada)) {
                if ($this->fecha_valida($request->fechaProgramada)) 
                    $auditoria->fechaProgramada = $request->fechaProgramada;
            }

            // actualizar auditor
            if(isset($request->idAuditor))
                $auditoria->idAuditor = $request->idAuditor==0? null: $request->idAuditor;
            if(isset($request->realizada))
                $auditoria->realizada = $request->realizada;
            if(isset($request->aprovada))
                $auditoria->aprovada = $request->aprovada;
            // actualizar hora de presentacion de auditor
            if(isset($request->horaPresentacionAuditor))
                $auditoria->horaPresentacionAuditor = $request->horaPresentacionAuditor==0? null: $request->horaPresentacionAuditor;
            
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
        return response()->json($auditorias, 200);
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
    // POST api/auditoria/cliente/{idCliente}/numeroLocal/{CECO}/fecha/{fecha}/informarRealizado
    function api_informarRealizado($idCliente, $ceco, $fecha){
        // Buscar el Local (por idCliente y CECO)
        $local = Locales::where('idCliente', '=', $idCliente)
            ->where('numero', '=', $ceco)
            ->first();
        if($local){
            // Buscar la auditoria (por fecha)
            $auditoria = Auditorias::where('idLocal', '=', $local->idLocal)->first();
            if($auditoria){
                $auditoria->realizada = true;
                $auditoria->save();
                return response()->json(Auditorias::find($auditoria->idAuditoria), 200);
            }else{
                // auditoria con esa fecha no existe
                return response()->json(['msg'=>'no existe una auditoria con esa fecha para este local'], 404);
            }
        }else{
            // local de ese usuario, con ese ceco no existe
            return response()->json(['msg'=>'no existe un local con este numero para este cliente'], 404);
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
        $auditoriasHeader = ['Fecha', 'Hora presentación', 'Realizada', 'Aprobada', 'Cliente', 'CECO', 'Local', 'Stock', 'Fecha stock', 'Auditor', 'Dirección', 'Región', 'Provincia', 'Comuna', 'Hora apertura', 'Hora cierre', 'Email', 'Telefono1', 'Telefono2'];

        $auditoriasArray = array_map(function($auditoria){
            return [
                $auditoria['fechaProgramada'],
                $auditoria['horaPresentacionAuditor'],
                $auditoria['realizada']? 'Realizada' : 'Pendiente',
                $auditoria['aprovada']? 'Aprobada': 'Pendiente',
                $auditoria['local']['cliente']['nombreCorto'],
                $auditoria['local']['numero'],
                $auditoria['local']['nombre'],
                $auditoria['local']['stock'],
                $auditoria['local']['fechaStock'],
                $auditoria['auditor']? ['auditor']:'--------------------------',
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
                'size'  => 12,
                'name'  => 'Verdana'
            )
        );

        $sheet = $workbook->getActiveSheet();
        $hora = date('d/m/Y h:i:s A',time()-10800);

        //Agregando valores a celdas y ancho a columnas
        $sheet->getStyle('A5:B5')->applyFromArray($styleArray);
        $sheet->getStyle('C5:D5')->applyFromArray($styleArray);
        $sheet->getStyle('E5:F5')->applyFromArray($styleArray);
        $sheet->getStyle('G5:H5')->applyFromArray($styleArray);
        $sheet->getStyle('I5:J5')->applyFromArray($styleArray);
        $sheet->getStyle('K5:L5')->applyFromArray($styleArray);
        $sheet->getStyle('M5:N5')->applyFromArray($styleArray);
        $sheet->getStyle('O5:P5')->applyFromArray($styleArray);
        $sheet->getStyle('Q5:R5')->applyFromArray($styleArray);
        $sheet->getStyle('S5')->applyFromArray($styleArray);
        $sheet->getColumnDimension('A')->setWidth(12.5);
        $sheet->getColumnDimension('B')->setWidth(19);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(14.5);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(38);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(15);
        $sheet->getColumnDimension('O')->setWidth(15);
        $sheet->getColumnDimension('P')->setWidth(15);
        $sheet->getColumnDimension('Q')->setWidth(28);
        $sheet->getColumnDimension('R')->setWidth(13);
        $sheet->getColumnDimension('S')->setWidth(13);
        $sheet->setCellValue('F1', 'Generado el:');
        $sheet->setCellValue('G1', $hora);
        $sheet->fromArray($auditoriasHeader, NULL, 'A5');
        $sheet->fromArray($auditoriasArray,  NULL, 'A6');

        return $workbook;
    }
}
