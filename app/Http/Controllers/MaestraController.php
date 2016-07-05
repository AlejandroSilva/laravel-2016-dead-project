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
// Modelos
use App\Clientes;
//use App\ProductoAF;
use App\AlmacenAF;

class MaestraController extends Controller {

    // GET maestra-produccion/leer          -- function temporal, eliminar
    function api_leer(){
        ini_set('memory_limit','1024M');
        $tableData = $this->leerArchivo('/home/asilva/Escritorio/maestraSEI.xls');
        $almacenDisponible = AlmacenAF::find(1);

        // todo surgio un "problema": activo fijo no tiene la misma maestra de inventario
        // maestra AF: 1 producto, una existencia
        // maestra INV: 1 producto, muchas existencias
        // transaccion, es muy comun tener datos duplicados en las maestras
        DB::transaction(function() use ($tableData, $almacenDisponible){
            foreach($tableData as $data) {
                $almacenDisponible->productosAF()->insert([
                    'codActivoFijo'=>$data['codigo_activo'],
                    'idAlmacenAF'=> 1,
                    'idLocal' => 1104,
                    'descripcion'=>$data['descripcion'],
                    'precio'=> isset($data['precio'])? $data['precio'] : 0,
                    'barra1'=> $data['barra1'],
                    'barra2'=> $data['barra2'],
                    'barra3'=> $data['barra3'],
                ]);
            }
        });
        $data = collect($tableData);
        return response()->json($data->count());
    }
    
    /**
     * ##########################################################
     * funciones privadas
     * ##########################################################
     */
    private function leerArchivo($inputFileName) {
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
        for ($row = 2; $row <= $highestRow; $row++){
            array_push($tableData, [
                'codigo_activo' =>  $sheet->getCell("A$row")->getValue(),
                'descripcion'=>     $sheet->getCell("B$row")->getValue(),
                'barra1'=>          $sheet->getCell("C$row")->getValue(),
                'barra2'=>          $sheet->getCell("D$row")->getValue(),
                'barra3'=>          $sheet->getCell("E$row")->getValue(),
                'precio'=>          $sheet->getCell("F$row")->getValue(),
            ]);
        }
        return $tableData;
    }
}
