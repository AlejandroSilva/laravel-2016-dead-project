<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarStockRealTeoricoToInventariosTable extends Migration {

    public function up(){
        Schema::table('inventarios', function(Blueprint $table){
            $table->integer('unidadesReal');
            $table->integer('unidadesTeorico');
            $table->date('fechaToma');
        });
    }

    public function down() {
        Schema::table('inventarios', function(Blueprint $table){
            $table->dropColumn('unidadesReal');
            $table->dropColumn('unidadesTeorico');
            $table->dropColumn('fechaToma');
        });
    }
}
