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
        $clientes  = Clientes::all();
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
    function api_actualizar($idAuditoria, Request $request){
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
                return response()->json(
                    Auditorias::with([
                        'local.cliente',
                        'local.direccion.comuna.provincia.region',
                        'auditor'
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
    function api_getPorMesYCliente($annoMesDia, $idCliente){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];

        $query = Auditorias::with([
            'local.cliente',
            'local.direccion.comuna.provincia.region',
            'auditor'
        ])
            ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$mes]);


        if($idCliente==0){
            // No se realiza un filtro por clientes
            return response()->json($query->get()->toArray(), 200);
        }else{
            $query->whereHas('local', function($query) use ($idCliente){
                $query->where('idCliente', '=', $idCliente);
            });
            return response()->json($query->get()->toArray(), 200);
        }
    }

    // GET api/auditoria/{fecha1}/al/{fecha2}/cliente/{idCliente}
    function api_getPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente){
        $query = Auditorias::with([
            'local.cliente',
            'local.direccion.comuna.provincia.region',
            'auditor'
        ])
            ->where('fechaProgramada', '>=', $annoMesDia1)
            ->where('fechaProgramada', '<=', $annoMesDia2);

        if($idCliente==0){
            // No se realiza un filtro por clientes
            $auditorias = $query->get();
            return response()->json($auditorias->toArray(), 200);
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

    /**
     * ##########################################################
     * Descarga de documentos
     * ##########################################################
     */

    // GET /pdf/auditorias/{mes}/cliente/{idCliente}
    function descargarPDF_porMes($annoMesDia, $idCliente){
        return response()->json(['msg'=>'no implementado'], 501);
    /*
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];

        // inventarios que se desean mostrar
        $inventarios = Inventarios::with([
            //'local',
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche'
        ])
            ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
            ->join('locales', 'locales.idLocal', '=', 'inventarios.idLocal')
            ->orderBy('fechaProgramada', 'ASC')
            ->orderBy('locales.stock', 'DESC')
            ->get();

        $inventariosHeader = ['Fecha', 'Cliente', 'CECO', 'Local', 'Región', 'Comuna', 'Stock', 'Dotación Total'];
        $inventariosArray = array_map(function($inventario){
            return [
                $inventario['fechaProgramada'],
                $inventario['local']['cliente']['nombreCorto'],
                $inventario['local']['numero'],
                $inventario['local']['nombre'],
                $inventario['local']['direccion']['comuna']['provincia']['region']['numero'],
                $inventario['local']['direccion']['comuna']['nombre'],
                $inventario['local']['stock'],
                $inventario['dotacionAsignadaTotal'],
            ];
        }, $inventarios->toArray());

        // Nuevo archivo
        $workbook = new PHPExcel();  // workbook
        $sheet = $workbook->getActiveSheet();
        // agregar datos
        $sheet->setCellValue('A1', 'Programación mensual');
        $sheet->fromArray($inventariosHeader, NULL, 'A4');
        $sheet->fromArray($inventariosArray,  NULL, 'A5');

        // guardar
        $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
        $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
        $excelWritter->save($randomFileName);

        // entregar la descarga al usuario
        return response()->download($randomFileName, "programacion $annoMesDia.xlsx");
    */
    }

    // GET /pdf/auditorias/{fechaInicial}/al{fechaFinal}/cliente/{idCliente}
    function descargarPDF_porRango($fechaInicial, $fechaFinal, $idCliente){
        return response()->json(['msg'=>'no implementado'], 501);
    }
    
    // Function para validar que la fecha entregada sea valida
    private function fecha_valida($fechaProgramada){
        $fecha = explode('-', $fechaProgramada);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        $dia = $fecha[2];

        return checkdate($mes,$dia,$anno);
    }
}
