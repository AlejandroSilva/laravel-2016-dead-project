<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Redirect;
// PHP Excel
use PHPExcel;
use PHPExcel_IOFactory;
// Modelos
use App\Clientes;
use App\Inventarios;
use App\Locales;
use App\Nominas;
use App\User;
// Auth
use App\Role;
use Auth;


class InventariosController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET programacionIG/
    public function showProgramacionIndex(){
        return view('operacional.programacionIG.programacionIG-index');
    }

    // GET programacionIG/mensual
    public function showProgramacionMensual(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaInventarios_ver'))
            return view('errors.403');

        $clientesWithLocales = Clientes::allWithSimpleLocales();
        return view('operacional.programacionIG.programacionIG-mensual', [
            'puedeAgregarInventarios'   => $user->can('programaInventarios_agregar')? "true":"false",
            'puedeModificarInventarios' => $user->can('programaInventarios_modificar')? "true":"false",
            'clientes' => $clientesWithLocales,
        ]);
    }

    // GET programacionIG/semanal
    public function showProgramacionSemanal(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaInventarios_ver'))
            return view('errors.403');

        // Clientes
        $clientes  = Clientes::all();
        // Captadores
        $rolCaptador = Role::where('name', 'Captador')->first();
        $captadores = $rolCaptador!=null? $rolCaptador->users : '[]';
        // Supervisores
        $rolSupervisor = Role::where('name', 'Supervisor')->first();
        $supervisores = $rolSupervisor!=null? $rolSupervisor->users : '[]';
        // Lideres
        $rolLider = Role::where('name', 'Lider')->first();
        $lideres = $rolLider!=null? $rolLider->users : '[]';

        // buscar la mayor fechaProgramada en los iventarios
        return view('operacional.programacionIG.programacionIG-semanal', [
            'puedeModificarInventarios' => $user->can('programaInventarios_modificar')? "true":"false",
            'clientes' => $clientes,
            'captadores'=> $captadores,
            'supervisores'=> $supervisores,
            'lideres'=> $lideres
        ]);
    }

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
            //'idJornada'=> 'required',
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
            // asignar la jornada entregada por parametros, o la que tenga por defecto el local
            if($request->idJornada) {
                $inventario->idJornada = $request->idJornada;
            }else{
                $inventario->idJornada = $local->idJornadaSugerida;
            }
            $inventario->fechaProgramada = $request->fechaProgramada;
            $inventario->dotacionAsignadaTotal = $local->dotacionSugerida();
            $inventario->stockTeorico = $local->stock;
            $inventario->fechaStock =   $local->fechaStock;

            // Crear las dos nominas
            $nominaDia = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaDia->horaPresentacionLider = $local->llegadaSugeridaLiderDia();
            $nominaDia->horaPresentacionEquipo = $local->llegadaSugeridaPersonalDia();
            // Todo: la dotacion sugerida deberia dividirse en dos cuando la jornada sea doble:
            $nominaDia->dotacionAsignada = $local->dotacionSugerida();
            $nominaDia->dotacionCaptador1 = 0;
            $nominaDia->dotacionCaptador2 = 0;
            $nominaDia->horaTermino = '';
            $nominaDia->horaTerminoConteo = '';
            $nominaDia->save();

            $nominaNoche = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaNoche->horaPresentacionLider = $local->llegadaSugeridaLiderNoche();
            $nominaNoche->horaPresentacionEquipo = $local->llegadaSugeridaPersonalNoche();
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
                return response()->json(
                    $inventario = Inventarios::with([
                        'local.cliente',
                        'local.formatoLocal',
                        'local.direccion.comuna.provincia.region',
                        'nominaDia',
                        'nominaNoche'
                    ])->find($inventario->idInventario)
                    , 201);
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
    function api_get($idInventario) {
        $inventario = Inventarios::with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
        ])->find($idInventario);
        if ($inventario) {
            return response()->json($inventario, 200);
        } else {
            return response()->json([], 404);
        }
    }

    // PUT api/inventario/{idInventario}
    function api_actualizar($idInventario, Request $request){
        $inventario = Inventarios::find($idInventario);
        // si no existe retorna un objeto vacio con statusCode 404 (not found)
        if($inventario){
            // actualizar fecha siempre y cuando sea valida dependiendo el mes
            if(isset($request->fechaProgramada)){
                if($this->fecha_valida($request->fechaProgramada))
                    $inventario->fechaProgramada = $request->fechaProgramada;
            }
            if(isset($request->dotacionAsignadaTotal))
                $inventario->dotacionAsignadaTotal = $request->dotacionAsignadaTotal;
            if(isset($request->idJornada))
                $inventario->idJornada = $request->idJornada;
            if(isset($request->stockTeorico))
                $inventario->stockTeorico = $request->stockTeorico;

            $resultado = $inventario->save();

            if($resultado) {
                // mostrar el dato tal cual como esta en la BD
                return response()->json(
                    Inventarios::with([
                        'local.cliente',
                        'local.formatoLocal',
                        'local.direccion.comuna.provincia.region',
                        'nominaDia',
                        'nominaNoche',
                        'nominaDia.lider',
                        'nominaNoche.lider',
                        'nominaDia.captador',
                        'nominaNoche.captador',
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

    // DELETE api/inventario/{idInventario}
    function api_eliminar($idInventario, Request $request){
        $inventario = Inventarios::find($idInventario);
        if($inventario){
            DB::transaction(function() use($inventario){
                $inventario->delete();
                // eliminar sus jornadas
                $nominaDia = $inventario->nominaDia;
                $nominaDia->delete();
                $nominaNoche = $inventario->nominaNoche;
                $nominaNoche->delete();
                return response()->json([], 204);
            });

        }else{
            return response()->json([], 404);
        }
    }

    // GET api/inventario/mes/{annoMesDia}                              // ELIMINAR
//    function api_getPorMesYCliente($annoMesDia, $idCliente){
//        $inventarios = $this->inventariosPorMesYCliente($annoMesDia, $idCliente);
//        return response()->json($inventarios, 200);
//    }

    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA
     * ##########################################################
     */
    
    // GET api/inventario/{fecha1}/al/{fecha2}/cliente/{idCliente}      // ELIMINAR
//    function api_getPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente){
//        $inventarios = $this->inventariosPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente);
//        return response()->json($inventarios, 200);
//    }

    // GET api/inventario/{fecha1}/al/{fecha2}/lider/{idCliente}        // ELIMINAR
//    function api_getPorRangoYLider($annoMesDia1, $annoMesDia2, $idCliente){
//        if(User::find($idCliente)){
//            $auditorias = $this->buscarPorRangoYLider($annoMesDia1, $annoMesDia2, $idCliente);
//            return response()->json($auditorias, 200);
//        }else{
//            return response()->json(['msg'=>'el usuario indicado no existe'], 404);
//        }
//    }
    /**
     * ##########################################################
     * Descarga de documentos
     * ##########################################################
     */

    // GET /pdf/inventarios/{mes}/cliente/{idCliente}
    public function descargarPDF_porMes($annoMesDia, $idCliente){
        //Se utiliza funcion privada que recorre inventarios por mes y dia
        $inventarios = $this->buscarInventarios(null, null, $annoMesDia, $idCliente, null);

        // nombre del cliente (si existe)
        $cliente = Clientes::find($idCliente);
        $nombreCliente = $cliente? $cliente->nombre : 'Todos';

        // generar el archivo
        $workbook = $this->generarWorkbook($inventarios);
        $sheet = $workbook->getActiveSheet();
        $sheet->setCellValue('A1', 'Cliente:');
        $sheet->setCellValue('B1', $nombreCliente);
        $sheet->setCellValue('A2', 'Fecha:');
        $sheet->setCellValue('B2', $annoMesDia);

        // guardar el archivo a disco y descargarlo
        $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
        $randomFileName = "archivos_temporales/progIGmes_".md5(uniqid(rand(), true)).".xlxs";
        $excelWritter->save($randomFileName);
        return response()->download($randomFileName, "programacion IG $nombreCliente ($annoMesDia).xlsx");
    }

    // GET /pdf/inventarios/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}
    public function descargarPDF_porRango($fechaInicio, $fechaFin, $idCliente){
        //Se utiliza funcion privada que recorre inventarios por fecha inicio-final y por idCliente
        $inventarios = $this->buscarInventarios($fechaInicio, $fechaFin, null, $idCliente, null);

        // nombre del cliente (si existe)
        $cliente = Clientes::find($idCliente);
        $nombreCliente = $cliente? $cliente->nombre : 'Todos';

        // generar el archivo
        $workbook = $this->generarWorkbook($inventarios);
        $sheet = $workbook->getActiveSheet();
        $sheet->setCellValue('A1', 'Cliente:');
        $sheet->setCellValue('B1', $nombreCliente);
        $sheet->setCellValue('A2', 'Desde:');
        $sheet->setCellValue('B2', $fechaInicio);
        $sheet->setCellValue('A3', 'Hasta:');
        $sheet->setCellValue('B3', $fechaFin);

        // guardar el archivo a disco y descargarlo
        $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
        $randomFileName = "archivos_temporales/progIGrango".md5(uniqid(rand(), true)).".xlxs";
        $excelWritter->save($randomFileName);
        return response()->download($randomFileName, "programacion IG $nombreCliente ($fechaInicio al $fechaFin).xlsx");
    }

    /**
     * ##########################################################
     * funciones privadas
     * ##########################################################
     */

    //función filtra por mes y cliente
//    private function inventariosPorMesYCliente($annoMesDia, $idCliente){
//        $fecha = explode('-', $annoMesDia);
//        $anno = $fecha[0];
//        $mes  = $fecha[1];
//
//        $query = Inventarios::with([
//            'local.cliente',
//            'local.formatoLocal',
//            'local.direccion.comuna.provincia.region',
//            'nominaDia',
//            'nominaNoche',
//            'nominaDia.lider',
//            'nominaNoche.lider',
//            'nominaDia.captador',
//            'nominaNoche.captador'
//        ])
//            ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
//            ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
//            ->orderBy('fechaProgramada', 'asc');
//
//        if($idCliente!=0) {
//            $query->whereHas('local', function($q) use ($idCliente) {
//                $q->where('idCliente', '=', $idCliente);
//            });
//        }
//        return $query->get()->toArray();
//    }

    //función filtra por rango de fecha y cliente
//    private function inventariosPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente){
//        $query = Inventarios::with([
//            'local.cliente',
//            'local.formatoLocal',
//            'local.direccion.comuna.provincia.region',
//            'nominaDia',
//            'nominaNoche',
//            'nominaDia.lider',
//            'nominaNoche.lider',
//            'nominaDia.captador',
//            'nominaNoche.captador',
//        ])
//            ->where('fechaProgramada', '>=', $annoMesDia1)
//            ->where('fechaProgramada', '<=', $annoMesDia2)
//            ->orderBy('fechaProgramada', 'asc');
//
//        if($idCliente!=0) {
//            // Se filtran por cliente
//            $query->whereHas('local', function ($q) use ($idCliente) {
//                $q->where('idCliente', '=', $idCliente);
//            });
//        }
//        return $query->get()->toArray();
//    }

    //función filtra por rango de fecha y lider
//    private function buscarPorRangoYLider($annoMesDia1, $annoMesDia2, $idUsuario){
//        // obtener todos los inventarios en ese periodo de tiempo
//        $inventarios = $this->inventariosPorRangoYCliente($annoMesDia1, $annoMesDia2, 0);
//
//        // quitar todos los inventarios en los que en usuario no es lider
//        $inventariosFiltrados = [];
//        foreach ($inventarios as $inventario) {
//            $jornadaInventario = $inventario['idJornada'];
//            $liderDia = $inventario['nomina_dia']['idLider'];
//            $liderNoche = $inventario['nomina_noche']['idLider'];
//
//            // 1="no definido", 2="dia", 3="noche", 4="dia y noche"
//            if ($jornadaInventario == 2 && ($liderDia==$idUsuario)) {
//                // si es "dia", solo puede estar asignado a la nomina de dia
//                array_push($inventariosFiltrados, $inventario);
//            } else if ($jornadaInventario == 3 && ($liderNoche==$idUsuario)) {
//                // si es "noche", solo puede estar asignado a la nomina de noche
//                array_push($inventariosFiltrados, $inventario);
//            } else if ($jornadaInventario == 4 && ( ($liderDia==$idUsuario) || ($liderNoche==$idUsuario) )) {
//                // si la jornada es "dia noche", puede ser lider de cualquiera de las dos nominas
//                array_push($inventariosFiltrados, $inventario);
//            } else {
//                // si no tiene nominas asignadas, no es lider de ninguna
//            }
//        }
//        return $inventariosFiltrados;
//    }

    function api_buscar(Request $request){
        // agrega cabeceras para las peticiones con CORS
        header('Access-Control-Allow-Origin: *');

        $fechaInicio = $request->query('fechaInicio');
        $fechaFin = $request->query('fechaFin');
        $mes = $request->query('mes');
        $idCliente = $request->query('idCliente');
        $idLider = $request->query('idLider');

        $inventarios = $this->buscarInventarios($fechaInicio, $fechaFin, $mes, $idCliente, $idLider);
        return response()->json($inventarios, 200);
    }

    private function buscarInventarios($fechaInicio, $fechaFin, $mes, $idCliente, $idLider){
        $query = Inventarios::with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
        ]);
        
        // Filtrar por rango de fecha si corresponde
        if(isset($fechaInicio) && isset($fechaFin)){
            $query
                ->where('fechaProgramada', '>=', $fechaInicio)
                ->where('fechaProgramada', '<=', $fechaFin)
                ->orderBy('fechaProgramada', 'asc');    
        }
        
        // Filtrar por mes si corresponde
        if(isset($mes)){
            $fecha = explode('-', $mes);
            $anno = $fecha[0];
            $mes  = $fecha[1];
            $query->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
                ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
                ->orderBy('fechaProgramada', 'asc');
        }
        
        // Se filtran por cliente, solo si este es distinto a cero
        if( isset($idCliente) && $idCliente!=0) {
            $query->whereHas('local', function ($q) use ($idCliente) {
                $q->where('idCliente', '=', $idCliente);
            });
        }
        
        $inventarios = $query->get()->toArray();

        // Filtrar por Lideres si corresponde
        if(isset($idLider) && $idLider!=0){
            $inventariosFiltrados = [];
            foreach ($inventarios as $inventario) {
                $jornadaInventario = $inventario['idJornada'];
                $liderDia = $inventario['nomina_dia']['idLider'];
                $liderNoche = $inventario['nomina_noche']['idLider'];

                // 1="no definido", 2="dia", 3="noche", 4="dia y noche"
                if ($jornadaInventario == 2 && ($liderDia==$idLider)) {
                    // si es "dia", solo puede estar asignado a la nomina de dia
                    array_push($inventariosFiltrados, $inventario);
                } else if ($jornadaInventario == 3 && ($liderNoche==$idLider)) {
                    // si es "noche", solo puede estar asignado a la nomina de noche
                    array_push($inventariosFiltrados, $inventario);
                } else if ($jornadaInventario == 4 && ( ($liderDia==$idLider) || ($liderNoche==$idLider) )) {
                    // si la jornada es "dia noche", puede ser lider de cualquiera de las dos nominas
                    array_push($inventariosFiltrados, $inventario);
                } else {
                    // si no tiene nominas asignadas, no es lider de ninguna
                }
            }
            return $inventariosFiltrados;
        }else{
            return $inventarios;
        }
    }

    //Function para validar que la fecha sea valida
    private function fecha_valida($fechaProgramada){
        $fecha = explode('-', $fechaProgramada);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        $dia = $fecha[2];

        // cuando se pone una fecha del tipo '2016-04-', checkdate lanza una excepcion
        if( !isset($anno) || !isset($mes) || !isset($dia)) {
            return false;
        }else{
            return checkdate($mes,$dia,$anno);
        }
    }
    
    // Función generica para generar el archivo excel
    private function generarWorkbook($inventarios){
        $inventariosHeader = ['Fecha', 'Cliente', 'CECO', 'Local', 'Región', 'Comuna', 'Stock', 'Fecha stock', 'Dotación Total', 'Dirección'];

        $inventariosArray = array_map(function($inventario){
            return [
                $inventario['fechaProgramada'],
                $inventario['local']['cliente']['nombreCorto'],
                $inventario['local']['numero'],
                $inventario['local']['nombre'],
                $inventario['local']['direccion']['comuna']['provincia']['region']['numero'],
                $inventario['local']['direccion']['comuna']['nombre'],
                $inventario['local']['stock'],
                $inventario['fechaStock'],
                $inventario['dotacionAsignadaTotal'],
                $inventario['local']['direccion']['direccion']
            ];
        }, $inventarios);

        // Nuevo archivo
        $workbook = new PHPExcel();  // workbook
        $sheet = $workbook->getActiveSheet();
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'size'  => 12,
                'name'  => 'Verdana'
            )
        );
        $hora = date('d/m/Y h:i:s A',time()-10800);

        $sheet->getStyle('A5:B5')->applyFromArray($styleArray);
        $sheet->getStyle('C5:D5')->applyFromArray($styleArray);
        $sheet->getStyle('E5:F5')->applyFromArray($styleArray);
        $sheet->getStyle('G5:H5')->applyFromArray($styleArray);
        $sheet->getStyle('I5:J5')->applyFromArray($styleArray);
        $sheet->getColumnDimension('A')->setWidth(12.5);
        $sheet->getColumnDimension('D')->setWidth(17);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12.5);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(40);
        $sheet->setCellValue('F1', 'Generado el:');
        $sheet->setCellValue('G1', $hora);
        $sheet->fromArray($inventariosHeader, NULL, 'A5');
        $sheet->fromArray($inventariosArray,  NULL, 'A6');

        return $workbook;
    }
}