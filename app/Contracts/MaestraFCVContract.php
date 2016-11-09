<?php namespace App\Contracts;

Interface MaestraFCVContract {
    // Maestra
    public function agregarMaestraFCV($user, $archivo);
    public function procesarMaestraFCV($user, $archivoMaestraProductos);
    public function validarProductosFCV($user, $archivoMaestraProductos);
    public function descargarMaestraDesdeDB($user, $archivoMaestraProductos);

    // Actualizaciones
    public function subirActualizacionFCV();
    public function procesarActualizacionFCV();
}