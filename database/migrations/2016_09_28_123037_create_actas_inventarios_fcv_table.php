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
            // FK: un 'acta' tiene informacion extraida de un 'archivoFinalInventario'
            $table->integer('idArchivoFinalInventario')->unsigned()->nullable();
            // FK: un archivo es 'publicado por' un usuairo
            $table->integer('idPublicadoPor')->unsigned()->nullable();

            // referencias a otras tablas
            $table->foreign('idInventario')->references('idInventario')->on('inventarios');
            $table->foreign('idArchivoFinalInventario')->references('idArchivoFinalInventario')->on('archivos_finales_inventarios');
            $table->foreign('idPublicadoPor')->references('id')->on('users');

            // Otros campos
            $table->dateTime('fecha_publicacion');  // la acta ha sido publicada o no?
            // datos del archivo txt
            $table->integer('presupuesto');
            $table->integer('efectiva');
            $table->time('hora_llegada');
            $table->string('administrador', 60);
            $table->integer('porcentaje');
            $table->dateTime('captura_uno');
            $table->dateTime('emision_cero');
            $table->dateTime('emision_variance');
            $table->dateTime('inicio_sumary');
            $table->dateTime('fin_captura');
            $table->integer('unidades');
            $table->integer('teorico_unidades');
            $table->date('fecha_toma');
            $table->integer('cod_local');
            $table->string('nombre_empresa', 10);
            $table->string('usuario', 60);
            $table->integer('nota1');
            $table->integer('nota2');
            $table->integer('nota3');
            $table->integer('aud1');
            $table->integer('aud2');
            $table->integer('aud3');
            $table->integer('aud4');
            $table->integer('aud5');
            $table->integer('aud6');
            $table->integer('aju1');
            $table->integer('aju2');
            $table->integer('aju3');
            $table->float('aju4');  // 2 decimales de precision
            $table->dateTime('tot1');
            $table->integer('tot2');
            $table->integer('tot3');
            $table->integer('tot4');
            $table->integer('check1');
            $table->integer('check2');
            $table->integer('check3');
            $table->integer('check4');
            // version "nueva"
            // RU01item;1122 (hasta RU24)
            // RU01uni;2554 (hasta RU24)
            // SUP10ptt;0  (hasta SUP13)
            // SUP10item;0 (hasta SUP13)
            // Dato-1;300;JOSÉ LUIS (hasta Dato-37)
            $table->datetime('fecha_revision_grilla');
            $table->date('supervisor_qf');
            $table->integer('diferencia_unid_absoluta');
            $table->integer('ptt_inventariadas');
            $table->integer('ptt_rev_qf');
            $table->integer('ptt_rev_apoyo1');
            $table->integer('ptt_rev_apoyo2');
            $table->integer('ptt_rev_supervisor_fcv');
            $table->integer('total_items_inventariados');
            $table->integer('items_auditados');
            $table->integer('items_corregidos_auditoria');
            $table->integer('items_rev_qf');
            $table->integer('items_rev_apoyo1');
            $table->integer('items_rev_apoyo2');
            $table->integer('unid_neto_corregido_auditoria');
            $table->integer('unid_absoluto_corregido_auditoria');

            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('actas_inventarios_fcv');
    }
}
