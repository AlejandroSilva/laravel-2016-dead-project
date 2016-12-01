<?php namespace App\Services;
// Utils
use App\CapturaRespuestaWOM;
use Carbon\Carbon;
use DB;
// Contracts
use App\Contracts\RespuestaWOMContract;
// Modelos
use App\ArchivoRespuestaWOM;
use App\Clientes;
use League\Flysystem\Exception;

class RespuestaWOMService implements RespuestaWOMContract {
    // todo: segun marco la respuesta biene buena desde la pda... no se hace ningun tipo de validacion...

    public function agregarZipRespuestaWOM($user, $archivo) {
        // validar permisos
        if(!$user || !$user->can('wom-subirArchivosRespusta'))
            return $this->_error('user', 'no tiene permisos', 403);

        $clienteWOM = Clientes::find(9);
        // extras en el nombre del archivo
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $cliente = $clienteWOM->nombreCorto;
        //$hash = md5(uniqid(rand(), true));
        $extra = "[$timestamp][$cliente]";
        $pathArchivosMaestra = ArchivoRespuestaWOM::getPathCarpeta($cliente);
        // mover el archivo a la carpeta que corresponde
        $archivo = \ArchivosHelper::moverACarpeta($archivo, $extra, $pathArchivosMaestra);

        // ... y crear un registro en la BD
        $archivoRespuestaWOM = ArchivoRespuestaWOM::create([
            'idCliente' => $clienteWOM->idCliente,
            'idSubidoPor' => $user? $user->id : null,
            'nombreArchivo' => $archivo->nombre_archivo,
            'nombreOriginal' => $archivo->nombre_original,
            'resultado' => 'archivo en proceso'
        ]);
        $archivoRespuestaWOM->setResultado("archivo en proceso", false);

        // despues de cargar, se pueden procesar los productos y luego validarlos...
        //return $this->procesarMaestraWOM($user, $archivoMaestraWOM);
        return $this->procesarArchivoZip($user, $archivoRespuestaWOM);
    }

    public function procesarArchivoZip($user, $archivoRespuesta){
        //$archivoRespuesta = ArchivoRespuestaWOM::find(8);
        $zipPath = $archivoRespuesta->getFullPath();
        $idArchivo = $archivoRespuesta->idArchivoRespuestaWOM;

        // ########### Leer datos del conteo final
        $txtConteoFinal = \ArchivosHelper::extraerArchivo($zipPath, 'My Documents/Auditoria_Final_Conteo.txt');
        if(isset($txtConteoFinal->error))
            return (object)[
                'error'=> "Error al leer el conteo final: $txtConteoFinal->error",
                'archivoRespuesta' => $archivoRespuesta
            ];

        // obtener los registros
        $resultadoParsear = $this->_parsearTXTConteoFinal($txtConteoFinal->fullpath, $idArchivo);
        if(isset($resultadoParsear->error))
            return (object)[
                'error'=> "Error al parsear el archivo de conteo final: $resultadoParsear->error",
                'archivoRespuesta' => $archivoRespuesta
            ];

        // guardarlos/reemplazarlos  en la BD
        $registros = $resultadoParsear->registros;
        CapturaRespuestaWOM::where('idArchivoRespuestaWOM', $idArchivo)->delete();
        $archivoRespuesta->capturas()->delete();
        $archivoRespuesta->capturas()->insert($registros);


        // ########### Leer datos del acta
        $txtActa = \ArchivosHelper::extraerArchivo($zipPath, 'My Documents/ArchivoActa.txt');
        if(isset($txtActa->error))
            return (object)[
                'error'=> "Error al leer el acta: $txtActa->error. EL ARCHIVO NO HA SIDO CARGADO AL SISTEMA",
                'archivoRespuesta' => $archivoRespuesta
            ];
        $acta = $this->_parsearTXTActa($txtActa->fullpath);

        // guardar los datos extraidos del acta
        $archivoRespuesta->organizacion             = isset($acta['Organizacion']) ? $acta['Organizacion'] :null;
        $archivoRespuesta->liderWom                 = isset($acta['Lider WOM']) ? $acta['Lider WOM'] :null;
        $archivoRespuesta->runLiderWom              = isset($acta['Rut wom']) ? $acta['Rut wom'] :null;
        $archivoRespuesta->liderSei                 = isset($acta['Lider SEI']) ? $acta['Lider SEI'] :null;
        $archivoRespuesta->runLiderSei              = isset($acta['Rut sei']) ? $acta['Rut sei'] :null;
        // unidades
        $archivoRespuesta->unidadesContadas         = isset($acta['Undades contadas']) ?
                                                        str_replace('.', '', $acta['Undades contadas']) :null;
        $archivoRespuesta->unidadesNuevo            = isset($acta['Unidades en NUEVO']) ?
                                                        str_replace('.', '', $acta['Unidades en NUEVO']) :null;
        $archivoRespuesta->unidadesUsado            = isset($acta['Unidades en USADO']) ?
                                                        str_replace('.', '', $acta['Unidades en USADO']) :null;
        $archivoRespuesta->unidadesPrestamo         = isset($acta['Unidades en PRESTAMO']) ?
                                                        str_replace('.', '', $acta['Unidades en PRESTAMO']) :null;
        $archivoRespuesta->unidadesServTecnico      = null;
        $archivoRespuesta->tiempoTranscurrido       = isset($acta['Tiempo Transcurrido : ']) ? $acta['Tiempo Transcurrido : '] :null;
        $archivoRespuesta->evaluacionAServicioSEI   = isset($acta['Evaluación a servicio SEI']) ? $acta['Evaluación a servicio SEI'] :null;
        // preguntas
        $archivoRespuesta->identificoTodosLosSectores       = isset($acta['Identifico todos sectores']) ? $acta['Identifico todos sectores'] :null;
        $archivoRespuesta->identificoEstadoDeTelefonos      = isset($acta['Identifico ESTADO de telefonos']) ? $acta['Identifico ESTADO de telefonos'] :null;
        $archivoRespuesta->identificoCajasSIMAbiertas       = isset($acta['Identifico cajas SIM abiertas']) ? $acta['Identifico cajas SIM abiertas'] :null;
        $archivoRespuesta->presentaOrdenadoSusProductos     = isset($acta['Presenta ordenado sus productos']) ? $acta['Presenta ordenado sus productos'] :null;
        $archivoRespuesta->seRealizoSegundoConteATelefonos  = isset($acta['Se realizó segundo conteo a télefonos']) ? $acta['Se realizó segundo conteo a télefonos'] :null;
        $archivoRespuesta->escaneoCajasAbiertasSIMUnoAUno   = isset($acta['Escaneo cajas abiertas de SIM uno a uno']) ? $acta['Escaneo cajas abiertas de SIM uno a uno'] :null;
        $archivoRespuesta->tieneBuenaDisposicionYExplica    = isset($acta['Tiene buena disposición y explica AI']) ? $acta['Tiene buena disposición y explica AI'] :null;

        // otros (no viene en el archivo de acta)
        $archivoRespuesta->pttTotal = $archivoRespuesta->getPatentesTotal();    // no viene en el archivo de acta
        $archivoRespuesta->unidadesErrorSei = null;
        $archivoRespuesta->porcentajeErrorSei = null;
        $archivoRespuesta->save();

        // ########### Marcar como correcto
        $archivoRespuesta->setResultado("Productos cargados", true);

        return $acta;
//        return [$txtConteoFinal, $txtActa, $acta];
//        return "ok";
    }

    public function _parsearTXTActa($txtPath){
        $rawdata = \CSVReader::csv_to_array($txtPath, '|');
        $data = [];
        foreach ($rawdata as $row){
            $data["".$row[0]] = isset($row[1])? $row[1] : null;
        }
        return $data;
    }


    public function _parsearTXTConteoFinal($txtPath, $idArchivo){
        $now = Carbon::now();
        // parsear el TXT a Array
        $data = \CSVReader::csv_to_array($txtPath, '|');
        if($data==false)
            return $this->_error('archivo', 'error al leer el archivo txt', 500);

        $registros = [];
        for($i=1; $i<count($data); $i++) {
            $row = $data[$i];

            // parsear fecha
            $fecha = isset($row[2])? trim($row[2]) : null;
            if(\DateTime::createFromFormat('Ymd', $fecha)!=false)
                $fecha = Carbon::createFromFormat('Ymd', $fecha)->format('Y-m-d');
            else
                $fecha = null;

            $registros[] = [
                'idArchivoRespuestaWOM' => $idArchivo,
                'line'                  => $i+1,
                'ptt'                   => isset($row[0])? trim($row[0]) : null,
                'correlativo'           => isset($row[1])? trim($row[1]) : null,
                'fechaCaptura'          => $fecha,
                'horaCaptura'           => null,
                'sku'                   => isset($row[3])? trim($row[3]) : null,
                'conteoInicial'         => isset($row[4])? trim($row[4]) : null,
                'conteoFinal'           => null,
                'serie'                 => isset($row[5])? trim($row[5]) : null,
                'codigoOrganizacion'    => isset($row[6])? trim($row[6]) : null,
                'nombreOrganizacion'    => isset($row[6])? trim($row[7]) : null,
                'estado'                => isset($row[7])? trim($row[8]) : null,
                'created_at'            => $now,
                'updated_at'            => $now
            ];
        }
        return (object)['registros'=>$registros];
    }

//    public function _parsearTXTConteoFinal_ORIGINAL($txtPath, $idArchivo){
//        $now = Carbon::now();
//        // parsear el TXT a Array
//        $data = \CSVReader::csv_to_array($txtPath, ';');
//        if($data==false)
//            return $this->_error('archivo', 'error al leer el archivo txt', 500);
//
//        // convertir Data a Registros
//        $registros = [];
//        $regex_sku = "/^1[0-9]{7}$/";                       // 8 digitos, inicia con "1"
//        $regex_ean15 = "/^[0-9]{15}$/";                     // 15 digitos
//        $regex_ean16 = "/^([a-z]|[A-Z]|[0-9]){16}$/";       // 16 digitos, con letras
//        $regex_ean20 = "/^[0-9]{20}$/";                     // 20 digitos
//
//
//        for($i=1; $i<count($data); $i++){
//            $row = $data[$i];
//            // sanitizar patente
//            if( isset($row[0]) ){
//                $_ptt = array_reverse( explode('|', trim($row[0])) );
//                $ptt = $_ptt[0];
//            }else{
//                $ptt = null;
//            }
//            // verificar que la barra sea valida
//            $barra = isset($row[1])? trim($row[1]) : null;
//            if(preg_match($regex_sku, $barra)){
//                $_barra = "00".$barra;
//                $sku = substr($_barra, 0, 3).".".substr($_barra, 3, 3).".".substr($_barra, 6, 4);
//                $serie = null;
//                $barra_valido = true;
//            }else if(preg_match($regex_ean15, $barra) || preg_match($regex_ean16, $barra) || preg_match($regex_ean20, $barra)){
//                $sku = null;
//                $serie = $barra;
//                $barra_valido = true;
//            }else{
//                $barra_valido = false;
//            }
//
//            $correlativo    = isset($row[2])? trim($row[2]) : null;
//            $conteo         = isset($row[3])? trim($row[3]) : null;
//            $estado         = isset($row[4])? trim($row[4]) : null;
//            $fecha          = isset($row[5])? trim($row[5]) : null;
//            // parsear fecha
//            if(\DateTime::createFromFormat('d-m-Y', $fecha)!=false)
//                $fecha = Carbon::createFromFormat('d-m-Y', $fecha)->format('Y-m-d');
//            else
//                $fecha = null;
//            $hora           = isset($row[6])? trim($row[6]) : null;
//
//            // si la fila no tiene correlativo, ean, conteo, estado, etc. no tomar en cuenta
//            if($ptt!=null && $barra_valido && $correlativo!=null && $conteo!=null /*&& $estado!=null && $fecha!=null && $hora!=null*/){
//                $registros[] = [
//                    'idArchivoRespuestaWOM' => $idArchivo,
//                    'line'                  => $i+1,
//                    // en la patente, se quitan los campos "extras"
//                    'ptt'                   => $ptt,
//                    // el ean, dependiendo del codigo se considera SKU o SERIE
//                    'sku'                   => $sku,
//                    'serie'                 => $serie,
//                    // correlativo, queda igual
//                    'correlativo'           => $correlativo,
//                    // conteo, queda igual
//                    'conteoInicial'         => $conteo,
//                    'conteoFinal'           => null,
//                    // estado, queda igual
//                    'estado'                => $estado,
//                    // fecha, pasa del formato DD-MM-YYYY A YYYYMMDD
//                    'fechaCaptura'          => $fecha,
//                    // hora, queda igual
//                    'horaCaptura'           => $hora,
//                    // organizacion...
//                    'codigoOrganizacion'    => $organizacion,
//                    'nombreOrganizacion'    => $organizacion,
//                    'created_at'            => $now,
//                    'updated_at'            => $now
//                ];
//            }
//        }
//
//        return (object)[
//            'registros'=>$registros
//        ];
//    }

    public function generarTxtConteoFinal($user, $idArchivo){
        // todo: validar permisos del usuario
        $archivoRespuesta = ArchivoRespuestaWOM::find($idArchivo);
        $capturas = $archivoRespuesta->capturas;

        // nombre de la descarga
        $nombreOriginal = $archivoRespuesta->nombreOriginal;
        $extensionIndex = strrpos($nombreOriginal, ".");
        $basename = substr($nombreOriginal, 0, $extensionIndex);

        // crear txt
        $random_number= md5(uniqid(rand(), true));
        $fullpath = public_path()."/tmp/respuestaWOM_$random_number.txt";
        $archivoTxt = fopen($fullpath, "w");
        fwrite($archivoTxt, "Nro. Documento|Nro. Linea|Fecha Despacho|Código Material|Cantidad|Número de Serie|Org. Origen|Comentario|Estado");
        foreach ($capturas as $cap){
            $fecha = Carbon::parse($cap->fechaCaptura)->format('Ymd');
            $line = "\r\n$cap->ptt|$cap->correlativo|$fecha|$cap->sku|$cap->conteoInicial|$cap->serie|$cap->codigoOrganizacion|$cap->nombreOrganizacion|$cap->estado";
            fwrite($archivoTxt, $line);
        }
        fclose($archivoTxt);

        return (object)[
            'fullPath' => $fullpath,
            'fileName' => "ConteoFinal_$archivoRespuesta->organizacion.txt"
        ];
    }

    public function generarExcel($user, $idArchivo){
        // todo: validar permisos del usuario
        $archivoRespuesta = ArchivoRespuestaWOM::find($idArchivo);
        $capturas = $archivoRespuesta->capturas;
        $nombreOriginal = $archivoRespuesta->nombreOriginal;
        $extensionIndex = strrpos($nombreOriginal, ".");
        $basename = substr($nombreOriginal, 0, $extensionIndex);
        return (object)[
            'fullPath' => \ExcelHelper::generarXLSX_capturasRespuestaWOM($capturas),
            'fileName' => "$basename.xlsx"
        ];
    }

    // privados
    private function _error($campo, $mensaje, $codigo){
        return (object)[
            'campo'=>$campo,
            'mensaje' => $mensaje,
            'error'=>[
                "$campo"=>$mensaje
            ],
            'codigo'=>$codigo
        ];
    }
}