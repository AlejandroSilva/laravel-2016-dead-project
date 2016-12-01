<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActaFieldsToArchivosRespuestaWOM extends Migration {

    public function up() {
        Schema::table('archivos_respuesta_wom', function (Blueprint $table) {

            // preguntas
            $table->text('tieneBuenaDisposicionYExplica', 5)->nullable()->after('runLiderSei');
            $table->text('escaneoCajasAbiertasSIMUnoAUno', 5)->nullable()->after('runLiderSei');
            $table->text('seRealizoSegundoConteATelefonos', 5)->nullable()->after('runLiderSei');
            $table->text('presentaOrdenadoSusProductos', 5)->nullable()->after('runLiderSei');
            $table->text('identificoCajasSIMAbiertas', 5)->nullable()->after('runLiderSei');
            $table->text('identificoEstadoDeTelefonos', 5)->nullable()->after('runLiderSei');
            $table->text('identificoTodosLosSectores', 5)->nullable()->after('runLiderSei');
            $table->tinyInteger('evaluacionAServicioSEI')->nullable()->after('runLiderSei');
            $table->time('tiempoTranscurrido')->nullable()->after('runLiderSei');
            $table->mediumInteger('porcentajeErrorSei')->nullable()->after('runLiderSei');
            $table->mediumInteger('unidadesErrorSei')->nullable()->after('runLiderSei');
            $table->mediumInteger('pttTotal')->nullable()->after('runLiderSei');
            $table->mediumInteger('unidadesServTecnico')->nullable()->after('runLiderSei');
            $table->mediumInteger('unidadesPrestamo')->nullable()->after('runLiderSei');
            $table->mediumInteger('unidadesUsado')->nullable()->after('runLiderSei');
            $table->mediumInteger('unidadesNuevo')->nullable()->after('runLiderSei');
            $table->mediumInteger('unidadesContadas')->nullable()->after('runLiderSei');
            $table->string('organizacion', 10)->nullable()->after('resultado');
        });
    }
    public function down() {
        Schema::table('archivos_respuesta_wom', function (Blueprint $table) {
            $table->dropColumn('tieneBuenaDisposicionYExplica');
            $table->dropColumn('escaneoCajasAbiertasSIMUnoAUno');
            $table->dropColumn('seRealizoSegundoConteATelefonos');
            $table->dropColumn('presentaOrdenadoSusProductos');
            $table->dropColumn('identificoCajasSIMAbiertas');
            $table->dropColumn('identificoEstadoDeTelefonos');
            $table->dropColumn('identificoTodosLosSectores');
            $table->dropColumn('evaluacionAServicioSEI');
            $table->dropColumn('tiempoTranscurrido');
            $table->dropColumn('porcentajeErrorSei');
            $table->dropColumn('unidadesErrorSei');
            $table->dropColumn('pttTotal');
            $table->dropColumn('unidadesServTecnico');
            $table->dropColumn('unidadesPrestamo');
            $table->dropColumn('unidadesUsado');
            $table->dropColumn('unidadesNuevo');
            $table->dropColumn('unidadesContadas');
            $table->dropColumn('organizacion');
        });
    }
}
