<?php

// Carbon
use Carbon\Carbon;

class ArchivoFinalInventarioFCV{

    static function descomprimirZip($fullPath){
        $tmpPath = public_path()."/tmp/archivoFinalInventario/".md5(uniqid(rand(), true))."/";
        $archivoActa_v1 = 'archivo_salida_Acta.txt';
        $archivoActa_v2 = 'nuevo_formato.txt';

        $resultado = (object)[
            'error' => null,
            'acta_v1' => null,          // acta "original" (feb-2016)
            'acta_v2' => null,          // acta "nueva" (sept-2016)
        ];
        $zip = new ZipArchive;      // documentacion: http://php.net/manual/es/ziparchive.extractto.php
        if ($zip->open($fullPath) === true) {
            // si se logra extraer correctamente el/los archivos, adjuntar su path en la respuesta
            $extraccionCorrecta_acta_v1 = $zip->extractTo($tmpPath, $archivoActa_v1);
            if($extraccionCorrecta_acta_v1)
                $resultado->acta_v1 = $tmpPath.$archivoActa_v1;

            $extraccionCorrecta_acta_v2 = $zip->extractTo($tmpPath, $archivoActa_v2);
            if($extraccionCorrecta_acta_v2)
                $resultado->acta_v2 = $tmpPath.$archivoActa_v2;

            $zip->close();
        } else {
            $resultado->error = 'Error al abrir el archivo zip.';
        }
        return $resultado;
    }

    static function leerDatos($archivoActa){
        $datos = [];
        foreach (file($archivoActa) as $linea){
            $d = explode(';', $linea);
            $key = $d[0];
            // la expresion regular quita los "control chars", como tabs '\t', new lines '\n', y retorno de carro '\r'
            $value = preg_replace('~[[:cntrl:]]~', '', $d[1]);

            $datos[$key] = $value;
        }
        return $datos;
    }

    static function parsearActa($acta_v1, $acta_v2, $ceco_inventario){
        $resultado = (object)[
            'error' => null,
            'acta' => null
        ];
        if( $acta_v2!=null )
            $resultado->acta = \ArchivoFinalInventarioFCV::parsearActa_v2($acta_v2);
        else if( $acta_v1!=null )
            $resultado->acta = \ArchivoFinalInventarioFCV::parsearActa_v1($acta_v1);

        // revisar que se haya parseado y encontrado correctaemnte el acta
        if($resultado->acta==null) {
            $resultado->error = 'No se encontro un archivo de acta dentro del zip';
            return $resultado;
        }

        // verificar que el CECO local indicado en el acta, es el mismo que el CECO del local inventariado
        $ceco_acta = $resultado->acta['ceco_local'];
        if($ceco_acta!=$ceco_inventario){
            $resultado->error = "El local indicado en el acta, no corresponde con el inventario seleccionado (acta:$ceco_acta|inventario:$ceco_inventario";
            return $resultado;
        }

        return $resultado;
    }
    static function parsearActa_v1($archivo){
        // leer datos del archivo .txt
        $datos = self::leerDatos($archivo);

        function get(&$var, $default=null) {
            return isset($var) ? $var : $default;
        }
        /*
        // datos existentes en plataforma "inventario.seiconsultores.cl"
        $txt = (object)[
//            'dotacion_presupuestada'            => 0,
//            'dotacion_efectiva'                 => 0,
            'hora_llegada'                      => 0,
//            'administrador_local'               => 0,
            'porcentaje_cumplimiento_personal'  => 0,
//            'fecha_captura_inicio'              => 0,
            'fecha_emision_cero'                => 0,
            'fecha_emision_variance'            => 0,
            'fecha_inicio_sumary'               => 0,
//            'fecha_captura_fin'                 => 0,
            'unidades_inventariadas'            => 0,
            'unidades_teoricas'                 => 0,
//            'fecha_inventario'                  => 0,
//            'numero_local'                      => 0,
            'usuario'                           => '',
//            'nota1'                             => 0,
//            'nota2'                             => 0,
//            'nota3'                             => 0,
            'ptt_revisada'                      => 0,
            'item_revisado'                     => 0,
            'unidades_revisadas'                => 0,
            'ptt_corregidas'                    => 0,
            'item_corregido'                    => 0,
            'unidades_corregidas'               => 0,
            'diferencia_valor'                  => 0,
            'item_total_contados'               => 0,
            'clientes_id_clientes'              => '$idCliente',
        ];
        */
        return [
            'ceco_local'            => get($datos['cod_local'], '??'),
            // la fecha se recibe como '30/03/2016', pero para guardarlo como date en la BD debe estar como '2016-03-30'
            'fecha_inventario'      => Carbon::createFromFormat('d/m/Y', get($datos['fecha_toma'], '0000-00-00') )->toDateString(),
            'cliente'               => get($datos['nombre_empresa'], '??'),
            'rut'                   => get($datos['__'], '??'),
            'supervisor'            => get($datos['__'], '??'),
            'quimico_farmaceutico'  => get($datos['administrador_local'], '??'),
            'nota_presentacion'     => get($datos['nota1'], '0'),
            'nota_supervisor'       => get($datos['nota2'], '???'),
            'nota_conteo'           => get($datos['nota3'], '???'),
            //inicio y fin de conteo se reciben como string, tambien se deben transformar a datetime
            'inicio_conteo'         => isset($datos['captura_uno'])?
                Carbon::createFromFormat('d/m/Y H:i:s', $datos['captura_uno'])->toDateTimeString() : '00/00/00 00:00:00',
            'fin_conteo'            => isset($datos['fin_captura'])?
                Carbon::createFromFormat('d/m/Y H:i:s', $datos['fin_captura'])->toDateTimeString() : '00/00/00 00:00:00',
            'fin_revisión'          => get($datos['__'], '??'),
            'horas_trabajadas'      => get($datos['__'], '??'),
            'dotacion_presupuestada'    => get($datos['__'], '??'),
            'dotacion_efectivo'         => get($datos['__'], '??'),
            'unidades_inventariadas'    => get($datos['__'], '??'),
            'unidades_teoricas'         => get($datos['__'], '??'),
            'unidades_ajustadas'        => get($datos['__'], '??'),
            'ptt_total_inventariadas'   => get($datos['__'], '??'),
            'ptt_revisadas_totales'     => get($datos['__'], '??'),
            'ptt_revisadas_qf'          => get($datos['__'], '??'),
            'ptt_revisadas_apoyo_cv_1'  => get($datos['__'], '??'),
            'ptt_revisadas_apoyo_cv_2'  => get($datos['__'], '??'),
            'ptt_revisadas_supervisores_fcv'    => get($datos['__'], '??'),
            'item_total_inventariados' => get($datos['__'], '??'),
            'item_revisados'            => get($datos['__'], '??'),
            'item_revisados_qf'         => get($datos['__'], '??'),
            'item_revisados_apoyo_cv_1' => get($datos['__'], '??'),
            'item_revisados_apoyo_cv_2' => get($datos['__'], '??'),
            'unidades_corregidas_revision_previo_ajuste' => get($datos['__'], '??'),
            'unidades_corregidas'       => get($datos['__'], '??'),
            'total_item'                => get($datos['__'], '??'),
        ];
    }
    static function parsearActa_v2($archivo){
        // leer datos del archivo .txt
        $datos = self::leerDatos($archivo);

        // function "local"
        function get(&$var, $default=null) {
            return isset($var) ? $var : $default;
        }

        return [
            'ceco_local'            => get($datos['cod_local'], '??'),
            // la fecha se recibe como '30/03/2016', pero para guardarlo como date en la BD debe estar como '2016-03-30'
            'fecha_inventario'      => Carbon::createFromFormat('d/m/Y', get($datos['fecha_toma'], '00/00/0000') )->toDateString(),
            'cliente'               => get($datos['??'], '??'),
            'rut'                   => get($datos['__'], '??'),
            'supervisor'            => get($datos['__'], '??'),
            'quimico_farmaceutico'  => get($datos['__'], '??'),
            'nota_presentacion'     => get($datos['nota1'], '0'),
            'nota_supervisor'       => get($datos['nota2'], '???'),
            'nota_conteo'           => get($datos['nota3'], '???'),
            //inicio y fin de conteo se reciben como string, tambien se deben transformar a datetime
            'inicio_conteo'         => isset($datos['captura_uno'])?
                Carbon::createFromFormat('d/m/Y H:i:s', $datos['captura_uno'])->toDateTimeString() : '00/00/00 00:00:00',
            'fin_conteo'            => isset($datos['fin_captura'])?
                Carbon::createFromFormat('d/m/Y H:i:s', $datos['fin_captura'])->toDateTimeString() : '00/00/00 00:00:00',
            'fin_revisión'          => get($datos['__'], '??'),
            'horas_trabajadas'      => get($datos['__'], '??'),
            'dotacion_presupuestada'    => get($datos['__'], '??'),
            'dotacion_efectivo'         => get($datos['__'], '??'),
            'unidades_inventariadas'    => get($datos['__'], '??'),
            'unidades_teoricas'         => get($datos['__'], '??'),
            'unidades_ajustadas'        => get($datos['__'], '??'),
            'ptt_total_inventariadas'   => get($datos['__'], '??'),
            'ptt_revisadas_totales'     => get($datos['__'], '??'),
            'ptt_revisadas_qf'          => get($datos['__'], '??'),
            'ptt_revisadas_apoyo_cv_1'  => get($datos['__'], '??'),
            'ptt_revisadas_apoyo_cv_2'  => get($datos['__'], '??'),
            'ptt_revisadas_supervisores_fcv'    => get($datos['__'], '??'),
            'item_total_inventariados' => get($datos['__'], '??'),
            'item_revisados'            => get($datos['__'], '??'),
            'item_revisados_qf'         => get($datos['__'], '??'),
            'item_revisados_apoyo_cv_1' => get($datos['__'], '??'),
            'item_revisados_apoyo_cv_2' => get($datos['__'], '??'),
            'unidades_corregidas_revision_previo_ajuste' => get($datos['__'], '??'),
            'unidades_corregidas'       => get($datos['__'], '??'),
            'total_item'                => get($datos['__'], '??'),
        ];
    }
}