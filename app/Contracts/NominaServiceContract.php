<?php namespace App\Contracts;

Interface NominaServiceContract {
    // Captador
    public function agregarCaptador($user, $nomina, $captador);
    public function quitarCaptador($user, $nomina, $captador);
    public function cambiarAsignadosDeCaptador($user, $nomina, $captador, $asignados);
    // Lider
    public function agregarLider($user, $nomina, $lider);
    public function quitarLider($user, $nomina);
    // Supervisor
    public function agregarSupervisor($user, $nomina, $supervisor);
    public function quitarSupervisor($user, $nomina);
    // Operadores
    public function agregarOperador($user, $nomina, $operador, $titular, $idCaptadorAsignado);
    public function quitarOperador($user, $nomina, $operador);
}