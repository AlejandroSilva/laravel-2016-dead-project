<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarCamposActa extends Migration {
    public function up() {
        Schema::table('actas_inventarios_fcv', function(Blueprint $table){
            $table->float('porcentaje_variacion_ajuste_grilla')->after('unid_absoluto_corregido_auditoria')->nullable();  // 2 decimales de precisio->nullable()
            $table->float('porcentaje_error_qf')->after('unid_absoluto_corregido_auditoria')->nullable();  // 2 decimales de precisio->nullable()
            $table->integer('total_sku_efectivos')->after('unid_absoluto_corregido_auditoria')->nullable();
        });
    }


    public function down() {
        Schema::table('actas_inventarios_fcv', function(Blueprint $table){
            $table->dropColumn('total_sku_efectivos');
            $table->dropColumn('porcentaje_error_qf');
            $table->dropColumn('porcentaje_variacion_ajuste_grilla');
        });
    }
}
