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

    private static function __array_a_actaFCV($d){
        function _get(&$value, &$value2=null) {
            // si existe el primer valor, entregarlo
            if( isset($value) && trim($value)!='' )
                return $value;
            else{
                // si no existe el primero, tratar con el segundo
                if( isset($value2) && trim($value2)!='' )
                    return $value2;
                else
                    return null;
            }
        }
        function _getDate(&$value, $DEFAULT_DATE='0000-00-00') {
            // La fecha se reciben como texto (ej. '30/03/2016',) el string debe estar definido, tener algo caracter, y
            // tener el DD/MM/AAAA formato valido antes de ser convertido a fecha con el formato correcto '2016-03-30'
            $DATE_FORMAT = 'd/m/Y';
            if( isset($value) ){
                try{
                    $date = Carbon::createFromFormat($DATE_FORMAT, $value);
                    return $date!=false? $date->toDateString() : $DEFAULT_DATE;
                }catch(InvalidArgumentException $e){
                    // esto puede ocurrir porque no entrega la fecha en el formato que corresponde...
                    return null;
                }
            }else
                return null;
        }
        function _getDatetime(&$value, $DEFAULT_DATETIME='00/00/00 00:00:00'){
            // Los datetime se reciben como texto, el string debe estar definido, tener algo caracter, y tener el
            // formato valido antes de ser convertido a datetime
            $DATETIME_FORMAT = 'd/m/Y H:i:s';
            if( isset($value) && trim($value)!=''){
                $datetime = Carbon::createFromFormat($DATETIME_FORMAT, trim($value));
                return $datetime!=false? $datetime->toDateTimeString() : $DEFAULT_DATETIME;
            }else{
                return null;
            }
        }
        return [
            'presupuesto'       => _get($d['presupuesto']),
            'efectiva'          => _get($d['efectiva']),
            'hora_llegada'      => _get($d['hora_llegada']),
            'administrador'     => _get($d['administrador']),
            'porcentaje'        => _get($d['porcentaje']),
            'captura_uno'       => _getDatetime($d['captura_uno']),
            'emision_cero'      => _getDatetime($d['emision_cero']),
            'emision_variance'  => _getDatetime($d['emision_variance']),
            'inicio_sumary'     => _getDatetime($d['inicio_sumary']),
            'fin_captura'       => _getDatetime($d['fin_captura']),
            'unidades'          => _get($d['unidades']),
            'teorico_unidades'  => _get($d['teorico_unidades']),
            'fecha_toma'        => _getDate($d['fecha_toma']),
            'cod_local'         => _get($d['cod_local']),
            'nombre_empresa'    => _get($d['nombre_empresa']),
            'usuario'           => _get($d['usuario']),
            'nota1'             => _get($d['nota1']),
            'nota2'             => _get($d['nota2']),
            'nota3'             => _get($d['nota3']),
            'aud1'              => _get($d['Aud1']),
            'aud2'              => _get($d['Aud2']),
            'aud3'              => _get($d['Aud3']),
            'aud4'              => _get($d['Aud4']),
            'aud5'              => _get($d['Aud5']),
            'aud6'              => _get($d['Aud6']),
            'aju1'              => _get($d['Aju1']),
            'aju2'              => _get($d['Aju2']),
            'aju3'              => _get($d['Aju3']),
            'aju4'              => _get($d['Aju4']),
            'tot1'              => _getDatetime($d['tot1']),
            'tot2'              => _get($d['tot2']),
            'tot3'              => _get($d['tot3']),
            'tot4'              => _get($d['tot4']),
            'check1'            => _get($d['check1']),
            'check2'            => _get($d['check2']),
            'check3'            => _get($d['check3']),
            'check4'            => _get($d['check4']),
            // CAMPOS DE LA VERSION "NUEVA"
            'fecha_revision_grilla'     => _getDatetime($d['Fecha Revision Grilla']),
            'supervisor_qf'             => _getDate($d['Supervisor QF']),
            'diferencia_unid_absoluta'  => _get($d['Diferencia Unidades Absoluta']),
            'ptt_inventariadas'         => _get($d['Cantidad PTT Inventariadas']),
            'ptt_rev_qf'                => _get($d['PTT Revisadas QF'], $d['QF Total Patentes']),       // dato repetido en 2 lados
            'ptt_rev_apoyo1'            => _get($d['PTT Rev. Apoyo 1'], $d['A1 Total Patentes']),       // dato repetido en 2 lados
            'ptt_rev_apoyo2'            => _get($d['PTT Rev. Apoyo 2'], $d['A2 Total Patentes']),       // dato repetido en 2 lados
            'ptt_rev_supervisor_fcv'    => _get($d['PTT Rev. Sup. FCV'], $d['SUP CV Total Patentes']),  // dato repetido en 2 lados
            'total_items_inventariados' => _get($d['Total Item Inventariados']),
            'items_auditados'           => _get($d['Item Auditados']),
            'items_corregidos_auditoria'=> _get($d['Items Corregido Auditoria']),
            'items_rev_qf'      => _get($d['Items Revisadas QF'], $d['QF Total Items']),                // dato repetido en 2 lados
            'items_rev_apoyo1'  => _get($d['Items Rev. Apoyo 1'], $d['A1 Total Items']),                // dato repetido en 2 lados
            'items_rev_apoyo2'  => _get($d['Items Rev. Apoyo 2']),
            'unid_neto_corregido_auditoria'     => _get($d['Unidades Neto corregido Auditoria']),
            'unid_absoluto_corregido_auditoria' => _get($d['Unidades Absoluto corregido Auditoria']),
            'total_sku_efectivos' => _get($d['Total SKU (Códigos internos) efectivos local']),
            'porcentaje_error_qf' => _get($d['(% Error QF CV)']),
            'porcentaje_variacion_ajuste_grilla' => _get($d['% Variacion Ajuste Neto (Grilla)']),

            // ordenar luego
            'qf_total_patentes'             => _get($d['QF Total Patentes'], $d['PTT Revisadas QF']),   // dato repetido en 2 lados
            'qf_total_unidades'             => _get($d['QF Total Unidades']),
            'qf_total_items'                => _get($d['QF Total Items'], $d['Items Revisadas QF']),    // dato repetido en 2 lados
            'apoyo1_total_patentes'         => _get($d['A1 Total Patentes'], $d['PTT Rev. Apoyo 1']),   // dato repetido en 2 lados
            'apoyo1_total_unidades'         => _get($d['A1 Total Unidades']),
            'apoyo1_total_items'            => _get($d['A1 Total Items'], $d['Items Rev. Apoyo 1']),    // dato repetido en 2 lados
            'apoyo2_total_patentes'         => _get($d['A2 Total Patentes'], $d['PTT Rev. Apoyo 2']),   // dato repetido en 2 lados
            'apoyo2_total_unidades'         => _get($d['A2 Total Unidades']),
            'apoyo2_total_items'            => _get($d['A2 Total Items'], $d['Items Rev. Apoyo 2']),    // dato repetido en 2 lados
            'supervisor_total_patentes'     => _get($d['SUP CV Total Patentes'], $d['PTT Rev. Sup. FCV']), // dato repetido en 2 lados
            'supervisor_total_unidades'     => _get($d['SUP CV Total Unidades']),
            'supervisor_total_items'        => _get($d['SUP CV Total Items']),

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