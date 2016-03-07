<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventariosTable extends Migration {

    public function up() {
        Schema::create('inventarios', function (Blueprint $table) {
            // PK
            $table->increments('idInventario');

            // FK
            // un 'inventario' pertenece a un 'local'
            $table->integer('idLocal')
                  ->unsigned();
            $table->foreign('idLocal')
                  ->references('idLocal')
                  ->on('locales');
            // un 'inventario' tiene una 'jornada' asignada
            $table->integer('idJornada')
                ->unsigned();
            $table->foreign('idJornada')
                ->references('idJornada')
                ->on('jornadas');

            // Otros campos
            $table->date('fechaProgramada');
            $table->time('horaLlegada');
            $table->integer('stockTeorico');
            $table->integer('dotacionAsignada');

            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('inventarios');
    }
}
