<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EliminarTablasMuestraVencimiento extends Migration {

    public function up() {
        Schema::drop('muestras_vencimiento_fcv');
        Schema::drop('archivos_muestra_vencimiento_fcv');
    }

    public function down() {
        Schema::create('archivos_muestra_vencimiento_fcv', function (Blueprint $table) {
            // PK
            $table->increments('idArchivoMuestraVencimientoFCV');
        });
        Schema::create('muestras_vencimiento_fcv', function (Blueprint $table) {
            // PK
            $table->increments('idMuestraVencimientoFCV');
        });
    }
}
