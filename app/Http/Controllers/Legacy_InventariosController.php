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
// Auth
use App\Role;
use Auth;

class Legacy_InventariosController extends Controller {

    // * ##########################################################
    // *        API DE INTERACCION CON LA OTRA PLATAFORMA
    // * ##########################################################

    // GET inventarios/buscar
    function api_publica_buscar(Request $request){
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

    // POST api/inventario/informar-archivo-final
    function api_publica_informarArchivoFinal(Request $request){
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

    // * ##########################################################
    // *                DESCARGA DE DOCUMENTOS
    // * ##########################################################

    // GET /pdf/inventarios/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}
    public function descargarPDF_porRango($fechaInicio, $fechaFin, $idCliente){
        //Se utiliza funcion privada que recorre inventarios por fecha inicio-final y por idCliente
        $inventarios = $this->buscarInventarios($fechaInicio, $fechaFin, null, $idCliente, null, null);

        // nombre del cliente (si existe)
        $cliente = Clientes::find($idCliente);
        $nombreCliente = $cliente? $cliente->nombre : 'Todos';

        // generar el archivo
        $workbook = $this->_generarWorkbook($inventarios);
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

    // GET /pdf/inventarios/{mes}/cliente/{idCliente}
    public function descargarPDF_porMes($annoMesDia, $idCliente){
        //Se utiliza funcion privada que recorre inventarios por mes y dia
        $inventarios = $this->buscarInventarios(null, null, $annoMesDia, $idCliente, null, null, null);

        // nombre del cliente (si existe)
        $cliente = Clientes::find($idCliente);
        $nombreCliente = $cliente? $cliente->nombre : 'Todos';

        // generar el archivo
        $workbook = $this->_generarWorkbook($inventarios);
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

    // Función generica para generar el archivo excel
    private function _generarWorkbook($inventarios){
        $inventarios = $inventarios->toArray();
        $inventariosHeader = [
            // Local
            'Fecha', 'Cliente', 'CECO', 'Local', 'Región', 'Comuna', 'Dirección', 'Stock', 'Fecha stock', 'PTT',
            // Nomina Dia
            'Dot. Total', 'Dot.Operadores', 'Líder', 'Supervisor', 'Supervisor', 'Hr.Líder', 'Hr.Equipo', 'estado Nomina',
            // Nomina Noche
            'Dot. Total', 'Dot.Operadores', 'Líder', 'Supervisor', 'Supervisor', 'Hr.Líder', 'Hr.Equipo', 'estado Nomina',
        ];

        $inventariosArray = array_map(function($inventario){
            $diaHabilitada = $inventario['nomina_dia']['habilitada']==1;
            $nocheHabilitada = $inventario['nomina_noche']['habilitada']==1;
            return [
                // Local (A-J)
                $inventario['fechaProgramada'],
                $inventario['local']['cliente']['nombreCorto'],
                $inventario['local']['numero'],
                $inventario['local']['nombre'],
                $inventario['local']['direccion']['comuna']['provincia']['region']['numero'],
                $inventario['local']['direccion']['comuna']['nombre'],
                $inventario['local']['direccion']['direccion'],
                $inventario['local']['stock'],
                $inventario['fechaStock'],
                $inventario['patentes'],
                // Nomina Dia (K-R)
                $diaHabilitada? $inventario['nomina_dia']['dotacionTotal'] : '',
                $diaHabilitada? $inventario['nomina_dia']['dotacionOperadores'] : '',
                // si la nomina esta habilitada, y tiene un lider/supervisor/captador asignado, entonces mostrar su nombre y apellido
                ($diaHabilitada && $inventario['nomina_dia']['lider']!=null)?
                    $inventario['nomina_dia']['lider']['nombre1'].' '.$inventario['nomina_dia']['lider']['apellidoPaterno'] : '',
                ($diaHabilitada && $inventario['nomina_dia']['supervisor']!=null)?
                    $inventario['nomina_dia']['supervisor']['nombre1'].' '.$inventario['nomina_dia']['supervisor']['apellidoPaterno'] : '',
                ($diaHabilitada && $inventario['nomina_dia']['captador']!=null)?
                    $inventario['nomina_dia']['captador']['nombre1'].' '.$inventario['nomina_dia']['captador']['apellidoPaterno'] : '',
                $diaHabilitada? $inventario['nomina_dia']['horaPresentacionLider'] : '',
                $diaHabilitada? $inventario['nomina_dia']['horaPresentacionEquipo'] : '',
                $diaHabilitada? $inventario['nomina_dia']['idEstadoNomina'] : '',
                // Nomina Noche (S-Z)
                $nocheHabilitada? $inventario['nomina_noche']['dotacionTotal'] : '',
                $nocheHabilitada? $inventario['nomina_noche']['dotacionOperadores'] : '',
                ($nocheHabilitada && $inventario['nomina_noche']['lider']!=null)?
                    $inventario['nomina_noche']['lider']['nombre1'].' '.$inventario['nomina_noche']['lider']['apellidoPaterno'] : '',
                ($nocheHabilitada && $inventario['nomina_noche']['supervisor']!=null)?
                    $inventario['nomina_noche']['supervisor']['nombre1'].' '.$inventario['nomina_noche']['supervisor']['apellidoPaterno'] : '',
                ($nocheHabilitada && $inventario['nomina_noche']['captador']!=null)?
                    $inventario['nomina_noche']['captador']['nombre1'].' '.$inventario['nomina_noche']['captador']['apellidoPaterno'] : '',
                $nocheHabilitada? $inventario['nomina_noche']['horaPresentacionLider'] : '',
                $nocheHabilitada? $inventario['nomina_noche']['horaPresentacionEquipo'] : '',
                $nocheHabilitada? $inventario['nomina_noche']['idEstadoNomina'] : '',
            ];
        }, $inventarios);

        // Nuevo archivo
        $workbook = new PHPExcel();  // workbook
        $sheet = $workbook->getActiveSheet();
        $boldStyle = [
            'font'  => [
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'size'  => 12,
                'name'  => 'Verdana'
            ]
        ];
        $hora = date('d/m/Y h:i:s A',time()-10800);

        $sheet->getStyle('A5:AR5')->applyFromArray($boldStyle);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);

        // Nomina Dia
        $sheet->mergeCells('K4:R4');
        $sheet->setCellValue('K4', 'Nómina de Día');
        $sheet->getStyle('K4')->applyFromArray($boldStyle);
        //$sheet->getStyle("K4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->getColumnDimension('R')->setAutoSize(true);

        // Nomina Noche
        $sheet->mergeCells('S4:Z4');
        $sheet->setCellValue('S4', 'Nómina de Noche');
        $sheet->getStyle('S4')->applyFromArray($boldStyle);
        //$sheet->getStyle('S4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->getColumnDimension('V')->setAutoSize(true);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        $sheet->getColumnDimension('X')->setAutoSize(true);
        $sheet->getColumnDimension('Y')->setAutoSize(true);
        $sheet->getColumnDimension('Z')->setAutoSize(true);

        $sheet->setCellValue('F1', 'Generado el:');
        $sheet->setCellValue('G1', $hora);
        $sheet->fromArray($inventariosHeader, NULL, 'A5');
        $sheet->fromArray($inventariosArray,  NULL, 'A6');

        return $workbook;
    }

    // * ##########################################################
    // *                   FUNCIONES PRIVADAS
    // * ##########################################################

    public function buscarInventarios($fechaInicio, $fechaFin, $mes, $idCliente, $idLider, $fechaSubidaNomina){
        $query = \App\Inventarios::withTodo();

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
            $inventario['nomina_dia']['publicIdNomina']   = \Crypt::encrypt($inventario['nomina_dia']['idNomina']);
            $inventario['nomina_noche']['publicIdNomina'] = \Crypt::encrypt($inventario['nomina_noche']['idNomina']);
            // calculo de las patentes (si es FCV(2) y FSB(5), se calcula por 44, otros clientes es por 110
            $idCliente = $inventario['local']['idCliente'];
            $inventario['patentes'] = ($idCliente==2 || $idCliente==5)?
                round($inventario['stockTeorico']/44) :
                round($inventario['stockTeorico']/110);
            return $inventario;
        })->toArray();

        // retornar una collection, igual que el query original
        return collect($inventarios);
    }
}
