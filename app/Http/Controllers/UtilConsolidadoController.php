<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// PHPExcel
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;

class UtilConsolidadoController extends Controller {

    public function actualizarConsolidado(Request $request) {
        // antes de cargar el archivo, es buena idea exportar el xlsx a csv, abrir el csv y transformarlo nuevamente
        // a xlsx. De esta forma se puede reducir hasta un 60% el tamaño del archivo, entonces la carga es mas rapida

        try {
            $nombreArchivo = $request->get('archivo');
            if(!isset($nombreArchivo))
                return response()->json(['debe ingresar un archivo']);

            ini_set('memory_limit','2000M');
            set_time_limit(0);

            $tableData = $this->leerConsolidado($nombreArchivo);
            $query = '';
            foreach ($tableData as $data){
                // si tiene una fecha nueva, actualizarla
                if($data->expiracionNuevo!=null) {
                    $query = $query." update SEI_INVENTARIO.archivo_auditoria_fecha set fecha_exp='$data->expiracionNuevo' ".
                             "where barra = '$data->barra' ".
                             "and tienda=$data->tienda ".
                             "and fecha_auditoria BETWEEN '2016-08-01' AND '2016-08-31'; ";
                }
                if($data->loteNuevo!=null){
                    $query = $query." update SEI_INVENTARIO.archivo_auditoria_fecha set lote='$data->loteNuevo' ".
                             "where barra = '$data->barra' ".
                             "and tienda=$data->tienda ".
                             "and fecha_auditoria BETWEEN '2016-08-01' AND '2016-08-31'; ";
                }
            }
            return response()->json($query);
        } catch (EXCEPTION $e) {
            throw $e;
            return response()->json('excepcion '.$e->message);
        }
    }


    private function leerConsolidado($inputFileName) {
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            return [];
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        //  Loop through each row of the worksheet in turn
        $tableData = [];
        // iniciar el 2, para saltarse el titulo
        for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++){
            $rowData = (object)[
                'tienda' => $sheet->getCell("A$rowIndex")->getValue(),
                'barra'=> $sheet->getCell("J$rowIndex")->getValue(),
                'loteNuevo'=> $sheet->getCell("N$rowIndex")->getValue(),
                'expiracionNuevo'=> $sheet->getCell("P$rowIndex")->getValue(),
            ];
            // si la fila tiene una modificacion, entonces se agrega a la lista
            if($rowData->loteNuevo!=null || $rowData->expiracionNuevo!=null){
                array_push($tableData, $rowData);
            }
        }
        return $tableData;
    }
}
