<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticulosActivoFijoTable extends Migration {
    public function up() {
        Schema::create('acticulos_activo_fijo', function (Blueprint $table) {
            $table->string('codArticuloAF', 32);            // Primary Key
            $table->string('SKU', 32);                      // FK ProductoAF
            $table->integer('idAlmacenAF')->unsigned();     // FK AlmacenAF
            $table->date('fechaIncorporacion');

            // Primary
            $table->primary('codArticuloAF');
            // FK AlmacenAF
            $table->foreign('idAlmacenAF')
                ->references('idAlmacenAF')->on('almacenes_activo_fijo');
            // FK ProuctoAF
            $table->foreign('SKU')
                ->references('SKU')->on('productos_activo_fijo');
        });
    }

    public function down() {
        Schema::drop('acticulos_activo_fijo');
    }
}
