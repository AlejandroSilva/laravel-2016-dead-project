<?php namespace App\Contracts;

Interface MaestraFCVContract {

    public function agregarMaestraFCV($user, $archivo);
    public function procesarMaestraFCV($user, $archivoMaestraProductos);
    public function subirActualizacionFCV();
    public function procesarActualizacionFCV();
}