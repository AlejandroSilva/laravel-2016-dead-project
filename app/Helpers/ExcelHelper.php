<?php

// PHPExcel
//use PHPExcel;
//use PHPExcel_IOFactory;
//use PHPExcel_Shared_Date;

class ExcelHelper{
    static function leerExcel($fullPath){
        $response = (object)[
            'error' => null,
            'datos' => null
        ];
        try {
            // al indicar que tipo de archivo se espera, fuerzo a que no pueda abrir archivos de texto plano
//            $inputFileType = 'Excel2007';
            $inputFileType = PHPExcel_IOFactory::identify($fullPath);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($fullPath);
            $sheet = $objPHPExcel->getSheet(0);

            $response->datos = $sheet->toArray(null, true, true, true);
        } catch(Exception $e) {
//            $fileName = pathinfo($fullPath, PATHINFO_BASENAME);
            $msg = $e->getMessage();
            $response->error = "Error critico al leer los datos: $msg";
        }
        return $response;
    }
}