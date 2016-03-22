<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration {

    public function up() {

        Schema::create('clientes', function (Blueprint $table) {
            // PK
            $table->increments('idCliente');

            // Otros campos
            $table->string('nombre', 50)->unique();
            $table->string('nombreCorto', 10)->unique();
        });
    }

    public function down() {
        Schema::drop('clientes');
    }
}
