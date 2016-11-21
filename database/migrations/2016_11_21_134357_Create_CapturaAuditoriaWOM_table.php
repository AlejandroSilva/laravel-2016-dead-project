<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapturaAuditoriaWOMTable extends Migration {

    public function up() {
        Schema::create('capturas_respuesta_wom', function (Blueprint $table) {
            // PK
            $table->increments('idCapturaAuditoriaWOM');

            // FK: un registro fue cargado desde un archivo de respuesta
            $table->integer('idArchivoRespuestaWOM')->unsigned()->nullable();

            // referencias a otras tablas
            $table->foreign('idArchivoRespuestaWOM')->references('idArchivoRespuestaWOM')->on('archivos_respuesta_wom');

            // Otros campos
            $table->integer('line');
            $table->string('ptt', 14);
            $table->integer('correlativo');
            $table->string('sku', 30);
            $table->string('serie', 30);
            $table->integer('conteoInicial');
            $table->integer('conteoFinal');
            $table->string('estado', 10);
            $table->string('codigoOrganizacion', 10);
            $table->string('nombreOrganizacion', 30);
            $table->date('fechaCaptura');
            $table->time('horaCaptura');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('capturas_respuesta_wom');
    }
}
