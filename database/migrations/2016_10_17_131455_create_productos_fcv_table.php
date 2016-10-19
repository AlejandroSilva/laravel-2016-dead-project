<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosFcvTable extends Migration {
    public function up() {
        Schema::create('productos_fcv', function (Blueprint $table) {
            // PK
            $table->increments('idProductoFCV');

            // FK
            $table->integer('idArchivoMaestra')->unsigned()->nullable();
            // Referencias
            $table->foreign('idArchivoMaestra')->references('idArchivoMaestra')->on('archivos_maestra_productos');

            // Otros campos
            $table->string('sku', 35);                      // El campo mas largo fue de 15 caracteres
            $table->string('barra', 27);                    // El mayor campo fue de 7
            $table->string('descriptor', 80);               // El mayor campo fue de 60
            $table->string('laboratorio', 60);              // El campo mas largo fue de 40 caracteres
            $table->string('clasificacionTerapeutica', 60); // La clasificacion tiene demasiado "espacios muertos"
            $table->timestamps();
        });
    }
    
    public function down(){
        Schema::drop('productos_fcv');
    }
}
