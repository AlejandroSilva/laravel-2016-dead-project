<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Redirect;
use Log;
// Carbon
use Carbon\Carbon;
// Crypt
use Crypt;
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
        
        return view('operacional.programacionIG.programacionIG-mensual', [
            'puedeAgregarInventarios'   => $user->can('programaInventarios_agregar')? "true":"false",
            'puedeModificarInventarios' => $user->can('programaInventarios_modificar')? "true":"false",
            'clientes' => Clientes::todos_conLocales(),
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
            // asignar la jornada que tenga por defecto el local
            $idJornada = $local->idJornadaSugerida;
            $inventario->idJornada = $idJornada;

            $inventario->fechaProgramada = $request->fechaProgramada;
            $inventario->stockTeorico = $local->stock;
            $inventario->fechaStock =   $local->fechaStock;

            // Crear las dos nominas
            $nominaDia = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaDia->horaPresentacionLider = $local->llegadaSugeridaLiderDia();
            $nominaDia->horaPresentacionEquipo = $local->llegadaSugeridaPersonalDia();
            // Todo: la dotacion sugerida deberia dividirse en dos cuando la jornada sea doble:
            $nominaDia->dotacionTotal = $inventario->dotacionTotalSugerido();
            $nominaDia->dotacionOperadores = $inventario->dotacionOperadoresSugerido();
            // si la jornada es de "dia"(2), o "dia y noche"(4), entonces la nomina esta habilitada
            $nominaDia->habilitada = ($idJornada==2 || $idJornada==4);
            $nominaDia->idEstadoNomina = 2; // pendiente
            $nominaDia->turno = 'Día';
            $nominaDia->save();

            $nominaNoche = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaNoche->horaPresentacionLider = $local->llegadaSugeridaLiderNoche();
            $nominaNoche->horaPresentacionEquipo = $local->llegadaSugeridaPersonalNoche();
            // Todo: la dotacion sugerida deberia dividirse en dos cuando la jornada sea doble:
            $nominaNoche->dotacionTotal = $inventario->dotacionTotalSugerido();
            $nominaNoche->dotacionOperadores = $inventario->dotacionOperadoresSugerido();
            // si la jornada es de "noche"(3), o "dia y noche"(4), entonces la nomina esta habilitada
            $nominaNoche->idEstadoNomina = ($idJornada==3 || $idJornada==4)? 2 : 1;
            $nominaNoche->idEstadoNomina = 2; // pendiente
            $nominaNoche->turno = 'Noche';
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
            if(isset($request->fechaProgramada)){
                // actualizar fecha (si es valida) y generar un log
                $inventario->actualizarFechaProgramada($request->fechaProgramada);
            }

            if(isset($request->idJornada)){
                // cambia la jornada del inventario, y cambiar el estado (habilitada) de las nominas asociadas
                $inventario->actualizarJornada($request->idJornada);
            }
            if(isset($request->stockTeorico)) {
                // actualizar el stock del local, y al mismo tiempo recalcular la dotacion de las nominas
                $inventario->actualizarStock($request->stockTeorico, Carbon::now());
            }

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

    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA
     * ##########################################################
     */

    // POST api/inventario/informar-archivo-final
    function api_informarArchivoFinal(Request $request){
        // agrega cabeceras para las peticiones con CORS
        header('Access-Control-Allow-Origin: *');

        $idCliente= $request->idCliente;
        $ceco = $request->ceco;
        $fechaProgramada = $request->fechaProgramada;
        $unidadesReal = $request->unidadesReal;
        $unidadesTeorico = $request->unidadesTeorico;
        $stringPeticion = "CECO:'$ceco' idCliente:'$idCliente' fechaProgamada:'$fechaProgramada' Unidades:'$unidadesReal'' U.Teorico:'$unidadesTeorico'";

        // Validar los campos
        $validator = Validator::make([
                'idCliente' => $idCliente,
                'ceco' => $ceco,
                'fechaProgramada' => $fechaProgramada,
                'unidadesReal' => $unidadesReal,
                'unidadesTeorico' => $unidadesTeorico
            ],
            [
                'idCliente' => 'required|numeric',
                'ceco' => 'required|numeric',
                'fechaProgramada' => 'required|date',
                'unidadesReal' => 'required|numeric',
                'unidadesTeorico' => 'required|numeric'
            ]
        );
        if($validator->fails()){
            $error = $validator->messages();
            Log::info("[INVENTARIO:INFORMAR_FINAL:ERROR] $stringPeticion. Validador: $error");
            return response()->json($error, 400);
        }

        // Buscar el inventario
        $inventario = Inventarios::with(['local'])
            ->whereHas('local', function($q) use ($ceco, $idCliente) {
                $q  ->where('numero', $ceco)
                    ->where('idCliente', $idCliente);
            })
            ->where('fechaProgramada', $fechaProgramada)
            ->first();
        if(!$inventario){
            $msg = "[INVENTARIO:INFORMAR_FINAL:ERROR] $stringPeticion. Inventario no encontrado ";
            Log::info($msg);
            return response()->json('inventario no encontrado', 400);
        }

        // Actualizar los datos
        $inventario->unidadesReal = $unidadesReal;
        $inventario->unidadesTeorico = $unidadesTeorico;
        $inventario->fechaToma = $fechaProgramada;  // Siempre la fecha de programada es la misma que la fecha de toma?
        $inventario->save();

        Log::info("[INVENTARIO:INFORMAR_FINAL:OK] $stringPeticion");
        return response()->json(Inventarios::find($inventario->idInventario), 200);
    }

    /**
     * ##########################################################
     * Descarga de documentos
     * ##########################################################
     */

    // GET /pdf/inventarios/{mes}/cliente/{idCliente}
    public function descargarPDF_porMes($annoMesDia, $idCliente){
        //Se utiliza funcion privada que recorre inventarios por mes y dia
        $inventarios = $this->buscarInventarios(null, null, $annoMesDia, $idCliente, null, null, null);

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
        $inventarios = $this->buscarInventarios($fechaInicio, $fechaFin, null, $idCliente, null, null);

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

    // GET inventarios/buscar
    function api_buscar(Request $request){
        // agrega cabeceras para las peticiones con CORS
        header('Access-Control-Allow-Origin: *');

        $fechaInicio = $request->query('fechaInicio');
        $fechaFin = $request->query('fechaFin');
        $mes = $request->query('mes');
        $idCliente = $request->query('idCliente');
        $idLider = $request->query('idLider');

        $inventarios = $this->buscarInventarios($fechaInicio, $fechaFin, $mes, $idCliente, $idLider, null);
        return response()->json($inventarios, 200);
    }
    public function buscarInventarios($fechaInicio, $fechaFin, $mes, $idCliente, $idLider, $fechaSubidaNomina){
        $query = Inventarios::withTodo();
        
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
            $inventarios = collect($inventarios)->filter(function($inventario) use ($idLider){
                $jornadaInventario = $inventario['idJornada'];
                $liderDia = $inventario['nomina_dia']['idLider'];
                $liderNoche = $inventario['nomina_noche']['idLider'];

                // 1="no definido", 2="dia", 3="noche", 4="dia y noche"
                if ($jornadaInventario == 2 && ($liderDia==$idLider)) {
                    // si es "dia", solo puede estar asignado a la nomina de dia
                    return true;
                } else if ($jornadaInventario == 3 && ($liderNoche==$idLider)) {
                    // si es "noche", solo puede estar asignado a la nomina de noche
                    return true;
                } else if ($jornadaInventario == 4 && ( ($liderDia==$idLider) || ($liderNoche==$idLider) )) {
                    // si la jornada es "dia noche", puede ser lider de cualquiera de las dos nominas
                    return true;
                } else {
                    // si no tiene nominas asignadas, no es lider de ninguna
                    return false;
                }
            })->toArray();
        }

        // Filtrar por fechaSubidaNomina
        if(isset($fechaSubidaNomina)){
            $inventarios = collect($inventarios)->filter(function($inventario) use ($fechaSubidaNomina){
                $fechaSubidaDia = $inventario['nomina_dia']['fechaSubidaNomina'];
                $fechaSubidaNoche = $inventario['nomina_noche']['fechaSubidaNomina'];

                // la fecha buscada debe ser igual a fecha de subida de la nomina de dia O la fecha de subida de la nomina de noche
                return $fechaSubidaDia==$fechaSubidaNomina || $fechaSubidaNoche==$fechaSubidaNomina;
            })->toArray();
        }

        // "Temporal": agregar a la nomina el campo publicIdNomina
        $inventarios = collect($inventarios)->map(function($inventario) use ($idLider){
            $inventario['nomina_dia']['publicIdNomina']   = Crypt::encrypt($inventario['nomina_dia']['idNomina']);
            $inventario['nomina_noche']['publicIdNomina'] = Crypt::encrypt($inventario['nomina_noche']['idNomina']);
            return $inventario;
        })->toArray();

        // retornar una collection, igual que el query original
        return collect($inventarios);
    }
    
    // Función generica para generar el archivo excel
    private function generarWorkbook($inventarios){
        $inventarios = $inventarios->toArray();
        $inventariosHeader = ['Fecha', 'Cliente', 'CECO', 'Local', 'Región', 'Comuna', 'Stock', 'Fecha stock', 'Dirección'];

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

    // utilizadas por el CRON para mostrar las NominasPendientes (dejar de ocupa esto, eliminar...)
    public function buscarInventarios_conFormato($fechaInicio, $fechaFin, $mes, $idCliente, $idLider, $fechaSubidaNomina){
        $inventarios = $this->buscarInventarios($fechaInicio, $fechaFin, $mes, $idCliente, $idLider, $fechaSubidaNomina);
        // se parsean los usuarios con el formato "estandar"
        return $inventarios_formato = $inventarios->map([$this, 'darFormatoInventario']);
    }
    public function darFormatoInventario($inventario){
        // eliminar esto, y utilizarr el fomrato creado en "inventarios"
        return [
            // Informacion del inventario
            'idInventario' => $inventario['idInventario'],
            'idJornada' => $inventario['idJornada'],
            'inventario_fechaProgramada' => $inventario['fechaProgramada'],
            'inventario_stockTeorico' => $inventario['stockTeorico'],
            'inventario_fechaStock' => $inventario['fechaStock'],
            'inventario_dotacionAsignadaTotal' => $inventario['dotacionAsignadaTotal'],
            // Local
            'idLocal' => $inventario['idLocal'],
            'local_numero' => $inventario['local']['numero'],
            'local_nombre' => $inventario['local']['nombre'],

            // Cliente
            'idCliente' => $inventario['local']['idCliente'],
            'cliente_nombreCorto' => $inventario['local']['cliente']['nombreCorto'],

            // Formato Local
            'idFormatoLocal' => $inventario['local']['idFormatoLocal'],
            'formatoLocal_nombre' => $inventario['local']['formato_local']['nombre'],
            'formatoLocal_produccionSugerida' => $inventario['local']['formato_local']['produccionSugerida'],

            'direccion' => $inventario['local']['direccion']['direccion'],
            // Comuna
            'cutComuna' => $inventario['local']['direccion']['cutComuna'],
            'comuna_nombre' => $inventario['local']['direccion']['comuna']['nombre'],
            // Region
            'cutRegion' => $inventario['local']['direccion']['comuna']['provincia']['cutRegion'],
            'region_numero' => $inventario['local']['direccion']['comuna']['provincia']['region']['numero'],

            // nomina Dia
            'idNominaDia' => $inventario['idNominaDia'],
            'nominaDia_horaPresentacionLider' => $inventario['nomina_dia']['horaPresentacionLider'],
            'nominaDia_horaPresentacionEquipo' => $inventario['nomina_dia']['horaPresentacionEquipo'],
            'nominaDia_dotacionAsignada' => $inventario['nomina_dia']['dotacionAsignada'],
            'nominaDia_fechaSubidaNomina' => $inventario['nomina_dia']['fechaSubidaNomina'],
            // Lider Dia
            'nominaDia_idLider' => $inventario['nomina_dia']['idLider'],
            'nominaDia_lider_nombre' => trim($inventario['nomina_dia']['lider']['nombre1']." ".$inventario['nomina_dia']['lider']['apellidoPaterno']),
            // Captador Dia
            'nominaDia_idCaptador' => $inventario['nomina_dia']['idCaptador1'],
            'nominaDia_captador_nombre' => trim($inventario['nomina_dia']['captador']['nombre1']." ".$inventario['nomina_dia']['captador']['apellidoPaterno']),

            // nomina Noche
            'idNominaNoche' => $inventario['idNominaNoche'],
            'nominaNoche_horaPresentacionLider' => $inventario['nomina_noche']['horaPresentacionLider'],
            'nominaNoche_horaPresentacionEquipo' => $inventario['nomina_noche']['horaPresentacionEquipo'],
            'nominaNoche_dotacionAsignada' => $inventario['nomina_noche']['dotacionAsignada'],
            'nominaNoche_fechaSubidaNomina' => $inventario['nomina_noche']['fechaSubidaNomina'],
            // Lider Noche
            'nominaNoche_idLider' => $inventario['nomina_noche']['idLider'],
            'nominaNoche_lider_nombre' => trim($inventario['nomina_noche']['lider']['nombre1']." ".$inventario['nomina_noche']['lider']['apellidoPaterno']),
            // Captador Dia
            'nominaNoche_idCaptador' => $inventario['nomina_noche']['idCaptador1'],
            'nominaNoche_captador_nombre' => trim($inventario['nomina_noche']['captador']['nombre1']." ".$inventario['nomina_noche']['captador']['apellidoPaterno']),
        ];
    }

    /* ***************************************************/

    function buscarNUEVO($peticion){
        $query = Inventarios::with([]);

        // Buscar por Fecha de Inicio
        if(isset($peticion->fechaInicio)){
            $query->where('fechaProgramada', '>=', $peticion->fechaInicio);
        }
        // Buscar por Fecha de Fin
        if(isset($peticion->fechaFin)){
            $query->where('fechaProgramada', '<=', $peticion->fechaFin);
        }

        // Buscar por idCaptador1
        $idCaptador1 = $peticion->idCaptador1;
        if(isset($idCaptador1)){
            // buscar el captador en la nomina de "dia" O en la de "noche", la nomina debe estar "Habilitada"
            $query
                ->whereHas('nominaDia', function($q) use ($idCaptador1){
                    $q->where('idCaptador1', $idCaptador1)->where('habilitada', true);
                })
                ->orWhereHas('nominaNoche', function($q) use ($idCaptador1){
                    $q->where('idCaptador1', $idCaptador1)->where('habilitada', true);
                });
        }

        $query->orderBy('fechaProgramada');
        return $query->get();
    }
}