<?php
// Carbon
use Carbon\Carbon;
use DB;
use PHPExcel_IOFactory;



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
            'fullPath' => $path.$fileName,
            'nombre_archivo' => $fileName,
            'nombre_original' => $nombreOriginal,
        ];
    }
    static function guardarRegistro($path){
        ini_set('memory_limit','1024M');
        ini_set('max_execution_time', 540);
        $datos = self::leerArchivoMaestra($path['fullPath']);
        DB::transaction(function() use ($datos){
            foreach ($datos as $dato){
                //$codigo = $dato['a'];
                $maestra = new \App\MaestraFCV([
                    'idArchivoMaestra'=>1,
                    'codigoProducto'=>isset($dato['a'])? $dato['a'] : '1',
                    'descriptor'=>isset($dato['b'])? $dato['b'] : '1',
                    'codigo'=> isset($dato['c'])? $dato['c'] : '1',
                    'laboratorio'=>isset($dato['d'])? $dato['d'] : '----',
                    'clasificacionTerapeutica'=>isset($dato['e'])? $dato['e'] : '----'
                ]);
                $maestra->save();
            }
        });
    }
    static function leerArchivoMaestra($inputFileName) {
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
            ]);
        }
        return $tableData;
    }
    
}