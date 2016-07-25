<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticuloAFTable extends Migration {
    public function up() {
        Schema::create('acticulos_activo_fijo', function (Blueprint $table) {
            $table->increments('idArticuloAF');             // Primary Key
            $table->string('SKU', 32);                      // FK ProductoAF
            $table->date('fechaIncorporacion');
            $table->integer('stock')->default(0);

            // FK ProuctoAF
            $table->foreign('SKU')
                ->references('SKU')->on('productos_activo_fijo');
        });
    }
    public function down() {
        Schema::drop('acticulos_activo_fijo');
    }
}
