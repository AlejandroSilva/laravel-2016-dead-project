<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFechaLimiteToNominas extends Migration {
    public function up() {
        Schema::table('nominas', function(Blueprint $table){
            $table->date('fechaLimiteCaptador')->default(null);
        });
    }

    public function down() {
        Schema::table('nominas', function(Blueprint $table){
            $table->dropColumn('fechaLimiteCaptador');
        });
    }
}
