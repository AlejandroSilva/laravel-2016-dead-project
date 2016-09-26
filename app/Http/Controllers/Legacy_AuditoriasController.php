<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// Modelos
use App\Auditorias;
use App\Clientes;
// PHP Excel
use PHPExcel;
use PHPExcel_IOFactory;
use App\User;

class Legacy_AuditoriasController extends Controller {
    // * ##########################################################
    // *        API DE INTERACCION CON LA OTRA PLATAFORMA
    // * ##########################################################

    // GET api/auditoria/{fecha1}/al/{fecha2}/auditor/{idAuditor}
    function api_getPorRangoYAuditor($annoMesDia1, $annoMesDia2, $idAuditor){
        // si el $idAuditor es 0, se muestran todas las autidorias en un periodo
        if($idAuditor==0){
            $auditorias = $this->buscarPorRangoYAuditor($annoMesDia1, $annoMesDia2, 0);
            return response()->json($auditorias, 200);
        }

        // validar que el usuario exista
        if(User::find($idAuditor)){
            $auditorias = $this->buscarPorRangoYAuditor($annoMesDia1, $annoMesDia2, $idAuditor);
            return response()->json($auditorias, 200);
        }else{
            return response()->json(['msg'=>'el usuario indicado no existe'], 404);
        }
    }

    // * ##########################################################
    // *                DESCARGA DE DOCUMENTOS
    // * ##########################################################

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


    // * ##########################################################
    // *                   FUNCIONES PRIVADAS
    // * ##########################################################

    // se usa para la busqueda en la plataforma "sig"
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

    // se usa en la plataforma "inventario"
    private function buscarPorRangoYAuditor($annoMesDia1, $annoMesDia2, $idAuditor){
        $query = Auditorias::with([
            'local.cliente',
            'local.direccion.comuna.provincia.region',
            'auditor'
        ])
            ->where('fechaProgramada', '>=', $annoMesDia1)
            ->where('fechaProgramada', '<=', $annoMesDia2);

        // Se filtran por auditor si esta definido
        if($idAuditor!=0){
            $query->whereHas('local', function($q) use ($idAuditor){
                $q->where('idAuditor', '=', $idAuditor);
            });
        }

        return $query
            ->orderBy('fechaProgramada', 'ASC')
            ->orderBy('idLocal')
            ->get()
            ->toArray();
    }

    // funcion para general el excel
    private function generarWorkbook($auditorias){
        //$formatoLocal = FormatoLocales::find();
        $auditoriasHeader = ['Fecha Programada', 'Fecha Auditoría', 'Hora presentación', 'Realizada', 'Aprobada', 'Cliente', 'CECO', 'Local', 'Stock', 'Fecha stock', 'Auditor', 'Dirección', 'Región', 'Nombre región', 'Provincia', 'Comuna', 'Hora apertura', 'Hora cierre', 'Email', 'Teléfono 1', 'Teléfono 2'];

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
                $auditoria['local']['direccion']['comuna']['provincia']['region']['numero'],
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
