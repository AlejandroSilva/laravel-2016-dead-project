<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoMaestrasTable extends Migration {

    public function up() {
        Schema::create('productos_maestra', function (Blueprint $table) {
            // PK compuesta
            $table->bigIncrements('idProductoMaestra'); // PK
            $table->string('SKU', 32);                  // unique
            $table->integer('idCliente')->unsigned();   // unique + FK
            $table->string('descripcion', 60);
            $table->string('barra1', 32);
            $table->string('barra2', 32);
            $table->string('barra3', 32);

            // el par SKU-CLIENTE debe ser unico (se puede repetir el sku pero no en el mismo cliente)
            $table->unique(['SKU', 'idCliente']);

            // FK idCliente
            $table->foreign('idCliente')
                ->references('idCliente')->on('clientes');
        });
    }

    public function down() {
        Schema::drop('productos_maestra');
    }
}
