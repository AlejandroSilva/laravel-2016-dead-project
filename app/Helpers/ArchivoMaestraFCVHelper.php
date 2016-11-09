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
            $sku                       = isset($array[$row]['A'])? trim($array[$row]['A']) : null;
            $descriptor                = isset($array[$row]['B'])? trim($array[$row]['B']) : null;
            $barra                     = isset($array[$row]['C'])? trim($array[$row]['C']) : null;
            $laboratorio               = isset($array[$row]['D'])? trim($array[$row]['D']) : null;
            $clasificacionTerapeutica  = isset($array[$row]['E'])? trim($array[$row]['E']) : null;

            // puede existir unn caso, en que un row este lleno de "espacios", y se lea incorrectamente como un producto
            // si al menos un campo es distinto de null y de '', entonces la fila es "valida" (aunque no tenga todos los campos
            if($sku!=null || $descriptor!=null || $barra!=null || $laboratorio!=null || $clasificacionTerapeutica!=null){
                $productos[] = [
                    'idArchivoMaestra'          => $idArchivoMaestra,
                    'sku'                       => $sku,
                    'descriptor'                => $descriptor,
                    'barra'                     => $barra,
                    'laboratorio'               => $laboratorio,
                    'clasificacionTerapeutica'  => $clasificacionTerapeutica,
                    'created_at'                => $now,
                    'updated_at'                => $now
                ];
            }
        }
        return $productos;
    }
}