<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivoMaestraProductosTable extends Migration {
    public function up(){
        Schema::create('archivos_maestra_productos', function (Blueprint $table) {
            // PK    
            $table->increments('idArchivoMaestra');

            // FK
            $table->integer('idCliente')->unsigned();
            $table->integer('idSubidoPor')->unsigned()->nullable();

            // Referencias
            $table->foreign('idCliente')->references('idCliente')->on('clientes');
            $table->foreign('idSubidoPor')->references('id')->on('users');

            // Otros campos
            $table->text('nombreArchivo');
            $table->text('nombreOriginal');
            $table->boolean('maestraValida')->default(false);
            $table->text('resultado');
            $table->timestamps();
        });
    }
    
    public function down(){
        Schema::drop('archivos_maestra_productos');
    }
}
