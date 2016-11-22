<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCamposToArchivosRespuestaWOM extends Migration {

    public function up() {
        Schema::table('archivos_respuesta_wom', function (Blueprint $table) {
            $table->string('runLiderSei', 20)->after('resultado');
            $table->string('liderSei', 60)->after('resultado');
            $table->string('runLiderWom', 20)->after('resultado');
            $table->string('liderWom', 60)->after('resultado');

            $table->text('nombreOriginalConteo2')->after('nombreOriginal');
            $table->text('nombreArchivoConteo2')->after('nombreOriginal');
        });
    }
    public function down() {
        Schema::table('archivos_respuesta_wom', function (Blueprint $table) {
            $table->dropColumn('liderWom');
            $table->dropColumn('runLiderWom');
            $table->dropColumn('liderSei');
            $table->dropColumn('runLiderSei');
        });
    }
}
