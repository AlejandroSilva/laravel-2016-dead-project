<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvinciasTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('provincias', function (Blueprint $table) {
            // PK
            $table->integer('cutProvincia')->unsigned();    // codigo unico regional
            $table->primary('cutProvincia');
            $table->unique('cutProvincia');

            // FK
            // cutRegion de la tabla Regiones
            $table->integer('cutRegion')
                  ->unsigned();
            $table->foreign('cutRegion')
                  ->references('cutRegion')
                  ->on('regiones');

            // Otros campos
            $table->string('nombre', 23);       // maximo en "San Felipe de Aconcagua" (23)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('provincias');
    }
}
