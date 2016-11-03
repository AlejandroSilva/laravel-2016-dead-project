<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdCaptadorToNominasUserTable extends Migration {

    public function up(){
        Schema::table('nominas_user', function(Blueprint $table) {
            // agregar captador
            $table->integer('idCaptador')->unsigned()->default(1)                   // por defecto asignamos a "SEI" como captador
                ->after('idRoleAsignado');
            $table->foreign('idCaptador')->references('id')->on('users');
        });
    }

    public function down() {
        Schema::table('nominas_user', function(Blueprint $table) {
            // eliminar referencia a captador
            $table->dropForeign('nominas_user_idcaptador_foreign');
            $table->dropColumn('idCaptador');
        });
    }
}
