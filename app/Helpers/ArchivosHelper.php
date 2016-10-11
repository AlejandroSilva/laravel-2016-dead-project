<?php

// Carbon
use Carbon\Carbon;
//Modelos
use App\ArchivoMuestraVencimientoFCV;

class ArchivosHelper{

    // ###################################### ARCHIVO FINAL INVENTARIO ######################################
    static function extraerActaDelZip($zip_fullpath){
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

        $tmpPath = public_path()."/tmp/archivoFinalInventario/".md5(uniqid(rand(), true))."/";
        $archivoActa_v1 = 'archivo_salida_Acta.txt';

        // tarta de abrir el archivo zip
        $zip = new ZipArchive;      // documentacion: http://php.net/manual/es/ziparchive.extractto.php
        $resultado = $zip->open($zip_fullpath);
        if( $resultado !== true ){
            $msg = isset($ZIP_ERROR[$resultado])? $ZIP_ERROR[$resultado] : 'Error desconocido.';
            return (object)['error'=>"Error al extrare el archivo: '$msg'"];
        }

        // extraer el zip dentro del acta
        $extraccionCorrecta_acta = $zip->extractTo($tmpPath, $archivoActa_v1);
        $zip->close();
        if($extraccionCorrecta_acta==false)
            return (object)['error'=>'Archivo de acta no encontrado dentro del zip.'];

        // leer los datos del txt a un array
        $actatxt_fullpath = $tmpPath.$archivoActa_v1;
        return (object)['actatxt_fullpath' => $actatxt_fullpath];
    }

    // #################################### MUESTRA VENCIMIENTO AUDITORIA FCV ###################################

    // utilizado por: MuestraVencimientoController::api_upload
    static function moverMuestraVencimientoFCV($archivo){
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $extra = "[$timestamp][FCV] ";
        $carpetaDestino = ArchivoMuestraVencimientoFCV::getPathCarpetaArchivos();

        return self::_moverACarpeta($archivo, $extra, $carpetaDestino);
    }

    // ################################################# OTROS ##################################################

    static function descargarArchivo($fullPath, $nombreArchivo){
        // existe el archivo fisicamente en el servidor?
        if(!File::exists($fullPath))
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Archivo no encontrado',
                'descripcion' => 'El archivo que busca no ha sido encontrado. Contactese con el departamento de informática.',
            ]);

        return response()
            ->download($fullPath, $nombreArchivo, [
                'Content-Type'=>'application/force-download',   // forzar la descarga en Opera Mini
                'Pragma'=>'no-cache',
                'Cache-Control'=>'no-cache, must-revalidate'
            ]);
    }

    static function _moverACarpeta($archivo, $extra, $carpetaDestino){
        $nombreOriginal = $archivo->getClientOriginalName();
        $nombreFinal = "$extra $nombreOriginal";

        // guardar el archivo en una carpeta publica, y cambiar los permisos para que el grupo pueda modifiarlos
        $archivo->move( $carpetaDestino, $nombreFinal);
        chmod($carpetaDestino.$nombreFinal, 0774);   // 0744 por defecto

        return (object)[
            'fullpath' => $carpetaDestino.$nombreFinal,
            'nombre_archivo' => $nombreFinal,
            'nombre_original' => $nombreOriginal,
        ];
    }
}