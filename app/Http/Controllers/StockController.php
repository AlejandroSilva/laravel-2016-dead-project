<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// PHPExcel
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
// Modelos
use App\Locales;

class StockController extends Controller {

    function api_leerArchivo(){
        $tableData = $this->leerArchivo(public_path().'/actualizarStock/stockFCV-2016-05-31.xlsx');

        $data = collect($tableData);
        return response()->json(
            $data->map(function($row){
                return $this->actualizarLocal($row['idCliente'], $row['numero'], $row['stock'], $row['fechaStock']);
            })
        );
    }

    function actualizarLocal($idCliente, $numero, $stock, $fechaStock){
        // todo: validar que el stock y la $fechaStock sean validos

        $local = Locales::where('idCliente', $idCliente)
            ->where('numero', $numero)->first();
        // validar que el local exista
        if(!$local)
            return [
                'cliente'=>$idCliente,
                'local'=>$numero,
                'error' => 'El local buscado no se encuentra.',
                'estado' => '',
                'inventarios' => []
            ];
        else{
            // actualizar stock del local, el mismo metodo actualiza el stock de los inventarios asociados
            return $local->actualizarStock($stock, $fechaStock);
        }
    }


    function leerArchivo($inputFileName){
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //  Loop through each row of the worksheet in turn
        $tableData = [];
        // iniciar el 2, para saltarse el titulo
        for ($row = 2; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            //$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $A = $sheet->getCell("A$row");
            $B = $sheet->getCell("B$row");
            $C = $sheet->getCell("C$row");
            $D = $sheet->getCell("D$row");
            array_push($tableData, [
                'idCliente' => $A->getValue(),
                'numero' => $B->getValue(),
                'stock' => $C->getValue(),
                
                // TODO: EXISTE UN BUG ACA, LA FECHA SE PARSEA MAL, NO ES EXACTAMENTE EL MISMO DIA INDICADO EN EXCEL
                // la fecha por lo general es un (int), con esto se verifica y parsea el el formato correcto si es una fecha
                'fechaStock' => PHPExcel_Shared_Date::isDateTime($D)?
                    date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($D->getValue()))
                    :
                    $D->getValue(),
            ]);
        }
        return $tableData;
    }
}
