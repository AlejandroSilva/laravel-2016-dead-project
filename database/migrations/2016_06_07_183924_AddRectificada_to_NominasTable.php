<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRectificadaToNominasTable extends Migration {
    public function up() {
        Schema::table('nominas', function (Blueprint $table) {
            // agregar campo 'rectificada'
            $table->boolean('rectificada')->default(false);

            // eliminar campo no utilizado
            $table->dropColumn('__dotacionAsignada__');
        });
    }


    public function down() {
        Schema::table('nominas', function (Blueprint $table) {
            // eliminar campo 'rectificada'
            $table->dropColumn('rectificada');

            // restaurar la columna '__dotacionAsignada__' (enrealidad no se ocupa)
            $table->integer('__dotacionAsignada__');
        });
    }
}
