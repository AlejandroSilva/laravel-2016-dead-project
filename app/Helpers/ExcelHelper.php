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
            $msg = $e->getMessage();
            $response->error = "Error critico al leer los datos: $msg";
        }
        return $response;
    }


    public static function generarWorkbook_consolidadoActas($datos){
        $cabeceras = [[
            'Hitos importantes del proceso de inventario', '', '', '', '', '', '', '',  // 8 celdas
            'Duración', '','',                      // 3 celdas
            'Dotaciones', '',                       // 2 celdas
            'Unidades', '', '', '',                 // 4 celdas
            'Evaluaciones', '', '',                 // 3 celdas
            'Consolidado Auditoría FCV', '', '',    // 3 celdas
            'Auditoría QF', '', '',                 // 3 celdas
            'Auditoría Apoyo 1', '', '',            // 3 celdas
            'Auditoría Apoyo 2', '', '',            // 3 celdas
            'Auditoría Sup. FCV', '', '',           // 3 celdas
            'Correcciones Auditoría FCV a SEI', '', '', '',    // 4 celdas
            '% Error Aud.', '',                     // 2 celdas
            'Variación Grilla', '',                 // 2 celdas
        ], [
            // Hitos, 8
            'Fecha Inv', 'CL', 'Local', 'Supervisor', 'Químico Farmacéutico', 'Inicio Conteo', 'Fin Conteo', 'Fin Proceso',
            // Duracion, 3
            'Conteo', 'Revisión', 'Total Proceso',
            // Dotaciones, 2
            'Presup.', 'Efectivo',
            // Unidades, 4
            'Conteo', 'Teórico', 'Dif. Neto', 'Dif. ABS',
            // Evaluaciones, 3
            'Nota Presentación', 'Nota Supervisor', 'Nota Conteo',
            // Consolidado Auditoria FCV, 3
            'Patente', 'Unidades', 'Ítems',
            // auditoria qf, 3
            'Patente', 'Unidades', 'Ítems',
            // auditoria apoyo 1, 3
            'Patente', 'Unidades', 'Ítems',
            // auditoria apoyo 2, 3
            'Patente', 'Unidades', 'Ítems',
            // auditoria sup, 3
            'Patente', 'Unidades', 'Ítems',
            // Correcciones Auditoria, 4
            'Patentes', 'Ítems', 'Un. Neto', 'Un. ABS',
            // perc.error aud, 2
            'SEI', 'QF',
            // Variación grilla, 2
            '%', 'SKU Inv.'
        ]];

        // SHEET 1: DATOS
        $workbook = new PHPExcel();
        $sheet = $workbook->getActiveSheet();
        $sheet->fromArray($cabeceras, NULL, 'A1');
        $sheet->fromArray($datos,  NULL, 'A3');

        // unir celdas de la cabecera
        $sheet->mergeCells("A1:H1");    // hitos, 8,        A-H
        $sheet->mergeCells("I1:K1");    // duracion, 3      I-K
        $sheet->mergeCells("L1:M1");    // dotaciones, 2    L-M
        $sheet->mergeCells("N1:Q1");    // unidades, 4      N-Q
        $sheet->mergeCells("R1:T1");    // evaluaciones, 3  R-T
        $sheet->mergeCells("U1:W1");    // consolidado, 3   U-W
        $sheet->mergeCells("X1:Z1");    // aud. qf , 3      X-Z
        $sheet->mergeCells("AA1:AC1");  // aud. apoyo 1, 3  AA-AC
        $sheet->mergeCells("AD1:AF1");  // aud. apoyo 2, 3  AD-AF
        $sheet->mergeCells("AG1:AI1");  // aud. sup, 3      AG-AI
        $sheet->mergeCells("AJ1:AM1");  // Correcc.aud, 4   AJ-AM
        $sheet->mergeCells("AN1:AO1");  // %erroraud, 2     AN-AO
        $sheet->mergeCells("AP1:AQ1");  // var grilla, 2    AP-AQ

        // Aplicar estilos
        $MAX_COL = $sheet->getHighestDataColumn();
        $MAX_ROW = $sheet->getHighestDataRow();
        // primer row: borde abajo y texto centrado
        $sheet->getStyle("A1:".$MAX_COL."1")->applyFromArray([
            'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ],
            'font' => ['bold'=>true]
        ]);
        // segundo row: borde abajo
        $sheet->getStyle("A2:".$MAX_COL."2")->applyFromArray([
            'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
            'font' => ['bold'=>true]
        ]);
        // por cada seccion, se agrega un separado de columnas
        $estiloBordeDerecha  = ['borders' => ['right' =>  ['style' => PHPExcel_Style_Border::BORDER_THIN]]];
        $sheet->getStyle("H1:H$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("K1:K$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("M1:M$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("Q1:Q$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("T1:T$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("W1:W$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("Z1:Z$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("AC1:AC$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("AF1:AF$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("AI1:AI$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("AM1:AM$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("AO1:AO$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("AQ1:AQ$MAX_ROW")->applyFromArray($estiloBordeDerecha );

        // columnas con tamaño ajustable
        //PHPExcel_Shared_Font::setTrueTypeFontPath('/usr/share/fonts/truetype/msttcorefonts/');
        //PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        for ($col = 'A'; $col <= $MAX_COL; $col++)
            $sheet->getColumnDimension($col)->setAutoSize(TRUE);

        // SHEET 2: DEFINICIONES
        $sheetDefiniciones = $workbook->createSheet();
        $sheetDefiniciones->fromArray([
            ['Sección', 'Campo', 'Descripción'],
            ['Hitos importantes', 'detalle', 'descripción pendiente'],
            ['Hitos importantes', 'Fecha Inv', 'descripción pendiente'],
            ['Hitos importantes', 'CL', 'descripción pendiente'],
            ['Hitos importantes', 'Local', 'descripción pendiente'],
            ['Hitos importantes', 'Supervisor', 'descripción pendiente'],
            ['Hitos importantes', 'Químico Farmacéutico', 'descripción pendiente'],
            ['Hitos importantes', 'Inicio Conteo', 'descripción pendiente'],
            ['Hitos importantes', 'Fin Conteo', 'descripción pendiente'],
            ['Hitos importantes', 'Fin Proceso', 'descripción pendiente'],
            ['Duración', 'Conteo', 'descripción pendiente'],
            ['Duración', 'Revisión', 'descripción pendiente'],
            ['Duración', 'Total Proceso', 'descripción pendiente'],
            ['Dotaciones', 'Presup.', 'descripción pendiente'],
            ['Dotaciones', 'Efectivo', 'descripción pendiente'],
            ['Unidades', 'Conteo', 'descripción pendiente'],
            ['Unidades', 'Teórico', 'descripción pendiente'],
            ['Unidades', 'Dif. Neto', 'descripción pendiente'],
            ['Unidades', 'Dif. ABS', 'descripción pendiente'],
            ['Evaluaciones', 'Nota Presentación', 'descripción pendiente'],
            ['Evaluaciones', 'Nota Supervisor', 'descripción pendiente'],
            ['Evaluaciones', 'Nota Conteo', 'descripción pendiente'],
            ['Consolidado Auditoría FCV', 'Patente', 'descripción pendiente'],
            ['Consolidado Auditoría FCV', 'Unidades', 'descripción pendiente'],
            ['Consolidado Auditoría FCV', 'Ítems', 'descripción pendiente'],
            ['Auditoría QF', 'Patente', 'descripción pendiente'],
            ['Auditoría QF', 'Unidades', 'descripción pendiente'],
            ['Auditoría QF', 'Ítems', 'descripción pendiente'],
            ['Auditoría Apoyo 1', 'Patente', 'descripción pendiente'],
            ['Auditoría Apoyo 1', 'Unidades', 'descripción pendiente'],
            ['Auditoría Apoyo 1', 'Ítems', 'descripción pendiente'],
            ['Auditoría Apoyo 2', 'Patente', 'descripción pendiente'],
            ['Auditoría Apoyo 2', 'Unidades', 'descripción pendiente'],
            ['Auditoría Apoyo 2', 'Ítems', 'descripción pendiente'],
            ['Auditoría Sup. FCV', 'Patente', 'descripción pendiente'],
            ['Auditoría Sup. FCV', 'Unidades', 'descripción pendiente'],
            ['Auditoría Sup. FCV', 'Ítems', 'descripción pendiente'],
            ['Correcciones Auditoría FCV a SEI', 'Patente', 'descripción pendiente'],
            ['Correcciones Auditoría FCV a SEI', 'Ítems', 'descripción pendiente'],
            ['Correcciones Auditoría FCV a SEI', 'Un. Neto', 'descripción pendiente'],
            ['Correcciones Auditoría FCV a SEI', 'Un. ABS', 'descripción pendiente'],
            ['% Error Aud.', 'SEI', 'descripción pendiente'],
            ['% Error Aud.', 'QF', 'descripción pendiente'],
            ['Variación Grilla', '%', 'descripción pendiente'],
            ['Variación Grilla', 'SKU Inv.', 'descripción pendiente'],
        ], null, 'A1');

        // Estilos
        $MAX_COL_2 = $sheetDefiniciones->getHighestDataColumn();
        //$MAX_ROW_2 = $sheetDefiniciones->getHighestDataRow();
        // las cabeceras en negrita
        $sheetDefiniciones->getStyle("A1:".$MAX_COL_2."1")->applyFromArray([
            'font' => ['bold'=>true ]
        ]);
        // columnas de tamaño ajustable
        for ($col = 'A'; $col <= $MAX_COL_2; $col++)
            $sheetDefiniciones->getColumnDimension($col)->setAutoSize(TRUE);

        $sheet->setTitle('Inventarios');
        $sheetDefiniciones->setTitle('Definiciones');
        $workbook->setActiveSheetIndex(0);
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