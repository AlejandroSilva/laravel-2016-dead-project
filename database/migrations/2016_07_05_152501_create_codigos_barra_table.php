<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodigosBarraTableNOPE extends Migration {

    public function up() {
        Schema::create('codigos_barra', function (Blueprint $table) {
            $table->string('barra', 32);            // Primary Key
            $table->string('codArticuloAF', 32);    // FK ArticuloAF

            // Primary
            $table->primary('barra');
            // FK ArticuloAF
            $table->foreign('codArticuloAF')
                ->references('codArticuloAF')->on('acticulos_activo_fijo');
        });
    }
    public function down() {
        Schema::drop('codigos_barra');
    }
}
