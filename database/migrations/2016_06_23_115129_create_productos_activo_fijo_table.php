<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosActivoFijoTable extends Migration {
    public function up() {
        Schema::create('productos_activo_fijo', function (Blueprint $table) {
            $table->bigIncrements('idProductoAF');                  // Primary Key
            $table->string('codActivoFijo', 32);            // Primary Key
            $table->integer('idAlmacenAF')->unsigned();     // FK AlmacenAF
            $table->integer('idLocal')->unsigned();         // FK Local
            $table->string('descripcion', 60);
            $table->integer('precio')->default(0);
            $table->string('barra1', 32);
            $table->string('barra2', 32)->nullable();
            $table->string('barra3', 32)->nullable();
            
            // CODIGO unico por cada LOCAL
            $table->unique(['codActivoFijo', 'idLocal']);
            
            // FK Almacen Activo Fijo
            $table->foreign('idAlmacenAF')
                ->references('idAlmacenAF')->on('almacenes_activo_fijo');
            // FK Local
            $table->foreign('idLocal')
                ->references('idLocal')->on('locales');
        });
    }
    
    public function down() {
        Schema::drop('productos_activo_fijo');
    }
}
