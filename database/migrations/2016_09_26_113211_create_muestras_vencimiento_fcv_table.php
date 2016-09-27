<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMuestrasVencimientoFcvTable extends Migration {
    public function up() {
        Schema::create('muestras_vencimiento_fcv', function (Blueprint $table) {
            // PK
            $table->increments('idMuestraVencimientoFCV');

            // FK: un archivo es 'enviado por' un usuairo
            $table->integer('idArchivoMuestraVencimientoFCV')->unsigned()->nullable();

            // referencias a otras tablas
            $table->foreign('idArchivoMuestraVencimientoFCV')
                ->references('idArchivoMuestraVencimientoFCV')
                ->on('archivos_muestra_vencimiento_fcv');

            // Otros campos
            $table->integer('ceco');
            $table->string('codigo_producto', 20);              // el campo mas largo encontrado fueron 6 campos
            $table->string('descriptor', 50);                   // el campo mas largo encontrado son 40 caracteres
            //$table->string('barra', 40);                        // el campo mas largo encontrado son 22 caracteres
            //$table->string('laboratorio', 50);                  // el campo mas largo encontrado son 38 caracteres
            //$table->string('clasificacion_terapeutica', 50);    // el campo mas largo encontrado son 38 caracteres
            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('muestras_vencimiento_fcv');
    }
}
