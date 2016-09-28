<?php

// Carbon
use Carbon\Carbon;
//Modelos
use App\ArchivoMuestraVencimientoFCV;

class ArchivosHelper{

    // utilizado por: MuestraVencimientoController::api_upload
    static function moverMuestraVencimientoFCV($archivo){
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $extra = "[$timestamp][FCV] ";
        $carpetaDestino = ArchivoMuestraVencimientoFCV::getPathCarpetaArchivos();

        return self::_moverACarpeta($archivo, $extra, $carpetaDestino);
    }

    // utilizado por: ArchivoFinalInventarioController::api_uploadZIP
    static function moverArchivoFinalInventario($archivo, $nombreCliente, $ceco, $fechaProgramada){
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $extra = "[$timestamp][$nombreCliente][$ceco][$fechaProgramada]";
        $carpetaDestino = public_path()."/$nombreCliente/archivoFinalInventario/";

        return self::_moverACarpeta($archivo, $extra, $carpetaDestino);
    }

    static function _moverACarpeta($archivo, $extra, $carpetaDestino){
        $nombreOriginal = $archivo->getClientOriginalName();
        $nombreFinal = "$extra $nombreOriginal";

        // guardar el archivo en una carpeta publica, y cambiar los permisos para que el grupo pueda modifiarlos
        $archivo->move( $carpetaDestino, $nombreFinal);
        chmod($carpetaDestino.$nombreFinal, 0774);   // 0744 por defecto

        return (object)[
            'fullPath' => $carpetaDestino.$nombreFinal,
            'nombre_archivo' => $nombreFinal,
            'nombre_original' => $nombreOriginal,
        ];
    }
}