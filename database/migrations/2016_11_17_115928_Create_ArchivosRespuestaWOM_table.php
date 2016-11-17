<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivosRespuestaWOMTable extends Migration {
    public function up() {
        Schema::create('archivos_respuesta_wom', function (Blueprint $table) {
            // PK
            $table->increments('idArchivoRespuestaWOM');

            // FK: un archivo es 'enviado por' un usuario
            $table->integer('idSubidoPor')->unsigned();
            // FK: en algun momento hay que relacionar el archivo con una auditoria programada, por ahora no se ocupa
            $table->integer('idAuditoria')->unsigned()->nullable();

            // referencias a otras tablas
            $table->foreign('idSubidoPor')->references('id')->on('users');

            // Otros campos
            $table->text('nombreArchivo');
            $table->text('nombreOriginal');
            $table->boolean('archivoValido')->default(false);
            $table->text('resultado');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('archivos_respuesta_wom');
    }
}
