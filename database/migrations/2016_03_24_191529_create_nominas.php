<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNominas extends Migration {
    
    public function up(){
        DB::transaction(function() {
            Schema::create('nominas', function (Blueprint $table) {
                // ### PK
                $table->increments('idNomina');

                // ### FK (el personal asignado puede ser null)
                // Lider
                $table->integer('idLider')->unsigned()->nullable();
                $table->foreign('idLider')->references('id')->on('users');
                // Supervisor
                $table->integer('idSupervisor')->unsigned()->nullable();
                $table->foreign('idSupervisor')->references('id')->on('users');
                // Captador 1
                $table->integer('idCaptador1')->unsigned()->nullable();
                $table->foreign('idCaptador1')->references('id')->on('users');
                // Captador 2
                $table->integer('idCaptador2')->unsigned()->nullable();
                $table->foreign('idCaptador2')->references('id')->on('users');

                // ### Otros campos
                // Hora presentación
                $table->time('horaPresentacionLider');
                $table->time('horaPresentacionEquipo');
                $table->integer('dotacionAsignada')->default(0);
                $table->integer('dotacionCaptador1')->default(0);
                $table->integer('dotacionCaptador2')->default(0);
                $table->time('horaTermino')->nullable();                // probablemente no se ocupe
                $table->time('horaTerminoConteo')->nullable();          // probablemente no se ocupe
            });
        });
    }
    
    public function down(){
        Schema::dropIfExists('nominas');
    }
}
