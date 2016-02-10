<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComunasTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comunas', function (Blueprint $table) {
            // PK
            $table->integer('cutComuna');    // codigo unico regional
            $table->primary('cutComuna');
            $table->unique('cutComuna');

            // FK
            // cutProvincia de la tabla Provincias
            $table->integer('cutProvincia');
            $table->foreign('cutProvincia')
                ->references('cutProvincia')
                ->on('provincias');

            // Otros campos
            $table->string('nombre', 23);       // maximo en "Cabo de Hornos (Ex - Navarino)" (31)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('comunas');
    }
}
