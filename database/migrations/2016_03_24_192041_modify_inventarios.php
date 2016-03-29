<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyInventarios extends Migration {
    /**
        agregar     fechaStock
        agregar     FK  idNominaDia
        agregar     FK idNominaNoche
        quitar      horaLlegada
        renombrar   dotacionAsignada a dotacionAsignadaTotal (?)
     **/

    public function upX() {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->integer('idNominaDia')->unsigned();
            $table->foreign('idNominaDia')->references('idNomina')->on('nominas');
        });
    }
    public function downX(){
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropForeign(['idNominaDia']);
            $table->dropColumn('idNominaDia');
        });
    }

    public function up(){
        // ADVERTENCIA:
        // Como estamos haciendo forzando que idNominaDia y idNominaNoche apunten a una nomina, cualquier
        // nomina que haya sido creada antes y que no cumpla esta restriccion, hara fallar la migracion
        // SOLUCION: se deben eliminar todos los inventarios antes de correr la migracion
        
        DB::transaction(function() {
            Schema::table('inventarios', function (Blueprint $table) {
                // ## Agregar columnas
                $table->date('fechaStock')->nullable();

                // un 'iventario' puede tener / tiene 'dos nominas
                // FK: inventario de dia
                $table->integer('idNominaDia')->unsigned();
                $table->foreign('idNominaDia')->references('idNomina')->on('nominas');
                // FK: inventario de noche
                $table->integer('idNominaNoche')->unsigned();
                $table->foreign('idNominaNoche')->references('idNomina')->on('nominas');

                // ## Quitar columnas
                $table->dropColumn('horaLlegada');

                // ## Renombrar columnas
                $table->renameColumn('dotacionAsignada', 'dotacionAsignadaTotal');
            });
        });
    }
    public function down(){
        DB::transaction(function() {
            Schema::table('inventarios', function (Blueprint $table) {
                // ## Quitar columnas
                $table->dropColumn('fechaStock');
                $table->dropForeign(['idNominaDia']);
                $table->dropColumn('idNominaDia');
                $table->dropForeign(['idNominaNoche']);
                $table->dropColumn('idNominaNoche');

                // ## Agregar columnas
                $table->time('horaLlegada');

                // ## Renombrar columnas
                $table->renameColumn('dotacionAsignadaTotal', 'dotacionAsignada');
            });
        });
    }
}
