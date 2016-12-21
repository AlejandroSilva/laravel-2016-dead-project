<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorreosClientesTable extends Migration {
    public function up() {
        Schema::create('correos_clientes', function (Blueprint $table) {
            // PK
            $table->increments('idCorreo');
            // FK
            $table->integer('idCliente')
                ->unsigned();
            $table->foreign('idCliente')
                ->references('idCliente')
                ->on('clientes');
            $table->string('correo', 20);
        });
    }
    public function down() {
        Schema::drop('correos_clientes');
    }
}
