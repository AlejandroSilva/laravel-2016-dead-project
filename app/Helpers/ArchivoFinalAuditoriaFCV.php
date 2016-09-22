<?php

class ArchivoFinalAuditoriaFCV{

    static function buscarArchivo($idArchivo){
        $FCV_AUDITORIAS_ZIP = "/home/asilva/Escritorio/";

        $resultado = (object)[
            'error' => null,
            'fullPath' => null,
        ];
        $data = DB::table('SEI_INVENTARIO.archivos_cruzverde')
            ->select('*')
            ->where('id_arch_cruz', $idArchivo)
            ->first();

        // se encontro el registro en la BD?
        if(!$data){
            $resultado->error = "Auditoria no encontrada";
            return $resultado;
        }

        // existe el archivo?
        $fullPath = $FCV_AUDITORIAS_ZIP.$data->nombre_archivo_nuevo;
        if( !file_exists($fullPath) ){
            $resultado->error = "Archivo '$data->nombre_archivo_nuevo' no encontrado en el servidor";
            return $resultado;
        }

        $resultado->fullPath = $fullPath;
        return $resultado;
    }

    static function descomprimirZip($fullPath){
        $tmpPath = public_path()."/tmp/archivoFinalAuditoria/".md5(uniqid(rand(), true))."/";

        $resultado = (object)[
            'error' => null,
            'datos_ird' => null,
            'datos_vencimiento' => null,
            'firma_ird' => null,
            'firma_vencimiento' => null,
        ];

        $zip = new ZipArchive;      // documentacion: http://php.net/manual/es/ziparchive.extractto.php
        if ($zip->open($fullPath) === true) {

            // SEGUIR ACA
            // $extraccionCorrecta_acta_v1 = $zip->extractTo($tmpPath, $archivoActa_v1);

            $zip->close();
        }else{
            $resultado->error = "Error al abrir el archivo zip";
        }
    }

}