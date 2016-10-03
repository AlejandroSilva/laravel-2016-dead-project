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
            $table->integer('presupuesto')->nullable();
            $table->integer('efectiva')->nullable();
            $table->time('hora_llegada')->nullable();
            $table->string('administrador', 60)->nullable();
            $table->integer('porcentaje')->nullable();
            $table->dateTime('captura_uno')->nullable();
            $table->dateTime('emision_cero')->nullable();
            $table->dateTime('emision_variance')->nullable();
            $table->dateTime('inicio_sumary')->nullable();
            $table->dateTime('fin_captura')->nullable();
            $table->integer('unidades')->nullable();
            $table->integer('teorico_unidades')->nullable();
            $table->date('fecha_toma')->nullable();
            $table->integer('cod_local')->nullable();
            $table->string('nombre_empresa', 10)->nullable();
            $table->string('usuario', 60)->nullable();
            $table->integer('nota1')->nullable();
            $table->integer('nota2')->nullable();
            $table->integer('nota3')->nullable();
            $table->integer('aud1')->nullable();
            $table->integer('aud2')->nullable();
            $table->integer('aud3')->nullable();
            $table->integer('aud4')->nullable();
            $table->integer('aud5')->nullable();
            $table->integer('aud6')->nullable();
            $table->integer('aju1')->nullable();
            $table->integer('aju2')->nullable();
            $table->integer('aju3')->nullable();
            $table->float('aju4')->nullable();;  // 2 decimales de precisio->nullable()n
            $table->dateTime('tot1')->nullable();
            $table->integer('tot2')->nullable();
            $table->integer('tot3')->nullable();
            $table->integer('tot4')->nullable();
            $table->integer('check1')->nullable();
            $table->integer('check2')->nullable();
            $table->integer('check3')->nullable();
            $table->integer('check4')->nullable();
            // version "nueva"
            // RU01item;1122 (hasta RU24)
            // RU01uni;2554 (hasta RU24)
            // SUP10ptt;0  (hasta SUP13)
            // SUP10item;0 (hasta SUP13)
            // Dato-1;300;JOSÉ LUIS (hasta Dato-37)
            $table->datetime('fecha_revision_grilla')->nullable();
            $table->date('supervisor_qf')->nullable();
            $table->integer('diferencia_unid_absoluta')->nullable();
            $table->integer('ptt_inventariadas')->nullable();
            $table->integer('ptt_rev_qf')->nullable();
            $table->integer('ptt_rev_apoyo1')->nullable();
            $table->integer('ptt_rev_apoyo2')->nullable();
            $table->integer('ptt_rev_supervisor_fcv')->nullable();
            $table->integer('total_items_inventariados')->nullable();
            $table->integer('items_auditados')->nullable();
            $table->integer('items_corregidos_auditoria')->nullable();
            $table->integer('items_rev_qf')->nullable();
            $table->integer('items_rev_apoyo1')->nullable();
            $table->integer('items_rev_apoyo2')->nullable();
            $table->integer('unid_neto_corregido_auditoria')->nullable();
            $table->integer('unid_absoluto_corregido_auditoria')->nullable();

            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('actas_inventarios_fcv');
    }
}
