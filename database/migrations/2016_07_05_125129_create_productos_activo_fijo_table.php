<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosActivoFijoTable extends Migration {
    public function up() {
        Schema::create('productos_activo_fijo', function (Blueprint $table) {
            $table->string('SKU', 32);
            $table->string('descripcion', 60);
            $table->integer('valorMercado')->default(0);

            // PK SKU
            $table->primary('SKU');
        });
    }
    
    public function down() {
        Schema::drop('productos_activo_fijo');
    }
}
