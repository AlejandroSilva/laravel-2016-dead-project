<?php
use Carbon\Carbon;

class ActaInventarioHelper{
    static function parsearZIPaActa($archivozip_fullpath, $local_numero){
        // paso 1) Extraer el archivo txt de acta del zip
        $resultadoExtraccion = \ArchivosHelper::extraerActaDelZip($archivozip_fullpath);
        if(isset($resultadoExtraccion->error))
            return (object)['error' => $resultadoExtraccion->error];

        // paso 2) tratar de parsear el txt que fue extraido
        $actatxt_fullpath = $resultadoExtraccion->actatxt_fullpath;
        return self::parsearTXTaActa($actatxt_fullpath, $local_numero);
    }

    static function parsearTXTaActa($actatxt_fullpath, $local_numero){
        $array = self::txt_a_array($actatxt_fullpath);
        $acta = self::__array_a_actaFCV($array);

        //validar: el local indicado en el acta es el mismo que el del inventario que estamos revisando?
        $ceco_acta = isset($acta['cod_local'])? $acta['cod_local'] : 'null';
        if($ceco_acta!=$local_numero)
            return (object)['error'=>"El local indicado en el acta, no corresponde con el inventario seleccionado (acta:$ceco_acta|inventario:$local_numero)."];

        return (object)['acta'=>$acta];
    }


    private static function __array_a_actaFCV($datos){
        function _get(&$value, $default=null) {
            if( isset($value) && trim($value)!='' )
                return $value;
            else
                return null;//return $default;
        }
        function _getDate(&$value, $DEFAULT_DATE='0000-00-00') {
            // La fecha se reciben como texto (ej. '30/03/2016',) el string debe estar definido, tener algo caracter, y
            // tener el DD/MM/AAAA formato valido antes de ser convertido a fecha con el formato correcto '2016-03-30'
            $DATE_FORMAT = 'd/m/Y';
            if( isset($value) ){
                $date = Carbon::createFromFormat($DATE_FORMAT, $value);
                return $date!=false? $date->toDateString() : $DEFAULT_DATE;
            }else
                return null;//return $DEFAULT_DATE;
        }
        function _getDatetime(&$value, $DEFAULT_DATETIME='00/00/00 00:00:00'){
            // Los datetime se reciben como texto, el string debe estar definido, tener algo caracter, y tener el
            // formato valido antes de ser convertido a datetime
            $DATETIME_FORMAT = 'd/m/Y H:i:s';
            if( isset($value) && trim($value)!=''){
                $datetime = Carbon::createFromFormat($DATETIME_FORMAT, trim($value));
                return $datetime!=false? $datetime->toDateTimeString() : $DEFAULT_DATETIME;
            }else{
                return null; //return $DEFAULT_DATETIME;
            }
        }
        return [
            'presupuesto'       => _get($datos['presupuesto']),             // integer
            'efectiva'          => _get($datos['efectiva']),                // integer
            'hora_llegada'      => _get($datos['hora_llegada']),            // TODO: time funciona igual??
            'administrador'     => _get($datos['administrador']),           // string
            'porcentaje'        => _get($datos['porcentaje']),              // integer
            'captura_uno'       => _getDatetime($datos['captura_uno']),     // dateTime
            'emision_cero'      => _getDatetime($datos['emision_cero']),    // dateTime
            'emision_variance'  => _getDatetime($datos['emision_variance']),// dateTime
            'inicio_sumary'     => _getDatetime($datos['inicio_sumary']),   // dateTime
            'fin_captura'       => _getDatetime($datos['fin_captura']),     // dateTime
            'unidades'          => _get($datos['unidades']),                // integer
            'teorico_unidades'  => _get($datos['teorico_unidades']),        // integer
            'fecha_toma'        => _getDate($datos['fecha_toma']),          // date
            'cod_local'         => _get($datos['cod_local']),               // integer
            'nombre_empresa'    => _get($datos['nombre_empresa']),          // string
            'usuario'           => _get($datos['usuario']),                 // string
            'nota1'             => _get($datos['nota1']),  // integer
            'nota2'             => _get($datos['nota2']),  // integer
            'nota3'             => _get($datos['nota3']),  // integer
            'aud1'              => _get($datos['Aud1']),   // integer
            'aud2'              => _get($datos['Aud2']),   // integer
            'aud3'              => _get($datos['Aud3']),   // integer
            'aud4'              => _get($datos['Aud4']),   // integer
            'aud5'              => _get($datos['Aud5']),   // integer
            'aud6'              => _get($datos['Aud6']),   // integer
            'aju1'              => _get($datos['Aju1']),   // integer
            'aju2'              => _get($datos['Aju2']),   // integer
            'aju3'              => _get($datos['Aju3']),   // integer
            'aju4'              => _get($datos['Aju4']),   // float, 2 decimales de precision
            'tot1'              => _getDatetime($datos['tot1']), // dateTime
            'tot2'              => _get($datos['tot2']),   // integer
            'tot3'              => _get($datos['tot3']),   // integer
            'tot4'              => _get($datos['tot4']),   // integer
            'check1'            => _get($datos['check1']), // integer
            'check2'            => _get($datos['check2']), // integer
            'check3'            => _get($datos['check3']), // integer
            'check4'            => _get($datos['check4']), // integer
            // CAMPOS DE LA VERSION "NUEVA"
            'fecha_revision_grilla'     => _getDatetime($datos['Fecha Revision Grilla']),   // dateTime
            'supervisor_qf'             => _getDate($datos['Supervisor QF']),               // date
            'diferencia_unid_absoluta'  => _get($datos['Diferencia Unidades Absoluta']),    // integer
            'ptt_inventariadas'         => _get($datos['Cantidad PTT Inventariadas']),      // integer
            'ptt_rev_qf'                => _get($datos['PTT Revisadas QF']),                // integer
            'ptt_rev_apoyo1'            => _get($datos['PTT Rev. Apoyo 1']),                // integer
            'ptt_rev_apoyo2'            => _get($datos['PTT Rev. Apoyo 2']),                // integer
            'ptt_rev_supervisor_fcv'    => _get($datos['PTT Rev. Sup. FCV']),               // integer
            'total_items_inventariados' => _get($datos['Total Item Inventariados']),        // integer
            'items_auditados'           => _get($datos['Item Auditados']),                  // integer
            'items_corregidos_auditoria'=> _get($datos['Items Corregido Auditoria']),       // integer
            'items_rev_qf'      => _get($datos['Items Revisadas QF']),                      // integer
            'items_rev_apoyo1'  => _get($datos['Items Rev. Apoyo 1']),                      // integer
            'items_rev_apoyo2'  => _get($datos['Items Rev. Apoyo 2']),                      // integer
            'unid_neto_corregido_auditoria'     => _get($datos['Unidades Neto corregido Auditoria']),       // integer
            'unid_absoluto_corregido_auditoria' => _get($datos['Unidades Absoluto corregido Auditoria']),   // integer
            'total_sku_efectivos' => _get($datos['Total SKU (Códigos internos) efectivos local']),          // integer
            'porcentaje_error_qf' => _get($datos['(% Error QF CV)']),                                       // float
            'porcentaje_variacion_ajuste_grilla' => _get($datos['% Variacion Ajuste Neto (Grilla)']),       // float
        ];
    }

    private static function txt_a_array($archivoActa){
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
}