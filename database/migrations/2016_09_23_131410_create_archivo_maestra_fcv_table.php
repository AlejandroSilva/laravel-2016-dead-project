<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivoMaestraFcvTable extends Migration
{
    public function up(){
        Schema::create('archivo_maestra_fcv', function (Blueprint $table) {
            // PK    
            $table->increments('idArchivoMaestra');
            // FK
            $table->integer('idUsuarioSubida')->unsigned()->nullable();
            // Referencias
            $table->foreign('idUsuarioSubida')->references('id')->on('users');
            // Otros campos
            $table->text('nombreArchivo');
            $table->text('nombreOriginal');
            $table->text('resultado');
            $table->timestamps();
        });
    }
    
    public function down(){
        Schema::drop('archivo_maestra_fcv');
    }
}
