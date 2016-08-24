<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreguiaDespachosTableNOPE extends Migration {
    public function up() {
        Schema::create('preguias_despacho', function (Blueprint $table) {
            $table->increments('idPreguia');                    // Primary Key
            $table->integer('idAlmacenOrigen')->unsigned();     // FK AlmacenAF
            $table->integer('idAlmacenDestino')->unsigned();    // FK AlmacenAF
            $table->text('descripcion');

            $table->date('fechaEmision');
            $table->timestamps();
            
            // FK Almacen Activo Fijo
            $table->foreign('idAlmacenOrigen')
                ->references('idAlmacenAF')->on('almacenes_activo_fijo');
            $table->foreign('idAlmacenDestino')
                ->references('idAlmacenAF')->on('almacenes_activo_fijo');
        });
    }

    public function down() {
        Schema::drop('preguias_despacho');
    }
}
