<?php namespace App\Contracts;

Interface MaestraWOMContract {
    // Maestra
    public function agregarMaestraWOM($user, $archivo);

    // segun marco la maestra esta correcta... no se hace ningun tipo de validacion...
    //public function procesarMaestraFCV($user, $archivoMaestraProductos);
    //public function validarProductosFCV($user, $archivoMaestraProductos);
    //public function descargarMaestraDesdeDB($user, $archivoMaestraProductos);
}