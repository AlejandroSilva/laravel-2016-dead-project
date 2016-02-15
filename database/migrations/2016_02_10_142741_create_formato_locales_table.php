<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormatoLocalesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('formato_locales', function (Blueprint $table) {
            // PK
            $table->increments('idFormatoLocal');

            // Otros campos
            $table->string('nombre', 40);
            $table->string('siglas', 10);
            $table->text('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('formato_locales');
    }
}
