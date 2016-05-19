<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstadoNominaTable extends Migration {
    public function up() {
        Schema::create('estado_nominas', function (Blueprint $table) {
            // PK
            $table->increments('idEstadoNomina');
            $table->string('nombre', 40)->unique();
            $table->text('descripcion');
        });

        Artisan::call('db:seed', array('--class' => 'EstadosNominaSeeder'));
    }

    public function down(){
        Schema::drop('estado_nominas');
    }
}