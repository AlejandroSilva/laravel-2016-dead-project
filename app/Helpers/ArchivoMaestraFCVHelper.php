<?php
// Carbon
use Carbon\Carbon;

class ArchivoMaestraFCVHelper{
    static function moverAcarpeta($archivo){
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $nombreOriginal = $archivo->getClientOriginalName();
        $fileName = "[$timestamp]$nombreOriginal";
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
        
        for ($row = 2; $row <= $highestRow; $row++){
            $codigoProducto = isset($arrayArchivo[$row]['A'])? $arrayArchivo[$row]['A'] : '0000';
            $descriptor = isset($arrayArchivo[$row]['B'])? $arrayArchivo[$row]['B'] : 'sin descriptor';
            $codigo = isset($arrayArchivo[$row]['C'])? $arrayArchivo[$row]['C'] : '0000';
            $laboratorio = isset($arrayArchivo[$row]['D'])? $arrayArchivo[$row]['D'] : 'sin lab';
            $clasificacionTerapeutica = isset($arrayArchivo[$row]['E'])? $arrayArchivo[$row]['E'] : 'sin clas';

            array_push($tableData,[
                'idArchivoMaestra' => $idArchivo,
                'codigoProducto' => $codigoProducto,
                'descriptor' => $descriptor,
                'codigo' => $codigo,
                'laboratorio' => $laboratorio,
                'clasificacionTerapeutica' => $clasificacionTerapeutica,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
        return (object)[
            'datos' => $tableData
        ];
    }
    static function leerArchivoMaestra($inputFileName) {
        $response = (object)[
            'datos' => null
        ];
        ini_set('memory_limit','1024M');
        ini_set('max_execution_time', 540);
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);
        /**  Advise the Reader of which WorkSheets we want to load  **/
        $objReader->setLoadSheetsOnly("Hoja1");
        $objPHPExcel = $objReader->load($inputFileName);
        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $response->datos = $sheet->toArray(null,true,true,true);
        
        return $response;
    }
}