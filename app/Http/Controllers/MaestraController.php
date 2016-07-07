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
use App\ArticuloAF;
use App\Clientes;
use App\CodigoBarra;
use App\ProductoAF;

class MaestraController extends Controller {

    // GET api/activo-fijo/cargar-productos         -- function temporal, eliminar
    function api_cargar_productos(){
        ini_set('memory_limit','1024M');
        $tableData = $this->leerArchivoProductos('/home/asilva/Escritorio/maestra_activo_fijo/SEI_productos.xlsx');

        // transaccion, es muy comun tener datos duplicados en las maestras
        DB::transaction(function() use ($tableData){
            foreach($tableData as $data) {
                ProductoAF::insert([
                    'SKU'=>$data['a'],
                    'descripcion'=>$data['b'],
                    'valorMercado'=> $data['c']
                ]);
            }
        });
        $data = collect($tableData);
        return response()->json($data->count());
    }

    // GET api/activo-fijo/cargar-articulos         -- function temporal, eliminar
    function api_cargar_articulos(){
        ini_set('memory_limit','1024M');
        $tableData = $this->leerArchivoProductos('/home/asilva/Escritorio/maestra_activo_fijo/SEI_articulos.xlsx');

        // transaccion, es muy comun tener datos duplicados en las maestras
        DB::transaction(function() use ($tableData){

            // insertar articulos
//            foreach($tableData as $data) {
//                ArticuloAF::insert([
//                    'codArticuloAF'=>$data['d'],
//                    'SKU'=>$data['a'],
//                    'idAlmacenAF'=>1,
//                    'fechaIncorporacion'=> '2016-07-07'
//                ]);
//            }

            // insertar codigos barra
            foreach($tableData as $data) {
//                if(isset($data['e']))
//                    CodigoBarra::insert([ 'barra' => $data['e'], 'codArticuloAF' => $data['d'] ]);
//                if(isset($data['f']))
//                    CodigoBarra::insert([ 'barra' => $data['f'], 'codArticuloAF' => $data['d'] ]);
                if(isset($data['g']))
                    CodigoBarra::insert([ 'barra' => $data['g'], 'codArticuloAF' => $data['d'] ]);
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
    private function leerArchivoProductos($inputFileName) {
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
                'a'=> $sheet->getCell("A$row")->getValue(),
                'b'=> $sheet->getCell("B$row")->getValue(),
                'c'=> $sheet->getCell("C$row")->getValue(),
                'd'=> $sheet->getCell("D$row")->getValue(),
                'e'=> $sheet->getCell("E$row")->getValue(),
                'f'=> $sheet->getCell("F$row")->getValue(),
                'g'=> $sheet->getCell("G$row")->getValue(),
                'h'=> $sheet->getCell("H$row")->getValue(),
            ]);
        }
        return $tableData;
    }
}
