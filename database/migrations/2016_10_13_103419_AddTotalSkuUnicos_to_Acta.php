<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalSkuUnicosToActa extends Migration {
    public function up() {
        Schema::table('actas_inventarios_fcv', function(Blueprint $table){
            $table->integer('sku_unicos_inventariados')->after('supervisor_total_unidades')->nullable();
        });
    }


    public function down() {
        Schema::table('actas_inventarios_fcv', function(Blueprint $table){
            $table->dropColumn('sku_unicos_inventariados');
        });
    }
}
