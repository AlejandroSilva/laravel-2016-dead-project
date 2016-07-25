<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodigosBarraTable extends Migration {
    public function up() {
        Schema::create('codigos_barra', function (Blueprint $table) {
            $table->string('barra', 32);                        // Primary Key
            $table->integer('idArticuloAF')->unsigned();    // FK ArticuloAF

            // Primary
            $table->primary('barra');
            // FK ArticuloAF
            $table->foreign('idArticuloAF')
                ->references('idArticuloAF')->on('acticulos_activo_fijo');
        });
    }

    public function down() {
        Schema::drop('codigos_barra');
    }
}
