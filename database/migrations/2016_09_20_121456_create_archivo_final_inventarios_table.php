<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivoFinalInventariosTable extends Migration {
    public function up() {
        Schema::create('archivos_finales_inventarios', function (Blueprint $table) {
            // PK
            $table->increments('idArchivoFinalInventario');

            // FK: un 'archivo final de inventario' pertenece a un 'inventario'
            $table->integer('idInventario')->unsigned();
            // FK: un archivo es 'enviado por' un usuairo
            $table->integer('idSubidoPor')->unsigned()->nullable();

            // referencias a otras tablas
            $table->foreign('idInventario')->references('idInventario')->on('inventarios');
            $table->foreign('idSubidoPor')->references('id')->on('users');

            // Otros campos
            $table->text('nombre_archivo');
            $table->text('nombre_original');
            $table->text('resultado');

            $table->date('fecha_publicacion');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('archivos_finales_inventarios');
    }
}
