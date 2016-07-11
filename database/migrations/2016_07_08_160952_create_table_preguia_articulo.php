<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePreguiaArticulo extends Migration {
    public function up() {
        Schema::create('preguia_articulo', function (Blueprint $table) {
            $table->integer('idPreguia')->unsigned();       // PK-FK Preguia
            $table->string('codArticuloAF', 32);            // PK-FK Articulos
            $table->integer('estado')->default(1);

            // PK compuesta
            $table->primary(['idPreguia', 'codArticuloAF']);

            // FK preguia
            $table->foreign('idPreguia')
                ->references('idPreguia')->on('preguias_despacho');
            $table->foreign('codArticuloAF')
                ->references('codArticuloAF')->on('acticulos_activo_fijo');
        });
    }


    public function down() {
        Schema::drop('preguia_articulo');
    }
}
