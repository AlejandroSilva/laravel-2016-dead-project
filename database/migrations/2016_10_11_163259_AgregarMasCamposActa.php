<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarMasCamposActa extends Migration {
    public function up() {
        Schema::table('actas_inventarios_fcv', function(Blueprint $table){
            $table->integer('supervisor_total_unidades')->after('porcentaje_variacion_ajuste_grilla')->nullable();
            $table->integer('supervisor_total_items')->after('porcentaje_variacion_ajuste_grilla')->nullable();
            $table->integer('supervisor_total_patentes')->after('porcentaje_variacion_ajuste_grilla')->nullable();

            $table->integer('apoyo2_total_unidades')->after('porcentaje_variacion_ajuste_grilla')->nullable();
            $table->integer('apoyo2_total_items')->after('porcentaje_variacion_ajuste_grilla')->nullable();
            $table->integer('apoyo2_total_patentes')->after('porcentaje_variacion_ajuste_grilla')->nullable();

            $table->integer('apoyo1_total_unidades')->after('porcentaje_variacion_ajuste_grilla')->nullable();
            $table->integer('apoyo1_total_items')->after('porcentaje_variacion_ajuste_grilla')->nullable();
            $table->integer('apoyo1_total_patentes')->after('porcentaje_variacion_ajuste_grilla')->nullable();

            $table->integer('qf_total_unidades')->after('porcentaje_variacion_ajuste_grilla')->nullable();
            $table->integer('qf_total_items')->after('porcentaje_variacion_ajuste_grilla')->nullable();
            $table->integer('qf_total_patentes')->after('porcentaje_variacion_ajuste_grilla')->nullable();
        });
    }


    public function down() {
        Schema::table('actas_inventarios_fcv', function(Blueprint $table){
            $table->dropColumn('supervisor_total_unidades');
            $table->dropColumn('supervisor_total_items');
            $table->dropColumn('supervisor_total_patentes');
            $table->dropColumn('apoyo2_total_unidades');
            $table->dropColumn('apoyo2_total_items');
            $table->dropColumn('apoyo2_total_patentes');
            $table->dropColumn('apoyo1_total_unidades');
            $table->dropColumn('apoyo1_total_items');
            $table->dropColumn('apoyo1_total_patentes');
            $table->dropColumn('qf_total_unidades');
            $table->dropColumn('qf_total_items');
            $table->dropColumn('qf_total_patentes');
        });
    }
}
