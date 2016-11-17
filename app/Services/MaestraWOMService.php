<?php namespace App\Services;
// Utils
use Carbon\Carbon;
// Contracts
use App\Contracts\MaestraWOMContract;
// Modelos
use DB;
use App\ArchivoMaestraProductos;
use App\Clientes;

class MaestraWOMService implements MaestraWOMContract {
    // todo: segun marco la maestra esta correcta... no se hace ningun tipo de validacion...

    // Maestra
    public function agregarMaestraWOM($user, $archivo){
        // validar permisos
        if(!$user || !$user->can('wom-administrarMaestra'))
            return $this->_error('user', 'no tiene permisos', 403);

        $clienteWOM = Clientes::find(9);
        // extras en el nombre del archivo
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $cliente = $clienteWOM->nombreCorto;
        $extra = "[$timestamp][$cliente]";
        $carpetaArchivosMaestra = ArchivoMaestraProductos::getPathCarpeta($cliente);
        // mover el archivo a la carpeta que corresponde
        $archivoFinal = \ArchivosHelper::moverACarpeta($archivo, $extra, $carpetaArchivosMaestra);

        // ... y crear un registro en la BD
        $archivoMaestraWOM = ArchivoMaestraProductos::create([
            'idCliente' => $clienteWOM->idCliente,
            'idSubidoPor' => $user? $user->id : null,
            'nombreArchivo' => $archivoFinal->nombre_archivo,
            'nombreOriginal' => $archivoFinal->nombre_original,
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