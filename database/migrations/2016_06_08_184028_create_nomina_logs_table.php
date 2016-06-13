<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNominaLogsTable extends Migration {
    public function up() {
        Schema::create('nomina_logs', function (Blueprint $table) {
            $table->increments('idNominasLog');

            // FK nomina
            $table->integer('idNomina')->unsigned();
            $table->foreign('idNomina')
                ->references('idNomina')->on('nominas')
                ->onDelete('cascade');  // si se elimina la nomina, se eliminan estos registros

            // cuerpo del mensaje
            $table->string('titulo', 50);
            $table->string('texto', 200);
            // importancia (de -128 a +128)
            $table->tinyInteger('importancia')->default(1);
            // necesitaAtencion / necesitaSolucion / necesitaCorreccion
            $table->boolean('mostrarAlerta')->default(false);

            $table->timestamps();
        });
    }
    
    public function down() {
        Schema::drop('nomina_logs');
    }
}
