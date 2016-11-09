<?php namespace App\Services;
// Utils
use App\ProductosFCV;
use Carbon\Carbon;
// Contracts
use App\Contracts\MaestraFCVContract;
// Modelos
use DB;
use App\ArchivoMaestraProductos;
use App\Clientes;

class MaestraFCVService implements MaestraFCVContract {
    // como siempre, este modulo quedo incompleto..., falta mejorar la carga de maestra en archivos grandes, y todas
    // las acciones para actualizar, y editar una maestra

    // Actualizaciones
    public function agregarMaestraFCV($user, $archivo){
        // validar permisos
        if(!$user || !$user->can('fcv-administrarMaestra'))
            return $this->_error('user', 'no tiene permisos', 403);

        $clienteFCV = Clientes::find(2);

        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $cliente = $clienteFCV->nombreCorto;
        $extra = "[$timestamp][$cliente]";
        $carpetaArchivosMaestra = ArchivoMaestraProductos::getPathCarpeta($cliente);

        // mover el archivo a la carpeta que corresponde
        $archivoFinal = \ArchivosHelper::moverACarpeta($archivo, $extra, $carpetaArchivosMaestra);

        // ... y crear un registro en la BD
        $archivoMaestraFCV = ArchivoMaestraProductos::create([
            'idCliente' => $clienteFCV->idCliente,
            'idSubidoPor' => $user? $user->id : null,
            'nombreArchivo' => $archivoFinal->nombre_archivo,
            'nombreOriginal' => $archivoFinal->nombre_original,
            'resultado' => 'archivo en proceso'
        ]);

        //
        return $this->procesarMaestraFCV($user, $archivoMaestraFCV);
    }
    public function procesarMaestraFCV($user, $archivoMaestraProductos){
        // parsear Excel a Array de productos
        $path = $archivoMaestraProductos->getFullPath();
        $idArchivo = $archivoMaestraProductos->idArchivoMaestra;
        $resultadoProductos = \ArchivoMaestraFCVHelper::parsearExcelAProductos($path, $idArchivo);
        if(isset($resultadoProductos->error)){
            $archivoMaestraProductos->setResultado($resultadoProductos->error, false);
            return $this->_error('archivo', $resultadoProductos->error, 400);
        }

        // Eliminar los productos anteriores de este mismo idArchivoMaestra
        ProductosFCV::where('idArchivoMaestra', $idArchivo)->delete();

        // Agregar productos a la DB
        $chunks = array_chunk($resultadoProductos->productos, 5000, true);
        DB::transaction(function() use ($chunks){
            foreach($chunks as $chunk) {
                ProductosFCV::insert($chunk);
            }
        });

        return $this->validarProductosFCV($user, $archivoMaestraProductos);

    }
    public function validarProductosFCV($user, $archivoMaestraProductos){
        // validar permisos
        if(!$user || !$user->can('fcv-administrarMaestra'))
            return $this->_error('user', 'no tiene permisos', 403);

        $barras          = $archivoMaestraProductos->getBarrasDuplicadas()->total;
        $vacios          = $archivoMaestraProductos->getCamposVacios()->total;
        $descriptores    = $archivoMaestraProductos->getDescriptoresDistintos()->total;
        $laboratorios    = $archivoMaestraProductos->getLaboratoriosDistintos()->total;
        $clasificaciones = $archivoMaestraProductos->getClasificacionesDistintas()->total;

        $error = null;
        if($barras>0)
            $error = "$barras barras duplicadas. ";
        if($vacios>0)
            $error .= "$vacios productos con campos vacios. ";
        if($descriptores>0)
            $error .= "$descriptores descriptores inconsistentes. ";
        if($laboratorios>0)
            $error .= "$laboratorios laboratorios inconsistentes. ";
        if($clasificaciones>0)
            $error .= "$clasificaciones c.terapeuticas inconsistentes. ";

        if($error==null){
            $archivoMaestraProductos->setResultado("Productos cargados correctamente", true);
            return [];
        }else{
            $error = 'Productos cargados. '.$error;
            $archivoMaestraProductos->setResultado($error, false);
            return $this->_error('productos', $error, 400);
        }
    }
    public function descargarMaestraDesdeDB($user, $archivoMaestraProductos){
        // validar permisos
        if(!$user || !$user->can('fcv-administrarMaestra'))
            return $this->_error('user', 'no tiene permisos', 403);
        if(!$archivoMaestraProductos)
            return $this->_error('Maestra de productos', 'La maestra que busca no ha sido encontrada', 404);

        $productos = DB::select(DB::raw("
            select sku, barra, descriptor, laboratorio, clasificacionTerapeutica from productos_fcv 
            where idArchivoMaestra = $archivoMaestraProductos->idArchivoMaestra
        "));

        return (object)[
            'maestraPath' => \ExcelHelper::generarXLSX_maestraFCV($productos),
        ];
    }

    // Actualizaciones
    public function subirActualizacionFCV(){

    }
    public function procesarActualizacionFCV(){

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