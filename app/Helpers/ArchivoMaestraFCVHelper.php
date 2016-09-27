<?php
// Carbon
use Carbon\Carbon;
use DB;
// Modelos
use App\ArchivoMaestraFCV;
use App\MaestraFCV;

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
        //Obtener el nombre del archivo que trae el path
        $nombreArchivo = $path['nombre_archivo'];
        //Obtener el modelo asociado con ese nombre en la BD
        $archivo = ArchivoMaestraFCV::where('nombreArchivo', '=', $nombreArchivo)->first();
        //Obtener el idArchivo mediante el archivo extraido
        $idArchivo = $archivo->idArchivoMaestra;
        ini_set('memory_limit','1024M');
        ini_set('max_execution_time', 540);
        $datos = self::leerArchivoMaestra($path['fullPath']);
        dd($datos);
        DB::transaction(function() use ($datos, $idArchivo){
            foreach ($datos as $dato){
                $maestra = new MaestraFCV([
                    'idArchivoMaestra'=>$idArchivo,
                    'codigoProducto'=>isset($dato['a'])? $dato['a'] : '00000',
                    'descriptor'=>isset($dato['b'])? $dato['b'] : '-----',
                    'codigo'=> isset($dato['c'])? $dato['c'] : '00000',
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
        //dd($sheet);
        $array = $sheet->toArray(null,true,true,true);

        $highestRow = count($array);
        //dd($highestRow);
        //  Loop through each row of the worksheet in turn
        $tableData = [];
        // inicia en la row 2 para no leer el titulo
        for ($row = 2; $row <= $highestRow; $row++){
            array_push($tableData, [
                'a'=> $array[$row]['A'],
                'b'=> $array[$row]['B'],
                'c'=> $array[$row]['C'],
                'd'=> $array[$row]['D'],
                'e'=> $array[$row]['E']
            ]);
        }
        return $tableData;
    }
}