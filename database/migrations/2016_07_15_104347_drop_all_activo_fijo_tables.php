<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAllActivoFijoTables extends Migration {
    public function up() {
        // eliminar varias tablas
        Schema::drop('preguia_articulo');
        Schema::drop('preguias_despacho');
        Schema::drop('codigos_barra');
        Schema::drop('acticulos_activo_fijo');

    }

    public function down() {
        // volver a crear las tablas, sin datos....
        Schema::create('preguia_articulo', function (Blueprint $table) {
            $table->increments('id');
        });
        Schema::create('preguias_despacho', function (Blueprint $table) {
            $table->increments('id');
        });
        Schema::create('codigos_barra', function (Blueprint $table) {
            $table->increments('id');
        });
        Schema::create('acticulos_activo_fijo', function (Blueprint $table) {
            $table->increments('id');
        });
    }
}
