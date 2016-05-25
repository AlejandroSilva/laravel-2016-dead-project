<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTableNomina extends Migration {
    public function up(){
        Schema::table('nominas_user', function(Blueprint $table) {
            // eliminar correlativo
            $table->dropColumn('correlativo');

            // agregar titular
            $table->boolean('titular')->default(true);

            // agregar Rol
            $table->integer('idRoleAsignado')->unsigned()->default(4);    // es un operador por defecto
            $table->foreign('idRoleAsignado')->references('id')->on('roles');
            
            // agregar timestamps
            $table->timestamps();
        });
    }

    public function down() {
        Schema::table('nominas_user', function(Blueprint $table) {
            // agregar correlativo
            $table->integer('correlativo');

            // eliminar titular
            $table->dropColumn('titular');

            // eliminar referencia a Rol
            $table->dropForeign('nominas_user_idrole_foreign');
            $table->dropColumn('idRoleAsignado');

            // eliminar timestamps
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
