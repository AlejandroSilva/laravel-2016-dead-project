<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActasInventariosFcvTable extends Migration {

    public function up() {
        Schema::create('actas_inventarios_fcv', function (Blueprint $table) {
            // PK
            $table->increments('idActaFCV');

            // FK: un 'inventario' pertenece a un 'local'
            $table->integer('idInventario')->unsigned();

            // referencias a otras tablas
            $table->foreign('idInventario')->references('idInventario')->on('inventarios');

            // Otros campos
            $table->dateTime('fecha_publicacion');
            $table->text('ceco_local');
            $table->date('fecha_inventario');
            $table->text('cliente');
            $table->text('rut');
            $table->text('supervisor');
            $table->text('quimico_farmaceutico');
            $table->text('nota_presentacion');
            $table->text('nota_supervisor');
            $table->text('nota_conteo');
            $table->dateTime('inicio_conteo');
            $table->dateTime('fin_conteo');
            $table->text('fin_revisión');
            $table->text('horas_trabajadas');
            $table->text('dotacion_presupuestada');
            $table->text('dotacion_efectivo');
            $table->text('unidades_inventariadas');
            $table->text('unidades_teoricas');
            $table->text('unidades_ajustadas');
            $table->text('ptt_total_inventariadas');
            $table->text('ptt_revisadas_totales');
            $table->text('ptt_revisadas_qf');
            $table->text('ptt_revisadas_apoyo_cv_1');
            $table->text('ptt_revisadas_apoyo_cv_2');
            $table->text('ptt_revisadas_supervisores_fcv');
            $table->text('item_total_inventariados');
            $table->text('item_revisados');
            $table->text('item_revisados_qf');
            $table->text('item_revisados_apoyo_cv_1');
            $table->text('item_revisados_apoyo_cv_2');
            $table->text('unidades_corregidas_revision_previo_ajuste');
            $table->text('unidades_corregidas');
            $table->text('total_item');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('actas_inventarios_fcv');
    }
}
