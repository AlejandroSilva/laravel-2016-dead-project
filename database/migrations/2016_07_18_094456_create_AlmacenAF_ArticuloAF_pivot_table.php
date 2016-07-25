<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlmacenAFArticuloAFPivotTable extends Migration {
    public function up() {
        Schema::create('almacenAF_articuloAF', function (Blueprint $table) {
            $table->integer('idAlmacenAF')->unsigned();     // PK-FK Preguia
            $table->integer('idArticuloAF')->unsigned();    // PK-FK Articulos
            $table->integer('stockActual')->default(0);

            // PK compuesta
            $table->primary(['idAlmacenAF', 'idArticuloAF']);

            // FK Almacen
            $table->foreign('idAlmacenAF')
                ->references('idAlmacenAF')->on('almacenes_activo_fijo');
            // FK Articulo
            $table->foreign('idArticuloAF')
                ->references('idArticuloAF')->on('acticulos_activo_fijo');
        });
    }
    public function down() {
        Schema::drop('almacenAF_articuloAF');
    }
}