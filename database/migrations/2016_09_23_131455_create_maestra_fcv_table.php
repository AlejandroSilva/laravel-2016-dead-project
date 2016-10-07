<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaestraFcvTable extends Migration
{
    public function up()
    {
        Schema::create('maestra_fcv', function (Blueprint $table) {
            // PK
            $table->increments('idMaestraFCV');
            // FK
            $table->integer('idArchivoMaestra')->unsigned()->nullable();
            // Referencias
            $table->foreign('idArchivoMaestra')->references('idArchivoMaestra')->on('archivo_maestra_fcv');
            // Otros campos
            $table->string('barra', 27); //El mayor campo fue de 7
            $table->string('descriptor', 80); // El mayor campo fue de 60
            $table->string('sku', 35); // El campo mas largo fue de 15 caracteres
            $table->string('laboratorio', 60); //El campo mas largo fue de 40 caracteres
            $table->string('clasificacionTerapeutica', 60);
            $table->timestamps();
        });
    }
    
    public function down(){
        Schema::drop('maestra_fcv');
    }
}
