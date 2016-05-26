<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHabilitadaToNominasTable extends Migration{
    public function up() {
        Schema::table('nominas', function(Blueprint $table){
            // se agrega una nueva columna para conocer el estado de la nomina
            // este campo depende del "idJornada" del inventario al que pertenece
            $table->boolean('habilitada')->default(true);

            $table->dropColumn('horaTermino');
            $table->dropColumn('horaTerminoConteo');
        });

        // luego de crear el campo, se recorren todos los inventarios, y se evalua si sus nominas estan o no habilitadas
        $inventarios = \App\Inventarios::all();
        $inventarios->each(function($inventario){
            $idJornada = $inventario->idJornada;
            // la nomina de dia esta habilitada si la jornada es "dia"(2) o "dia y noche"(4)
            $inventario->nominaDia->habilitada = ($idJornada==2 || $idJornada==4);
            $inventario->nominaDia->save();
            // la nominaNoche esta habilitada si la jornada es "noche"(3) o "dia y noche"(4)
            $inventario->nominaNoche->habilitada = ($idJornada==3 || $idJornada==4);
            $inventario->nominaNoche->save();
        });
    }

    public function down() {
        Schema::table('nominas', function(Blueprint $table){
            $table->dropColumn('habilitada');
            
            // restaura las columnas eliminadas
            $table->time('horaTermino')->nullable();                // probablemente no se ocupe
            $table->time('horaTerminoConteo')->nullable();          // probablemente no se ocupe
        });
    }
}
