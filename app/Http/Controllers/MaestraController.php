<?php

namespace App\Http\Controllers;

use App\AlmacenAF;
use Illuminate\Http\Request;
use App\Http\Requests;
// Carbon
use Carbon\Carbon;
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
                if(isset($data['e']))
                    CodigoBarra::insert([ 'barra' => $data['e'], 'codArticuloAF' => $data['d'] ]);
                if(isset($data['f']))
                    CodigoBarra::insert([ 'barra' => $data['f'], 'codArticuloAF' => $data['d'] ]);
                if(isset($data['g']))
                    CodigoBarra::insert([ 'barra' => $data['g'], 'codArticuloAF' => $data['d'] ]);
            }
        });
        $data = collect($tableData);
        return response()->json($data->count());
    }


    function api_cargar_maestra(){
        ini_set('memory_limit','1024M');
        $tableData = $this->leerArchivoProductos('/home/asilva/Escritorio/maestra_sei.xlsx');

        $almacenDisponible = AlmacenAF::find(1);
        // transaccion, es muy comun tener datos duplicados en las maestras
        DB::transaction(function() use ($tableData, $almacenDisponible){
            // agregar productos
            foreach($tableData as $data) {
                $producto = ProductoAF::find($data['a']);
                $prod = $producto;
                if(!$producto){
                    $prod = new ProductoAF([
                        'SKU'=>$data['a'],
                        'descripcion' => $data['b'],
                        'valorMercado' => $data['f']
                    ]);
                    $prod->save();
                }else{
                    $producto->descripcion = $data['b'];
                    $producto->valorMercado = $data['f'];
                    $producto->save();
                }

                // agregar articulo
                $articulo = new ArticuloAF([
                    'fechaIncorporacion' => Carbon::now(),
                    'stock' => $data['g'],
                    'SKU' => $prod->SKU
                ]);
                $articulo->save();

                // agregar barras
                if(isset($data['c']) && $data['c']!=""){
                    $codigo = new CodigoBarra(['barra'=> $data['c'], 'idArticuloAF' => $articulo->idArticuloAF]);
                    $codigo->save();
                }
                if(isset($data['d'])){
                    $codigo = new CodigoBarra(['barra'=> $data['d'], 'idArticuloAF' => $articulo->idArticuloAF]);
                    $codigo->save();
                }
                if(isset($data['e'])){
                    $codigo = new CodigoBarra(['barra'=> $data['e'], 'idArticuloAF' => $articulo->idArticuloAF]);
                    $codigo->save();
                }

                // agregar productos a almacen DISPONIBLE
                $almacenDisponible->articulos()->attach( $articulo->idArticuloAF, [
                    'stockActual' => $data['g']
                    ]
                );
            }
        });
        return response()->json(
            collect($tableData)->count()
        );
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
