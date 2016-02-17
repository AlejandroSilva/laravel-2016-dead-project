<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJornadasTable extends Migration {
    public function up() {
        Schema::create('jornadas', function (Blueprint $table) {
            // PK
            $table->increments('idJornada');

            // Otros campos
            $table->string('nombre');
            $table->string('descripcion');
        });
    }

    public function down() {
        Schema::drop('jornadas');
    }
}
