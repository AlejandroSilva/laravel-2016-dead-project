<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreguiaArticuloTable extends Migration {
    public function up() {
        Schema::create('preguia_articulo', function (Blueprint $table) {
            $table->integer('idPreguia')->unsigned();       // PK-FK Preguia
            $table->integer('idArticuloAF')->unsigned();    // PK-FK Articulos
            $table->integer('stockEntregado')->default(0);
            $table->integer('stockRetornado')->default(0);

            // PK compuesta
            $table->primary(['idPreguia', 'idArticuloAF']);
            // FK Preguia
            $table->foreign('idPreguia')
                ->references('idPreguia')->on('preguias_despacho');
            // FK Articulo
            $table->foreign('idArticuloAF')
                ->references('idArticuloAF')->on('acticulos_activo_fijo');
        });
    }
    public function down() {
        Schema::drop('preguia_articulo');
    }
}
