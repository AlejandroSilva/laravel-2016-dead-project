<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstadoNominaPagoToNominasTable extends Migration {

    public function up() {
        Schema::table('nominas', function(Blueprint $table){
            $table->text('urlNominaPago')->default(null);
        });
    }

    public function down() {
        Schema::table('nominas', function(Blueprint $table){
            $table->dropColumn('urlNominaPago');
        });
    }
}
