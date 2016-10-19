<?php
// Carbon
use Carbon\Carbon;

class ArchivoMaestraFCVHelper{
    static function parsearExcelAProductos($excelPath, $idArchivoMaestra){
        // paso 1) de Excel a Array
        $resultadoExcel = \ExcelHelper::leerExcel($excelPath);
        if(isset($resultadoExcel->error))
            return (object)[
                'error' => $resultadoExcel->error
            ];

        // Parsear los datos del archivo
        $productos = \ArchivoMaestraFCVHelper::_array_a_productos($resultadoExcel->datos, $idArchivoMaestra);
        return (object)[
            'productos' => $productos
        ];
    }

    static function _array_a_productos($array, $idArchivoMaestra){
        $now = Carbon::now()->toDateTimeString();
        $highestRow = count($array);

        $productos = [];
        for( $row=2; $row<=$highestRow; $row++ ){
            $productos[] = [
                'idArchivoMaestra'          => $idArchivoMaestra,
                'sku'                       => isset($array[$row]['A'])? trim($array[$row]['A']) : null,
                'descriptor'                => isset($array[$row]['B'])? trim($array[$row]['B']) : null,
                'barra'                     => isset($array[$row]['C'])? trim($array[$row]['C']) : null,
                'laboratorio'               => isset($array[$row]['D'])? trim($array[$row]['D']) : null,
                'clasificacionTerapeutica'  => isset($array[$row]['E'])? trim($array[$row]['E']) : null,
                'created_at'                => $now,
                'updated_at'                => $now
            ];
        }
        return $productos;
    }
}