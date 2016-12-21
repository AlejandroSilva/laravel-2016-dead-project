<?php
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use Carbon\Carbon;

class ExcelHelper{

    static function leerExcel($fullPath){
        $response = (object)[
            'error' => null,
            'datos' => null
        ];
        try {
            ini_set('memory_limit','2048M');
            ini_set('max_execution_time', 540);
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize' => '512MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);

            // al indicar que tipo de archivo se espera, fuerzo a que no pueda abrir archivos de texto plano
            //$inputFileType = 'Excel2007';
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
            '', '', '', '', '', 'Conteo', '', '',   // 8 celdas
            'Duración', '','',                      // 3 celdas
            'Dotaciones', '',                       // 2 celdas
            'Unidades', '', '', '',                 // 4 celdas
            'Evaluaciones', '', '',                 // 3 celdas
            'Tot. Aud. FCV', '', '',                // 3 celdas
            'Auditoría QF', '', '',                 // 3 celdas
            'Aud. Apoyo 1', '', '',                 // 3 celdas
            'Aud. Apoyo 2', '', '',                 // 3 celdas
            'Aud. Sup. FCV', '', '',                // 3 celdas
            'Correcciones Auditoría FCV a SEI', '', '', '',    // 4 celdas
            '% Error Aud.', '',                     // 2 celdas
            'Variación Grilla',                     // 1 celdas
            'Totales Inventario',                   // 3 celdas
        ], [
            // Hitos, 8
            'Fecha Inv', 'CL', 'Local', 'Supervisor', 'Químico Farmacéutico', 'Inicio', 'Fin', 'Fin Proceso',
            // Duracion, 3
            'Conteo', 'Revisión', 'IG',
            // Dotaciones, 2
            'Ppto.', 'Efec.',
            // Unidades, 4
            'Conteo', 'Mapro', 'Dif.Neto', 'Dif.ABS',
            // Evaluaciones, 3
            'Pres.', 'Sup.', 'Cont.',
            // Consolidado Auditoria FCV, 3
            'PTT', 'Unids', 'Ítems',
            // auditoria qf, 3
            'PTT', 'Unids', 'Ítems',
            // auditoria apoyo 1, 3
            'PTT', 'Unids', 'Ítems',
            // auditoria apoyo 2, 3
            'PTT', 'Unids', 'Ítems',
            // auditoria sup, 3
            'PTT', 'Unids', 'Ítems',
            // Correcciones Auditoria, 4
            'Patentes', 'Ítems', 'Un. Neto', 'Un. ABS',
            // perc.error aud, 2
            'SEI', 'QF',
            // Variación grilla, 1
            '%',
            // Totales inventario, 3
            'PTT',
            'Items',
            'SKU',
        ]];

        // SHEET 1: DATOS
        $workbook = new PHPExcel();
        $sheet = $workbook->getActiveSheet();
        $sheet->fromArray($cabeceras, NULL, 'A1');
        $sheet->fromArray($datos,  NULL, 'A3');

        // unir celdas de la cabecera
        $sheet->mergeCells("F1:G1");    // hitos, 8,        F-G
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
        $sheet->mergeCells("AP1:AP1");  // var grilla, 1    AP-AP
        $sheet->mergeCells("AQ1:AS1");  // totales inv., 3  AQ-AS

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
        $sheet->getStyle("AP1:AP$MAX_ROW")->applyFromArray($estiloBordeDerecha );
        $sheet->getStyle("AQ1:AS$MAX_ROW")->applyFromArray($estiloBordeDerecha );

        // columnas con tamaño ajustable
        //PHPExcel_Shared_Font::setTrueTypeFontPath('/usr/share/fonts/truetype/msttcorefonts/');
        //PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $MAX_COL_INDEX = PHPExcel_Cell::columnIndexFromString($MAX_COL);
        for($index=0 ; $index <= $MAX_COL_INDEX ; $index++){
            $col = PHPExcel_Cell::stringFromColumnIndex($index);
            $sheet->getColumnDimension($col)->setAutoSize(TRUE);
        }

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
            ['Evaluaciones', 'Pres.', 'descripción pendiente'],
            ['Evaluaciones', 'Sup.', 'descripción pendiente'],
            ['Evaluaciones', 'Cont.', 'descripción pendiente'],
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
            ['Total Inventario', 'Patentes Inventariadas', 'descripción pendiente'],
            ['Total Inventario', 'Items Totales', 'descripción pendiente'],
            ['Total Inventario', 'SKU unicos', 'descripción pendiente'],
        ], null, 'A1');

        // Estilos
        $MAX_COL_2 = $sheetDefiniciones->getHighestDataColumn();
        //$MAX_ROW_2 = $sheetDefiniciones->getHighestDataRow();
        // las cabeceras en negrita
        $sheetDefiniciones->getStyle("A1:".$MAX_COL_2."1")->applyFromArray([
            'font' => ['bold'=>true ]
        ]);
        // columnas de tamaño ajustable
        $MAX_COL_INDEX_2 = PHPExcel_Cell::columnIndexFromString($MAX_COL_2);
        for($index=0 ; $index <= $MAX_COL_INDEX_2 ; $index++){
            $col = PHPExcel_Cell::stringFromColumnIndex($index);
            $sheetDefiniciones->getColumnDimension($col)->setAutoSize(TRUE);
        }

        $sheet->setTitle('Inventarios');
        $sheetDefiniciones->setTitle('Definiciones');
        $workbook->setActiveSheetIndex(0);
        return $workbook;
    }


    public static function generarXLSX_capturasRespuestaWOM($capturas){
        $cabecera = [
            'Nro. Documento' => 'string',
            'Nro. Linea' => 'string',
            'Fecha Despacho' => 'string',
            'Código Material' => 'string',
            'Cantidad' => 'string',
            'Número de Serie' => 'string',
            'Org. Origen' => 'string',
            'Comentario' => 'string',
            'Estado' => 'string',
        ];
        $datos = [];
        foreach ($capturas as $cap)
            $datos[] = [
                $cap->ptt,
                $cap->correlativo,
                Carbon::parse($cap->fechaCaptura)->format('Ymd'),
                //1,//
                $cap->sku,
                $cap->conteoInicial,
                $cap->serie,
                $cap->codigoOrganizacion,
                $cap->nombreOrganizacion,
                $cap->estado,
            ];

        $random_number= md5(uniqid(rand(), true));
        $fullpath = public_path()."/tmp/respuestaWOM_$random_number.xlsx";

        $writer = new XLSXWriter();
        $writer->writeSheet($datos,'RespuestaWOM', $cabecera);
        $writer->writeToFile($fullpath);
        return $fullpath;
    }

    public static function generarXLSX_archivosWOM($archivos){
        $cabecera = [
            'Organización' => 'string',
            'Fecha' => 'string',
            'Líder WOM' => 'string',
            'RUN Líder WOM' => 'string',
            'Líder Sei' => 'string',
            'RUN Líder Sei' => 'string',
            'Unid. Contadas' => 'string',
            'Unid. Nuevo' => 'string',
            'Unid. Usado' => 'string',
            'Unid. Préstamo' => 'string',
            'Patentes' => 'string',
            'Tiempo' => 'string',
            'Evaluación' => 'string',
            'Cumplimiento WOM' => 'string',
            'Cumplimiento SEI' => 'string'
        ];
        $datos = [];
        foreach ($archivos as $a)
            $datos[] = [
                $a->organizacion,
                $a->fecha,
                $a->liderWOM,
                $a->runLiderWOM,
                $a->liderSei,
                $a->runLiderSei,
                $a->unidadesContadas,
                $a->unidadesNuevo,
                $a->unidadesUsado,
                $a->unidadesPrestamo,
                $a->pttTotal,
                $a->tiempoTranscurrido,
                $a->evaluacionAServicioSEI,
                "$a->pCunplimientoWOM%",
                "$a->pCunplimientoSEI%"
            ];

        $random_number= md5(uniqid(rand(), true));
        $fullpath = public_path()."/tmp/archivosWOM$random_number.xlsx";

        $writer = new XLSXWriter();
        $writer->writeSheet($datos,'wom', $cabecera);
        $writer->writeToFile($fullpath);
        return $fullpath;
    }

    
    public static function workbook_a_archivo($workbook){
        $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
        $random_number= md5(uniqid(rand(), true));

        $fullpath = public_path()."/tmp/$random_number.xlsx";
        $excelWritter->save($fullpath);
        return $fullpath;
    }
}