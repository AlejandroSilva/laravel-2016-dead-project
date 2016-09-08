<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// DB
use DB;
// PHPExcel
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;

class UtilConsolidadoController extends Controller {

    public function actualizarConsolidado(Request $request) {
        DB::beginTransaction();
        try {
            $nombreArchivo = $request->get('archivo');
            if(!isset($nombreArchivo))
                return response()->json(['debe ingresar un archivo']);

            ini_set('memory_limit','2000M');
            set_time_limit(0);

            $tableData = $this->leerConsolidado($nombreArchivo);
            foreach ($tableData as $data){
                // si tiene una fecha nueva, actualizarla
                if($data->expiracionNuevo!=null) {
                   $query = "update SEI_INVENTARIO.archivo_auditoria_fecha set fecha_exp='FECHA NUEVA2'
                             where barra = '$data->barra'
                             and rut_auditor = '$data->rutAuditor'
                             and rut_qf = '$data->rutQF'
                             and tienda=$data->tienda
                             and fecha_auditoria BETWEEN '2016-08-01' AND '2016-08-31'";
                    DB::update(DB::raw($query));
                }
                if($data->loteNuevo!=null){
                    $query = "update SEI_INVENTARIO.archivo_auditoria_fecha set lote='LOTE NUEVO2'
                             where barra = '$data->barra'
                             and rut_auditor = '$data->rutAuditor'
                             and rut_qf = '$data->rutQF'
                             and tienda=$data->tienda
                             and fecha_auditoria BETWEEN '2016-08-01' AND '2016-08-31'";
                    DB::update(DB::raw($query));
                }
            }

            DB::commit();
            return response()->json('ok');
        } catch (EXCEPTION $e) {
            DB::rollback();
            throw $e;
            return response()->json($e->message);
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
                // A 1
                // B TIENDA
                'tienda' => $sheet->getCell("B$rowIndex")->getValue(),
                // C FECHA          ****
                // D AUDITOR
                // E RUT AUDITOR    ****
                // F NOMBRE QF
                // G RUT QF         ****
                // 'fecha'=> $sheet->getCell("C$rowIndex")->getValue(),
                'rutAuditor' => $sheet->getCell("E$rowIndex")->getValue(),
                'rutQF'=> $sheet->getCell("G$rowIndex")->getValue(),
                // H ITEMS
                // I SKU
                // J BARRA
                'barra'=> $sheet->getCell("J$rowIndex")->getValue(),
                // K DESCRIPTOR
                // L LABORATORIO
                // M LOTE           *****
                // N NUEVO LOTE     *****
                'loteAntiguo'=> $sheet->getCell("M$rowIndex")->getValue(),
                'loteNuevo'=> $sheet->getCell("N$rowIndex")->getValue(),

                // O FECHA EXP      *****
                // P NUEVA FECHA    *****
                'expiracionAntiguo'=> $sheet->getCell("O$rowIndex")->getValue(),
                'expiracionNuevo'=> $sheet->getCell("P$rowIndex")->getValue(),
                // Q IMAGEN
                // R HORA
                // S ??
                // T RUT Julio
            ];
            // si la fila tiene una modificacion, entonces se agrega a la lista
            if($rowData->loteNuevo!=null || $rowData->expiracionNuevo!=null){
                array_push($tableData, $rowData);
            }
        }
        return $tableData;
    }
}
