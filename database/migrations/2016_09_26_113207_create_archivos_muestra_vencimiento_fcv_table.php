<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivosMuestraVencimientoFcvTable extends Migration {
    public function up() {
        Schema::create('archivos_muestra_vencimiento_fcv', function (Blueprint $table) {
            // PK
            $table->increments('idArchivoMuestraVencimientoFCV');

            // FK: un archivo es 'enviado por' un usuairo
            $table->integer('idSubidoPor')->unsigned()->nullable();

            // referencias a otras tablas
            $table->foreign('idSubidoPor')->references('id')->on('users');

            // Otros campos
            $table->text('nombre_archivo');
            $table->text('nombre_original');
            $table->boolean('muestraValida')->default(false);
            $table->text('resultado');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('archivos_muestra_vencimiento_fcv');
    }
}
