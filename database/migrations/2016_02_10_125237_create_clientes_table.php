<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('clientes', function (Blueprint $table) {
            // PK
            $table->increments('idCliente');

            // Otros campos
            $table->string('nombre', 50);
            $table->string('nombreCorto', 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('clientes');
    }
}
