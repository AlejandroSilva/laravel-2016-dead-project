<?php

use Illuminate\Database\Seeder;
use App\EstadoNominas;

class EstadosNominaSeeder extends Seeder {

    public function run() {
        // Crear los estados por defecto
        EstadoNominas::create(['idEstadoNomina' => 1, 'nombre' => 'Deshabilitada']);
        EstadoNominas::create(['idEstadoNomina' => 2, 'nombre' => 'Pendiente']);
        EstadoNominas::create(['idEstadoNomina' => 3, 'nombre' => 'Recibida']);
        EstadoNominas::create(['idEstadoNomina' => 4, 'nombre' => 'Aprobada']);
        EstadoNominas::create(['idEstadoNomina' => 5, 'nombre' => 'Informada']);
    }
}
