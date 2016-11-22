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

    public function agregarArchivoRespuestaWOM($user, $archivo) {
        // validar permisos
        if(!$user || !$user->can('wom-subirArchivosRespusta'))
            return $this->_error('user', 'no tiene permisos', 403);

        $clienteWOM = Clientes::find(9);
        // extras en el nombre del archivo
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $cliente = $clienteWOM->nombreCorto;
        //$hash = md5(uniqid(rand(), true));
        $extra = "[$timestamp][$cliente][conteo1]";
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
        $archivoRespuestaWOM->setResultado("no se validan los productos, solo se recibe el archivo", true);

        // despues de cargar, se pueden procesar los productos y luego validarlos...
        //return $this->procesarMaestraWOM($user, $archivoMaestraWOM);
        return $this->procesarArchivo($user, $archivoRespuestaWOM);
    }

    public function agregarArchivoRespuestaWOM_conteo2($user, $idArchivoConteo1, $archivo) {
        // validar permisos
        if(!$user || !$user->can('wom-subirArchivosRespusta'))
            return $this->_error('user', 'no tiene permisos', 403);

        $clienteWOM = Clientes::find(9);
        // extras en el nombre del archivo
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $cliente = $clienteWOM->nombreCorto;
        //$hash = md5(uniqid(rand(), true));
        $extra = "[$timestamp][$cliente][conteo2]";
        $pathArchivosMaestra = ArchivoRespuestaWOM::getPathCarpeta($cliente);
        // mover el archivo a la carpeta que corresponde
        $archivo = \ArchivosHelper::moverACarpeta($archivo, $extra, $pathArchivosMaestra);

        // ... y crear un registro en la BD
        $archivoRespuestaWOM = ArchivoRespuestaWOM::find($idArchivoConteo1);
        $archivoRespuestaWOM->nombreArchivoConteo2 = $archivo->nombre_archivo;
        $archivoRespuestaWOM->nombreOriginalConteo2 = $archivo->nombre_original;
        $archivoRespuestaWOM->save();
        return $this->procesarArchivoConteo2($archivoRespuestaWOM);
    }

    public function procesarArchivo($user, $archivoRespuesta){
        //$archivoRespuesta = ArchivoRespuestaWOM::find(8);
        $txtPath = $archivoRespuesta->getFullPath();
        $idArchivo = $archivoRespuesta->idArchivoRespuestaWOM;

        // TODO: obtener el numero de local del archivo, esto debe ser sustituido por otro metodo mas tarde
        $organizacion = $archivoRespuesta->getOrganizacionDesdeNombreArchivo();

        // obtener los registros
        $resultadoParsear = $this->_parsearTXT($txtPath, $idArchivo, $organizacion);
        if(isset($resultadoParsear->error))
            return $resultadoParsear;

        // guardarlos/reemplazarlos  en la BD
        $registros = $resultadoParsear->registros;
        //CapturaRespuestaWOM::where('idArchivoRespuestaWOM', $idArchivo)->delete();
        $archivoRespuesta->capturas()->delete();
        $archivoRespuesta->capturas()->insert($registros);
        return $registros;
    }

    public function procesarArchivoConteo2($archivoRespuesta){
        //$archivoRespuesta = ArchivoRespuestaWOM::find(8);
        $txt2Path = $archivoRespuesta->getFullPath2();
        // TODO: obtener el numero de local del archivo, esto debe ser sustituido por otro metodo mas tarde
        $unaCaptura = $archivoRespuesta->capturas->first();
        $organizacion = $unaCaptura? $unaCaptura->codigoOrganizacion : '-';

        // obtener los registros de la captura2
        $resultadoParsear = $this->_parsearTXT($txt2Path, $archivoRespuesta->idArchivoRespuestaWOM, $organizacion);
        if(isset($resultadoParsear->error))
            return $resultadoParsear;

        // buscar los registros en captura2 que no esten en captura1
        $registrosNuevos = [];
        foreach( $resultadoParsear->registros as $cap2){
            if( $archivoRespuesta->tieneBarra($cap2['sku'], $cap2['serie']) ){
                // si lo tiene, actualizar conteo2?
            }else{
                $cap2['sku'] = $cap2['sku']==null? '' : $cap2['sku'];   // sku no puede ser null
                $cap2['conteoFinal'] = $cap2['conteoInicial'];          // para diferenciarlos del conteo2
                $registrosNuevos[] = $cap2;
            }
        }
        // agregar los registros
        $archivoRespuesta->capturas()->insert($registrosNuevos);
        return $registrosNuevos;
    }

    public function _parsearTXT($txtPath, $idArchivo, $organizacion){
        $now = Carbon::now();
        // parsear el TXT a Array
        $data = \CSVReader::csv_to_array($txtPath, ';');
        if($data==false)
            return $this->_error('archivo', 'error al leer el archivo txt', 500);

        // convertir Data a Registros
        $registros = [];
        $regex_sku = "/^1[0-9]{7}$/";                       // 8 digitos, inicia con "1"
        $regex_ean15 = "/^[0-9]{15}$/";                     // 15 digitos
        $regex_ean16 = "/^([a-z]|[A-Z]|[0-9]){16}$/";       // 16 digitos, con letras
        $regex_ean20 = "/^[0-9]{20}$/";                     // 20 digitos


        for($i=1; $i<count($data); $i++){
            $row = $data[$i];
            // sanitizar patente
            if( isset($row[0]) ){
                $_ptt = array_reverse( explode('|', trim($row[0])) );
                $ptt = $_ptt[0];
            }else{
                $ptt = null;
            }
            // verificar que la barra sea valida
            $barra = isset($row[1])? trim($row[1]) : null;
            if(preg_match($regex_sku, $barra)){
                $_barra = "00".$barra;
                $sku = substr($_barra, 0, 3).".".substr($_barra, 3, 3).".".substr($_barra, 6, 4);
                $serie = null;
                $barra_valido = true;
            }else if(preg_match($regex_ean15, $barra) || preg_match($regex_ean16, $barra) || preg_match($regex_ean20, $barra)){
                $sku = null;
                $serie = $barra;
                $barra_valido = true;
            }else{
                $barra_valido = false;
            }

            $correlativo    = isset($row[2])? trim($row[2]) : null;
            $conteo         = isset($row[3])? trim($row[3]) : null;
            $estado         = isset($row[4])? trim($row[4]) : null;
            $fecha          = isset($row[5])? trim($row[5]) : null;
            // parsear fecha
            if(\DateTime::createFromFormat('d-m-Y', $fecha)!=false)
                $fecha = Carbon::createFromFormat('d-m-Y', $fecha)->format('Y-m-d');
            else
                $fecha = null;
            $hora           = isset($row[6])? trim($row[6]) : null;

            // si la fila no tiene correlativo, ean, conteo, estado, etc. no tomar en cuenta
            if($ptt!=null && $barra_valido && $correlativo!=null && $conteo!=null /*&& $estado!=null && $fecha!=null && $hora!=null*/){
                $registros[] = [
                    'idArchivoRespuestaWOM' => $idArchivo,
                    'line'                  => $i+1,
                    // en la patente, se quitan los campos "extras"
                    'ptt'                   => $ptt,
                    // el ean, dependiendo del codigo se considera SKU o SERIE
                    'sku'                   => $sku,
                    'serie'                 => $serie,
                    // correlativo, queda igual
                    'correlativo'           => $correlativo,
                    // conteo, queda igual
                    'conteoInicial'         => $conteo,
                    'conteoFinal'           => null,
                    // estado, queda igual
                    'estado'                => $estado,
                    // fecha, pasa del formato DD-MM-YYYY A YYYYMMDD
                    'fechaCaptura'          => $fecha,
                    // hora, queda igual
                    'horaCaptura'           => $hora,
                    // organizacion...
                    'codigoOrganizacion'    => $organizacion,
                    'nombreOrganizacion'    => $organizacion,
                    'created_at'            => $now,
                    'updated_at'            => $now
                ];
            }
        }

        return (object)[
            'registros'=>$registros
        ];
    }

    public function generarTxt($user, $idArchivo){
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
            'fileName' => "$basename.txt"
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