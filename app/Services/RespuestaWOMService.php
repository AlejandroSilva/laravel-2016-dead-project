<?php namespace App\Services;
// Utils
use Carbon\Carbon;
use DB;
// Contracts
use App\Contracts\RespuestaWOMContract;
// Modelos
use App\ArchivoRespuestaWOM;
use App\Clientes;

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
        $extra = "[$timestamp][$cliente]";
        $carpetaArchivosMaestra = ArchivoRespuestaWOM::getPathCarpeta($cliente);
        // mover el archivo a la carpeta que corresponde
        $archivoRespuesta = \ArchivosHelper::moverACarpeta($archivo, $extra, $carpetaArchivosMaestra);

        // ... y crear un registro en la BD
        $archivoMaestraWOM = ArchivoRespuestaWOM::create([
            'idCliente' => $clienteWOM->idCliente,
            'idSubidoPor' => $user? $user->id : null,
            'nombreArchivo' => $archivoRespuesta->nombre_archivo,
            'nombreOriginal' => $archivoRespuesta->nombre_original,
            'resultado' => 'archivo en proceso'
        ]);
        $archivoMaestraWOM->setResultado("no se validan los productos, solo se recibe el archivo", true);

        // despues de cargar, se pueden procesar los productos y luego validarlos...
        //return $this->procesarMaestraWOM($user, $archivoMaestraWOM);
        return [];
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