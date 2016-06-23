<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosActivoFijoTable extends Migration {
    public function up() {
        Schema::create('productos_activo_fijo', function (Blueprint $table) {
            $table->bigIncrements('idProductoAF');                  // Primary Key
            $table->bigInteger('idProductoMaestra')->unsigned();    // FK
            $table->integer('idAlmacenAF')->unsigned();             // FK
            $table->integer('precio')->default(0);

            // FK Producto Maestra
            $table->foreign('idProductoMaestra')
                ->references('idProductoMaestra')->on('productos_maestra');
            
            // FK Almacen Activo Fijo
            $table->foreign('idAlmacenAF')
                ->references('idAlmacenAF')->on('almacenes_activo_fijo');
        });
    }
    
    public function down() {
        Schema::drop('productos_activo_fijo');
    }
}
