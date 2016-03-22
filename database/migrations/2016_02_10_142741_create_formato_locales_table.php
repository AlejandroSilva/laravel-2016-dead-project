<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormatoLocalesTable extends Migration {
    public function up() {
        Schema::create('formato_locales', function (Blueprint $table) {
            // PK
            $table->increments('idFormatoLocal');

            // Otros campos
            $table->string('nombre', 40);
            $table->string('siglas', 10);
            $table->integer('produccionSugerida');
            $table->text('descripcion');
        });
    }

    public function down(){
        Schema::drop('formato_locales');
    }
}
