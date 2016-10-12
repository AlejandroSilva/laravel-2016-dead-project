<?php
// Carbon
use Carbon\Carbon;
use App\MaestraFCV;

class ArchivoMaestraFCVHelper{
    static function moverAcarpeta($archivo){
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $nombreOriginal = $archivo->getClientOriginalName();
        $fileName = "[$timestamp] $nombreOriginal";
        $path = public_path()."/FCV/maestrasFCV/";
        // guardar el archivo en una carpeta publica, y cambiar los permisos para que el grupo pueda modifiarlos
        $archivo->move( $path, $fileName);

        chmod($path.$fileName, 0774);   // 0744 por defecto
        return [
            'nombre_archivo' => $fileName,
            'nombre_original' => $nombreOriginal,
        ];
    }
    static function parseo($arrayArchivo, $idArchivo){
        $now = Carbon::now()->toDateTimeString();
        $highestRow = count($arrayArchivo);
        $tableData = [];
        //Definimos la variable error
        $error = '';
        
        for ($row = 2; $row <= $highestRow; $row++){
            $barra = isset($arrayArchivo[$row]['A'])? $arrayArchivo[$row]['A'] : null;
            $descriptor = isset($arrayArchivo[$row]['B'])? $arrayArchivo[$row]['B'] : null;
            $sku = isset($arrayArchivo[$row]['C'])? $arrayArchivo[$row]['C'] : null;
            $laboratorio = isset($arrayArchivo[$row]['D'])? $arrayArchivo[$row]['D'] : null;
            $clasificacionTerapeutica = isset($arrayArchivo[$row]['E'])? $arrayArchivo[$row]['E'] : null;
            // validar de que los campos no vengan con datos nulos
            $rowValido = self::_camposValidos($row, $barra, $descriptor, $sku, $laboratorio, $clasificacionTerapeutica);
            if( $rowValido!=null )
                $error = $error.$rowValido; // si se concatena un null con un string, el resultado es un string

            array_push($tableData,[
                'idArchivoMaestra' => $idArchivo,
                'barra' => $barra,
                'descriptor' => $descriptor,
                'sku' => $sku,
                'laboratorio' => $laboratorio,
                'clasificacionTerapeutica' => $clasificacionTerapeutica,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
        return (object)[
            'datos' => $tableData,
            'error' => $error
        ];
    }
    // Select a la tabla maestra_fcv para revisar SKU duplicados
    static function skuDuplicado(){
        $response = (object)[
            'error' => null,
            'datos' => null
        ];
        $duplicados = MaestraFCV::skuDuplicados();
        if($duplicados->count()>0){
            $response->datos = $duplicados;
            $response->error = "SKU duplicados";
        }
        return $response;
    }
    //Validar que los campos no contengas valores nulos
    private static function _camposValidos($row, $barra, $descriptor, $sku, $laboratorio, $clasificacionTerapeutica){
        // todo: validar el tipo, que no sean string vacios, que sean numeros, etc
        if($barra==null)
            return "En la fila $row, falta el campo 'codigoProducto'. ";
        if($descriptor==null)
            return "En la fila $row, falta el campo 'descriptor'. ";
        if($sku==null)
            return "En la fila $row, falta el campo 'codigo'. ";
        if($laboratorio==null)
            return "En la fila $row, falta el campo 'laboratorio'. ";
        if($clasificacionTerapeutica==null)
            return "En la fila $row, falta el campo 'clasificacionTerapeutica'. ";
        return null;
    }
}