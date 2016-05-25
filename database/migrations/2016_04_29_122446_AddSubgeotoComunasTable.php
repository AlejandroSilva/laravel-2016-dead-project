<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// Modelos
use App\Comunas;

class AddSubgeotoComunasTable extends Migration {
    
    public function up() {
        // Agregar el campo idSubgeo
        Schema::table('comunas', function(Blueprint $table){
            $table->integer('idSubgeo1')->unsigned()->nullable();
            $table->integer('idSubgeo2')->unsigned()->nullable();
            $table->integer('idSubgeo3')->unsigned()->nullable();

            $table->foreign('idSubgeo1')->references('idSubgeo')->on('subgeos');
            $table->foreign('idSubgeo2')->references('idSubgeo')->on('subgeos');
            $table->foreign('idSubgeo3')->references('idSubgeo')->on('subgeos');
        });
    }
    
    public function down() {
        Schema::table('comunas', function(Blueprint $table) {
            $table->dropForeign('comunas_idsubgeo1_foreign');
            $table->dropForeign('comunas_idsubgeo2_foreign');
            $table->dropForeign('comunas_idsubgeo3_foreign');

            $table->dropColumn('idSubgeo1');
            $table->dropColumn('idSubgeo2');
            $table->dropColumn('idSubgeo3');
        });
    }
}
