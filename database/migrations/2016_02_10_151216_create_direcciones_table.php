<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDireccionesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('direcciones', function (Blueprint $table) {
            // PK
            $table->increments('idDireccion');

            // FK
            // cutComuna de la tabla Comunas
            $table->integer('cutComuna')
                   ->unsigned();
            $table->foreign('cutComuna')
                ->references('cutComuna')
                ->on('comunas');

            // Otros campos
            $table->string('direccion', 150);
            $table->string('referencia', 150)->default('');
            $table->string('gmapShortUrl', 40)->nullable();     // Ejemplo: https://goo.gl/maps/g4NBaThDwWQ2 (32 largo)
            $table->text('gmapIframeUrl')->nullable();      // Ejemplo: https://www.google.com/maps/embed?pb=!1m14!1m8!.....+Metropolitana!5e0!3m2!...4v1455118804482 (282 largo)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('direcciones');
    }
}
