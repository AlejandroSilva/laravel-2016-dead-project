<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlmacenesActivoFijoTable extends Migration {
    public function up() {
        Schema::create('almacenes_activo_fijo', function (Blueprint $table) {
            $table->increments('idAlmacenAF');                      // Primary Key
            $table->integer('idLocal')->unsigned();                 // FK
            $table->integer('idUsuarioResponsable')->unsigned();    // FK
            $table->string('descripcion', 60);

            // FK Usuario Responsable
            $table->foreign('idUsuarioResponsable')
                ->references('id')->on('users');
            // FK Local
            $table->foreign('idLocal')
                ->references('idLocal')->on('locales');
        });
    }

    public function down() {
        Schema::drop('almacenes_activo_fijo');
    }
}
