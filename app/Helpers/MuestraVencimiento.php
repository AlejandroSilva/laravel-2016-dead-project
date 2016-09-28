<?php
// Carbon
use Carbon\Carbon;

class MuestraVencimiento{

    // utilizado por: ArchivoFinalInventarioController::api_uploadFCV
    static function parsearArrayMuestraFCV($array, $idArchivo){
        $totalRows = count($array);
        $now = Carbon::now()->toDateTimeString();

        $datos = [];
        $error = '';
        // iniciar el 2, para saltarse el titulo
        for ($row = 2; $row <= $totalRows; $row++){
            $ceco = isset($array[$row]['A'])? $array[$row]['A'] : null;
            $codigo_producto = isset($array[$row]['B'])? $array[$row]['B'] : null;
            $descriptor = isset($array[$row]['C'])? $array[$row]['C'] : null;

            // validar de que los campos existan
            $rowValido = self::_camposValidos($row, $ceco, $codigo_producto, $descriptor);
            if( $rowValido!=null )
                $error = $error.$rowValido; // si se concatena un null con un string, el resultado es un string

            array_push($datos, [
                'idArchivoMuestraVencimientoFCV' => $idArchivo,
                'ceco' => $ceco,
                'codigo_producto' => $codigo_producto,
                'descriptor' => $descriptor,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        };
        return (object)[
            'error' => $error,
            'datos' => $datos
        ];
    }
    private static function _camposValidos($row, $ceco, $codigo_producto, $descriptor){
        // todo: validar el tipo, que no sean string vacios, que sean numeros, etc
        if($ceco==null)
            return "En la fila $row, falta el campo 'ceco'. ";
        if($codigo_producto==null)
            return "En la fila $row, falta el campo 'codigo_producto'. ";
        if($descriptor==null)
            return "En la fila $row, falta el campo 'descriptor'. ";
        return null;
    }
}