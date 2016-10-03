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
            ini_set('memory_limit','1024M');
            ini_set('max_execution_time', 540);
            // al indicar que tipo de archivo se espera, fuerzo a que no pueda abrir archivos de texto plano
//            $inputFileType = 'Excel2007';
            $inputFileType = PHPExcel_IOFactory::identify($fullPath);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($fullPath);
            $sheet = $objPHPExcel->getSheet(0);

            $response->datos = $sheet->toArray(null, true, true, true);
        } catch(Exception $e) {
            $msg = $e->getMessage();
            $response->error = "Error critico al leer los datos: $msg";
        }
        return $response;
    }


    public static function generarWorkbook($cabeceras, $datos){
        $headerStyles = ['font'  => [
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'name'  => 'Verdana'
        ]];
        //$bodyStyles = ['font' => [
        //    'name'=>'Verdana'
        //]];
        $workbook = new PHPExcel();
        $sheet = $workbook->getActiveSheet();
        $sheet->fromArray($cabeceras, NULL, 'A1');
        $sheet->fromArray($datos,  NULL, 'A2');

        // aplicar estilos
        $ultimaColumna = $sheet->getHighestDataColumn();
        $ultimaFila = $sheet->getHighestDataRow();
        $sheet->getStyle("A1:".$ultimaColumna."1")->applyFromArray($headerStyles);
        //$sheet->getStyle("A2:".$ultimaColumna.$ultimaFila)->applyFromArray($bodyStyles);

        // columnas con tamaño ajustable
        //PHPExcel_Shared_Font::setTrueTypeFontPath('/usr/share/fonts/truetype/msttcorefonts/');
        //PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach (range('A', $ultimaColumna) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $workbook;
    }
    public static function workbook_a_archivo($workbook){
        $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
        $random_number= md5(uniqid(rand(), true));

        $fullpath = public_path()."/tmp/$random_number.xlxs";
        $excelWritter->save($fullpath);
        return $fullpath;
    }
}