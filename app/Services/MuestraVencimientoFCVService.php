<?php namespace App\Services;
// Utils
use App\Contracts\MuestraVencimientoFCVServiceContract;
use App\ArchivoMuestraVencimientoFCV;
use Carbon\Carbon;
// Contracts
//use App\Contracts\MaestraFCVContract;
// Modelos
use DB;
//use App\ArchivoMaestraProductos;
use App\Clientes;

class MuestraVencimientoFCVService implements MuestraVencimientoFCVServiceContract{

    // Agregar archivo
    public function agregarArchivoMuestra($user, $archivoFomulario){
        // valida los permisos
        if(!$user || !$user->can('fcv-administrarMuestras'))
            return $this->_error('auth', 'no tiene permisos para realizar esta accion', 403);

        $clienteFCV = Clientes::find(2);
        // extras en el nombre del archivo
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $extra = "[$timestamp][$clienteFCV->nombreCorto][M.VENC]_";
        $carpetaDestino = ArchivoMuestraVencimientoFCV::getPathCarpetaArchivos();

        // mover el archivo a la carpeta correspondiente
        $archivoFinal = \ArchivosHelper::moverACarpeta($archivoFomulario, $extra, $carpetaDestino);

        // agregar el registro del archivo en la BD
        $archivoMuestraFCV = ArchivoMuestraVencimientoFCV::create([
            'idSubidoPor' => $user->id,
            'nombreArchivo' => $archivoFinal->nombre_archivo,
            'nombreOriginal' => $archivoFinal->nombre_original,
            'resultado' => 'ANÁLISIS DE ARCHIVO INCOMPLETO',
        ]);

        // todo este servicio queda pendiente.... por ahora solo recibe una muestra

        return []; // ok por ahora
        // $$this->procesarArchivoMuestra($user, $archivoMuestraFCV);
    }
    public function procesarArchivoMuestra($user, $archivoMaestraProductos){
        // pendiente como siempre
        /*
        // 1) leer los datos del xlsx
        $resultadoExcel = \ExcelHelper::leerExcel($archivoMuestraVencimiento->getFullPath());
        if( $resultadoExcel->error!=null ){
            $archivoMuestraVencimiento->setResultado($resultadoExcel->error, false);
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', $resultadoExcel->error);
        }

        // 2) parsear los datos al formato correcto
        $resultadoParseo = \MuestraVencimiento::parsearArrayMuestraFCV($resultadoExcel->datos, $archivoMuestraVencimiento->idArchivoMuestraVencimientoFCV);
        if( $resultadoParseo->error!=null ){
            $archivoMuestraVencimiento->setResultado($resultadoParseo->error, false);
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', $resultadoParseo->error);
        }

        // insertar los datos en la BD
        $archivoMuestraVencimiento->agregarDatos($resultadoParseo->datos);

        // todo: validar: buscar duplicados o algun otro error

        // se agrego correctamente el archivo a la BD
        $archivoMuestraVencimiento->setResultado("Archivo cargado correctamente a la DB.", true);
        */
    }
    public function validarProductosFCV($user, $archivoMaestraProductos){
        // pendiente como siempre
    }

    // GENERACION DE MUESTRAS
    public function generarMuestra(){

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
    private function _parsearExcelAProductos($excelPath, $idArchivoMaestra){

    }
}