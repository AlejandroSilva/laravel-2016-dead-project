<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNominaUser extends Migration {
    public function up() {
        // Create table for associating roles to users (Many-to-Many)
        Schema::create('nominas_user', function (Blueprint $table) {
            // PK Compuesta
            $table->integer('idUser')->unsigned();
            $table->integer('idNomina')->unsigned();
            // referencias a otras tablas
            $table->foreign('idUser')->references('id')->on('users');
            $table->foreign('idNomina')->references('idNomina')->on('nominas');
            // primary compuesta
            $table->primary(['idUser', 'idNomina']);

            // Otros campos
            $table->integer('correlativo');
        });
    }

    public function down() {
        Schema::drop('nominas_user');
    }
}
