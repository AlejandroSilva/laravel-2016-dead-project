<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('regiones', function (Blueprint $table) {
            // PK
            $table->integer('cutRegion');   // codigo unico regional
            $table->primary('cutRegion');
            $table->unique('cutRegion');

            // FK
            // idZona de la tabla Zonas
            $table->integer('idZona')
                  ->unsigned();
            $table->foreign('idZona')
                ->references('idZona')
                ->on('zonas');

            // Otros campos
            $table->string('nombre', 55);   // maximo en "Región de Aysén del General Carlos Ibáñez del Campo" (52)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('regiones');
    }
}
