<?php

// Carbon
use Carbon\Carbon;
//Modelos
use App\ArchivoMuestraVencimientoFCV;

class ArchivosHelper{

//     no funciona esta wea, falla por los nombres de archivos...
//    static function XLSXaCSV($xlsxPath){
//        //$txtPath = public_path()."/tmp/".md5(uniqid(rand(), true))."/".rand()."txt";
//        $txtPath = "/home/asilva/Escritorio/asd.txt";
//        $REPLACE = 's/|\"/|/g;s/\"|/|/g;s/   \"//g';
//        $com = "ssconvert -O 'separator=|' \"$xlsxPath\" $txtPath";
//        exec($com);
//        exec("sed -i -- '$REPLACE' $txtPath", $aa);
//    }

    static function extraerArchivo($zip_fullpath, $archivo){
        $ZIP_ERROR = [
            ZIPARCHIVE::ER_EXISTS => 'El fichero ya existe.',
            ZIPARCHIVE::ER_INCONS => 'Archivo zip inconsistente.',
            ZIPARCHIVE::ER_INVAL => 'Argumento no válido.',
            ZIPARCHIVE::ER_MEMORY => 'Falló malloc.',
            ZIPARCHIVE::ER_NOENT => 'No existe el fichero.',
            ZIPARCHIVE::ER_NOZIP => 'No es un archivo zip.',
            ZIPARCHIVE::ER_OPEN => 'No se puede abrir el fichero.',
            ZIPARCHIVE::ER_READ => 'Error de lectura.',
            ZIPARCHIVE::ER_SEEK => 'Error de búsqueda.',
        ];

        $tmpPath = public_path()."/tmp/".md5(uniqid(rand(), true))."/";

        // tratar de abrir el archivo zip
        $zip = new ZipArchive;      // documentacion: http://php.net/manual/es/ziparchive.extractto.php
        $resultado = $zip->open($zip_fullpath);
        if( $resultado !== true ){
            $msg = isset($ZIP_ERROR[$resultado])? $ZIP_ERROR[$resultado] : 'Error desconocido.';
            return (object)['error'=>"Error al extrare el archivo: '$msg'"];
        }

        // extraer el zip dentro del acta
        $extraccionCorrecta_acta = $zip->extractTo($tmpPath, $archivo);
        $zip->close();
        if($extraccionCorrecta_acta==false)
            return (object)['error'=>'Archivo no encontrado dentro del zip.'];

        // leer los datos del txt a un array
        return (object)['fullpath' => $tmpPath.$archivo];
    }

    // ################################################# OTROS ##################################################

    static function descargarArchivo($fullPath, $nombreArchivo){
        // existe el archivo fisicamente en el servidor?
        if(!File::exists($fullPath))
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Archivo no encontrado',
                'descripcion' => 'El archivo que busca no ha sido encontrado. Contactese con el departamento de informática.',
            ]);

        // hot fix: problema esporadico con el buffer
        ob_end_clean();

        return response()
            ->download($fullPath, $nombreArchivo, [
                'Content-Type'=>'application/force-download',   // forzar la descarga en Opera Mini
                'Pragma'=>'no-cache',
                'Cache-Control'=>'no-cache, must-revalidate'
            ]);
    }

    static function moverACarpeta($archivo, $extra, $carpetaDestino){
        $nombreOriginal = $archivo->getClientOriginalName();
        $nombreFinal = "$extra $nombreOriginal";

        // guardar el archivo en una carpeta publica, y cambiar los permisos para que el grupo pueda modifiarlos
        $archivo->move($carpetaDestino, $nombreFinal);
        chmod($carpetaDestino.$nombreFinal, 0774);   // 0744 por defecto

        return (object)[
            'fullpath' => $carpetaDestino.$nombreFinal,
            'nombre_archivo' => $nombreFinal,
            'nombre_original' => $nombreOriginal,
        ];
    }
}