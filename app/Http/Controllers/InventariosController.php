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

    // GET api/inventario/mes/{annoMesDia}
    function api_getPorMesYCliente($annoMesDia, $idCliente){
        $inventarios = $this->inventariosPorMesYCliente($annoMesDia, $idCliente);
        return response()->json($inventarios, 200);
    }

    // GET api/inventario/{fecha1}/al/{fecha2}/cliente/{idCliente}
    function api_getPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente){
        $inventarios = $this->inventariosPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente);
        return response()->json($inventarios, 200);
    }

    // GET api/inventario/{fecha1}/al/{fecha2}
    // TODO ¿¿getPorRango ya no se utiliza??
    function api_getPorRango($annoMesDia1, $annoMesDia2){
        $inventarios = Inventarios::with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
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

    // GET api/inventario/{fecha1}/al/{fecha2}/lider/{idCliente}
    function api_getPorRangoYLider($fecha1, $fecha2, $idCliente){
        return response()->json(['msg'=>'no implementado'], 501);
    }
    /**
     * ##########################################################
     * Descarga de documentos
     * ##########################################################
     */

    // GET /pdf/inventarios/{mes}/cliente/{idCliente}
    public function descargarPDF_porMes($annoMesDia, $idCliente){
        //Se utiliza funcion privada que recorre inventarios por mes y dia
        $inventarios = $this->inventariosPorMesYCliente($annoMesDia, $idCliente);
        $cliente = Clientes::find($idCliente);

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

        $sheet->getStyle('A4:B4')->applyFromArray($styleArray);
        $sheet->getStyle('C4:D4')->applyFromArray($styleArray);
        $sheet->getStyle('E4:F4')->applyFromArray($styleArray);
        $sheet->getStyle('G4:H4')->applyFromArray($styleArray);
        $sheet->getStyle('I4:J4')->applyFromArray($styleArray);
        $sheet->getColumnDimension('A')->setWidth(12.5);
        $sheet->getColumnDimension('D')->setWidth(17);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12.5);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(40);

        // agregar datos
        $sheet->setCellValue('A1', 'Programación mensual');
        $sheet->setCellValue('A2', 'Mes:');
        $sheet->setCellValue('B2', $annoMesDia);
        $sheet->setCellValue('F1', 'Generado el:');
        $sheet->setCellValue('G1', $hora);
        $sheet->fromArray($inventariosHeader, NULL, 'A4');
        $sheet->fromArray($inventariosArray,  NULL, 'A5');

        if(!$cliente){
            $sheet->setCellValue('A1', 'Cliente:');
            $sheet->setCellValue('B1', 'Todos');

            // guardar
            $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
            $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
            $excelWritter->save($randomFileName);

            // entregar la descarga al usuario
            return response()->download($randomFileName, "programacion $annoMesDia.xlsx");

        }else{
            $sheet->setCellValue('A1', 'Cliente:');
            //obtener nombre cliente
            $sheet->setCellValue('B1', $cliente->nombre);

            // guardar
            $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
            $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
            $excelWritter->save($randomFileName);

            // entregar la descarga al usuario
            return response()->download($randomFileName, "programacion $cliente->nombre-$annoMesDia.xlsx");
        }
    }

    // GET /pdf/inventarios/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}
    public function descargarPDF_porRango($annoMesDia1, $annoMesDia2, $idCliente){
        //Se utiliza funcion privada que recorre inventarios por fecha inicio-final y por idCliente
        $inventarios = $this-> inventariosPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente);
        $cliente = Clientes::find($idCliente);

        $inventariosHeader = ['Fecha', 'Cliente', 'CECO', 'Local', 'Región', 'Comuna', 'Stock', 'Fecha Stock', 'Dotación Total', 'Dirección'];
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
        //aplicando estilos
        $sheet->getStyle('A4:B4')->applyFromArray($styleArray);
        $sheet->getStyle('C4:D4')->applyFromArray($styleArray);
        $sheet->getStyle('E4:F4')->applyFromArray($styleArray);
        $sheet->getStyle('G4:H4')->applyFromArray($styleArray);
        $sheet->getStyle('I4:J4')->applyFromArray($styleArray);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(40);
        $sheet->getColumnDimension('A')->setWidth(12.5);
        $sheet->getColumnDimension('B')->setWidth(13);
        $sheet->getColumnDimension('D')->setWidth(17);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12.5);
        $sheet->getColumnDimension('H')->setWidth(15);

        // agregar datos
        $sheet->setCellValue('A2', 'Rango fecha:');
        $sheet->setCellValue('B2', "$annoMesDia1 al ");
        $sheet->setCellValue('C2', $annoMesDia2);
        $sheet->setCellValue('F1', 'Generado el:');
        $sheet->setCellValue('G1', $hora);
        $sheet->fromArray($inventariosHeader, NULL, 'A4');
        $sheet->fromArray($inventariosArray,  NULL, 'A5');

        if(!$cliente){
            $sheet->setCellValue('A1', 'Cliente:');
            $sheet->setCellValue('B1', 'Todos');

            // guardar
            $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
            $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
            $excelWritter->save($randomFileName);

            // entregar la descarga al usuario
            return response()->download($randomFileName, "programacion $annoMesDia1.xlsx");

        }else{
            $sheet->setCellValue('A1', 'Cliente:');
            //obtener nombre cliente
            $sheet->setCellValue('B1', $cliente->nombre);

            // guardar
            $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
            $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
            $excelWritter->save($randomFileName);

            // entregar la descarga al usuario
            return response()->download($randomFileName, "programacion $cliente->nombre-$annoMesDia1.xlsx");
        }
    }

    /**
     * ##########################################################
     * funciones privadas
     * ##########################################################
     */

    //función filtra por mes y cliente
    private function inventariosPorMesYCliente($annoMesDia, $idCliente){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];

        $query = Inventarios::with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador'
        ])
            ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
            ->orderBy('fechaProgramada', 'asc');

        if($idCliente!=0) {
            $query->whereHas('local', function($q) use ($idCliente) {
                $q->where('idCliente', '=', $idCliente);
            });
        }
        return $query->get()->toArray();
    }

    //función filtra por rango de fecha y cliente
    private function inventariosPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente){
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
        ])
            ->where('fechaProgramada', '>=', $annoMesDia1)
            ->where('fechaProgramada', '<=', $annoMesDia2)
            ->orderBy('fechaProgramada', 'asc');

        if($idCliente!=0) {
            // Se filtran por cliente
            $query->whereHas('local', function ($q) use ($idCliente) {
                $q->where('idCliente', '=', $idCliente);
            });
        }
        return $query->get()->toArray();
    }

    //Function para validar que la fecha sea valida
    private function fecha_valida($fechaProgramada){
        $fecha = explode('-', $fechaProgramada);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        $dia = $fecha[2];

        return checkdate($mes,$dia,$anno);
    }
}