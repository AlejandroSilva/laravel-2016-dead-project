<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubgeoTable extends Migration {
    public function up() {
        Schema::create('subgeos', function (Blueprint $table) {
            // PK
            $table->increments('idSubgeo');

            // FK
            // idZona de la tabla Geos
            $table->integer('idGeo')
                ->unsigned();
            $table->foreign('idGeo')
                ->references('idGeo')
                ->on('geos');

            // Otros campos
            $table->string('nombre', 40)->unique();
            $table->integer('min');
            $table->integer('max');
        });
    }

    public function down() {
        Schema::drop('subgeos');
    }
}
